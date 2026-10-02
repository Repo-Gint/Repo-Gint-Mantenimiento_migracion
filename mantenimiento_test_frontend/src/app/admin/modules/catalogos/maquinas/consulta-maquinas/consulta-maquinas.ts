import { CommonModule } from '@angular/common';
import { ChangeDetectorRef, Component, OnDestroy, OnInit } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { ModalService } from '../../../../services/modal/modal';
import { MaquinasService } from '../../../../services/api/maquinas/maquinas';
import { MessagesService } from '../../../../services/messages/messages';
import { RegistrarMaquinas } from '../registrar-maquinas/registrar-maquinas';
import { DropdownComponent } from '../../../../components/dropdown/dropdown';

@Component({
  selector: 'app-consulta-maquinas',
  imports: [CommonModule, FormsModule, DropdownComponent],
  standalone: true,
  templateUrl: './consulta-maquinas.html',
  styleUrl: './consulta-maquinas.css',
})
export class ConsultaMaquinas implements OnInit, OnDestroy {
  protected datosTabla: any = [];
  
  // Variables de búsqueda y filtros
  protected textoBusqueda: string = '';
  protected filtroArea: string = '';
  protected filtroCatMaquina: string = '';
  protected filtroMarca: string = '';
  protected filtroEstatus: string = '';

  // Opciones mapeadas para los componentes Dropdown
  protected opcionesAreas: any[] = [];
  protected opcionesCatMaquinas: any[] = [];
  protected opcionesMarcas: any[] = [];
  protected opcionesEstatus: any[] = [
    { value: '', label: 'Estatus: Todos', checked: true },
    { value: '1', label: 'Activos', checked: false },
    { value: '0', label: 'Inactivos', checked: false }
  ];

  // Listas originales de respaldo
  protected listaAreas: any[] = [];
  protected listaCatMaquinas: any[] = [];
  protected listaMarcas: string[] = [];

  // Métricas reales
  protected metricas: any = {
    total_maquinaria: 0,
    activos: 0,
    demanda_kva: 0,
    peso_kg: 0
  };

  // Paginación exacta a 5 elementos por página
  protected paginaActual: number = 1;
  protected elementosPorPagina: number = 5;

  private intervalo: any;

  constructor (
    private modal: ModalService,
    private maquinas: MaquinasService,
    private messages: MessagesService,
    private ch: ChangeDetectorRef
  ) {}

  async ngOnInit(): Promise<any> {
    this.messages.mensajeEsperar();

    await this.obtenerListaMaquinas();
    this.repetitiveInstruction();

    this.messages.cerrarMensajes();
  }

  private repetitiveInstruction(): void {
    this.intervalo = setInterval(() => {
      this.obtenerListaMaquinas();
    }, 10000);
  }

  public async obtenerListaMaquinas(): Promise<any> {
    try {
      const respuesta: any = await this.maquinas.obtenerListaMaquinas().toPromise();
      
      this.datosTabla = (respuesta.maquinas || []).map((maq: any) => ({
        ...maq,
        activo: Number(maq.active) === 1
      }));

      if (respuesta.filtros_recursos) {
        this.listaAreas = respuesta.filtros_recursos.areas || [];
        this.opcionesAreas = [
          { value: '', label: 'Área: Todas', checked: !this.filtroArea },
          ...this.listaAreas.map((a: any) => ({
            value: String(a.id_area),
            label: a.area,
            checked: String(a.id_area) === String(this.filtroArea)
          }))
        ];

        this.listaCatMaquinas = respuesta.filtros_recursos.cat_machines || [];
        this.opcionesCatMaquinas = [
          { value: '', label: 'Cat. Máquina: Todas', checked: !this.filtroCatMaquina },
          ...this.listaCatMaquinas.map((c: any) => ({
            value: String(c.id_cat_machines),
            label: c.cat_machines,
            checked: String(c.id_cat_machines) === String(this.filtroCatMaquina)
          }))
        ];

        this.listaMarcas = respuesta.filtros_recursos.marcas || [];
        this.opcionesMarcas = [
          { value: '', label: 'Marca: Todas', checked: !this.filtroMarca },
          ...this.listaMarcas.map((m: string) => ({
            value: m,
            label: m,
            checked: m === this.filtroMarca
          }))
        ];
      }

      if (respuesta.metricas) {
        this.metricas = respuesta.metricas;
      }

      this.ch.markForCheck();
    } catch (error) {
      this.datosTabla = [];
    }
  }

