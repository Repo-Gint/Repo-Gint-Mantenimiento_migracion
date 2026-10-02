import { CommonModule } from '@angular/common';
import { ChangeDetectorRef, Component, OnDestroy, OnInit } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { ModalService } from '../../../../services/modal/modal';
import { MessagesService } from '../../../../services/messages/messages';
import { RegistrarCatmaquina } from '../registrar-catmaquina/registrar-catmaquina';
import { CatmaquinasService } from '../../../../services/api/catmaquinas/catmaquinas';

@Component({
  selector: 'app-consultar-catmaquinas',
  imports: [CommonModule, FormsModule],
  standalone: true,
  templateUrl: './consultar-catmaquinas.html',
  styleUrl: './consultar-catmaquinas.css',
})
export class ConsultarCatmaquinas implements OnInit, OnDestroy {
  protected datosTabla: any = [];

  // Variables de búsqueda y filtro
  protected textoBusqueda: string = '';
  protected filtroEstatus: string = '';

  // Paginación exacta a 5 elementos por página
  protected paginaActual: number = 1;
  protected elementosPorPagina: number = 5;

  private intervalo: any;

  constructor(
    private modal: ModalService,
    private messages: MessagesService,
    private ch: ChangeDetectorRef,
    private catmaquinas: CatmaquinasService
  ) {}

  async ngOnInit(): Promise<any> {
    this.messages.mensajeEsperar();

    await this.obtenerListaCatalogoMaquina();
    this.repetitiveInstruction();

    this.messages.cerrarMensajes();
  }

  private repetitiveInstruction(): void {
    this.intervalo = setInterval(() => {
      this.obtenerListaCatalogoMaquina(true); // Actualización silenciosa de fondo
    }, 10000);
  }

  public async obtenerListaCatalogoMaquina(silencioso: boolean = false): Promise<any> {
    if (!silencioso) {
      this.messages.mensajeEsperar();
    }

    try {
      const respuesta: any = await this.catmaquinas.obtenerListaCatalogoMaquina().toPromise();
      
      this.datosTabla = (respuesta.maquinaCatalogos || []).map((item: any) => ({
        ...item,
        activo: Number(item.active) === 1
      }));

      this.ch.markForCheck();
    } catch (error) {
      this.datosTabla = [];
    } finally {
      if (!silencioso) {
        this.messages.cerrarMensajes();
      }
    }
  }

  protected buscarPorBackend(): void {
    this.paginaActual = 1;
    this.ch.markForCheck();
  }

  // --- FILTRADO REACTIVO (Buscador + Estatus) ---
  get catMaquinasFiltradas() {
    return this.datosTabla.filter((item: any) => {
      const query = (this.textoBusqueda || '').toLowerCase().trim();
      const cumpleBusqueda = !query || 
        (item.cat_machines && item.cat_machines.toLowerCase().includes(query));

      const cumpleEstatus = this.filtroEstatus === '' || String(item.active) === String(this.filtroEstatus);

      return cumpleBusqueda && cumpleEstatus;
    });
  }

  // --- PAGINACIÓN DE 5 EN 5 ---
  get catMaquinasPaginadas() {
    const inicio = (this.paginaActual - 1) * this.elementosPorPagina;
    const fin = inicio + this.elementosPorPagina;
    return this.catMaquinasFiltradas.slice(inicio, fin);
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

  public cambiarStatusCatalogoMaquina(catmaquina: any): void {
    this.messages.mensajeConfirmacionCustom(
      `¿Está seguro de ${catmaquina.activo ? 'inactivar' : 'activar'} el catálogo de máquina?`,
      'question',
      `${catmaquina.activo ? 'Inactivar' : 'Activar'} catálogo`
    ).then(res => {
      if (!res.isConfirmed) return; 

      this.messages.mensajeEsperar();

      this.catmaquinas.cambiarStatusCatalogoMaquina(catmaquina.id_cat_machines).subscribe(
        respuesta => {
          this.obtenerListaCatalogoMaquina().then(() => {
            this.messages.mensajeGenerico(respuesta.mensaje, 'success', respuesta.title);
          });
        }, error => {
          this.messages.mensajeGenerico('error', 'error');
        }
      );
    });
  }

  public abrirModalRegistroCatMaquina(pkCatalogoMaquina: number): void {
    const data: any = {
      pkCatalogoMaquina: pkCatalogoMaquina
    };
 
    this.modal.abrirModalConComponente(RegistrarCatmaquina, data, 'md-modal');
  }

  ngOnDestroy(): void {
    clearInterval(this.intervalo);
  }
}