  // --- MANEJADORES DE SELECCIÓN DE LOS DROPDOWNS ---
  protected onAreaSelectionChange(event: any): void {
    if (event.from === 'area') {
      const selected = event.selectedOptions[0];
      this.filtroArea = selected ? selected.value : '';
      this.aplicarFiltros();
    }
  }

  protected onCatSelectionChange(event: any): void {
    if (event.from === 'cat') {
      const selected = event.selectedOptions[0];
      this.filtroCatMaquina = selected ? selected.value : '';
      this.aplicarFiltros();
    }
  }

  protected onMarcaSelectionChange(event: any): void {
    if (event.from === 'marca') {
      const selected = event.selectedOptions[0];
      this.filtroMarca = selected ? selected.value : '';
      this.aplicarFiltros();
    }
  }

  protected onEstatusSelectionChange(event: any): void {
    if (event.from === 'estatus') {
      const selected = event.selectedOptions[0];
      this.filtroEstatus = selected ? selected.value : '';
      this.aplicarFiltros();
    }
  }

  protected aplicarFiltros(): void {
    this.paginaActual = 1;
    this.ch.markForCheck();
  }

  // --- FILTRO COMBINADO ---
  get maquinasFiltradas() {
    return this.datosTabla.filter((maq: any) => {
      const query = (this.textoBusqueda || '').toLowerCase().trim();
      const cumpleBusqueda = !query || 
        (maq.machines && maq.machines.toLowerCase().includes(query)) ||
        (maq.brand && maq.brand.toLowerCase().includes(query)) ||
        (maq.model && maq.model.toLowerCase().includes(query)) ||
        (maq.serial && maq.serial.toLowerCase().includes(query)) ||
        (maq.area && maq.area.toLowerCase().includes(query)) ||
        (maq.cat_machines && maq.cat_machines.toLowerCase().includes(query));

      const cumpleArea = !this.filtroArea || String(maq.id_area) === String(this.filtroArea);
      const cumpleCat = !this.filtroCatMaquina || String(maq.id_cat_machines) === String(this.filtroCatMaquina);
      const cumpleMarca = !this.filtroMarca || maq.brand === this.filtroMarca;
      const cumpleEstatus = this.filtroEstatus === '' || String(maq.active) === String(this.filtroEstatus);

      return cumpleBusqueda && cumpleArea && cumpleCat && cumpleMarca && cumpleEstatus;
    });
  }

  // --- PAGINACIÓN DE 5 EN 5 ---
  get maquinasPaginadas() {
    const inicio = (this.paginaActual - 1) * this.elementosPorPagina;
    const fin = inicio + this.elementosPorPagina;
    return this.maquinasFiltradas.slice(inicio, fin);
  }

  protected cambiarPagina(nuevaPagina: number): void {
    this.messages.mensajeEsperar();
    
    setTimeout(() => {
      this.paginaActual = nuevaPagina;
      this.messages.cerrarMensajes();
      this.ch.markForCheck();
    }, 350);
  }

  protected mathMin(a: number, b: number): number {
    return Math.min(a, b);
  }

  public cambiarStatus(maquina: any): void {
    this.messages.mensajeConfirmacionCustom(
      `¿Está seguro de ${maquina.activo ? 'inactivar' : 'activar'} la máquina?`,
      'question',
      `${maquina.activo ? 'Inactivar' : 'Activar'} máquina`
    ).then(res => {
      if (!res.isConfirmed) return; 

      this.messages.mensajeEsperar();

      this.maquinas.cambiarStatusMaquina(maquina.id_machines).subscribe(
        respuesta => {
          this.obtenerListaMaquinas().then(() => {
            this.messages.mensajeGenerico(respuesta.mensaje, 'success', respuesta.title);
          });
        }, error => {
          this.messages.mensajeGenerico('error', 'error');
        }
      );
    });
  }

  public abrirModalRegistroMaquina(pkMaquina: number): void {
    const data: any = {
      pkMaquina: pkMaquina
    };

    this.modal.abrirModalConComponente(RegistrarMaquinas, data, 'md-modal');
  }

  ngOnDestroy(): void {
    clearInterval(this.intervalo);
  }
}