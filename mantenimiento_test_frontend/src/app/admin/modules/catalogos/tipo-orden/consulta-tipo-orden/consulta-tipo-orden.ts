import { CommonModule } from '@angular/common';
import { ChangeDetectorRef, Component, HostListener, OnInit, OnDestroy } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { ModalService } from '../../../../services/modal/modal';
import { MessagesService } from '../../../../services/messages/messages';
import { TipoOrdenService } from '../../../../services/api/tipoOrden/tipo-orden';
import { RegistrarTipoOrden } from '../registrar-tipo-orden/registrar-tipo-orden';

@Component({
  selector: 'app-consulta-tipo-orden',
  imports: [CommonModule, FormsModule],
  standalone: true,
  templateUrl: './consulta-tipo-orden.html',
  styleUrl: './consulta-tipo-orden.css',
})
export class ConsultaTipoOrden implements OnInit, OnDestroy {
  protected datosTabla: any = [];
  protected datosFiltrados: any = [];
  protected datosPaginados: any = [];

  protected textoBusqueda: string = '';
  protected filtroEstado: string = 'todos';
  private intervalo: any;

  // Paginación
  public paginaActual: number = 1;
  public elementosPorPagina: number = 5;
  public totalPaginas: number = 1;
  public paginas: number[] = [];

  constructor(
    private modal: ModalService,
    private ch: ChangeDetectorRef,
    private messages: MessagesService,
    private tipoOrdenes: TipoOrdenService
  ) { }

  async ngOnInit(): Promise<any> {
    this.messages.mensajeEsperar();
    await this.obtenerListaTipoOrden();
    this.repetitiveInstruction();
    this.messages.cerrarMensajes();
  }

  @HostListener('window:keydown', ['$event'])
  handleKeyboardEvent(event: KeyboardEvent) {
    if ((event.ctrlKey || event.metaKey) && event.key.toLowerCase() === 'k') {
      event.preventDefault();
      const inputSearch = document.getElementById('inputBusquedaGlobal') as HTMLInputElement;
      if (inputSearch) inputSearch.focus();
    }
  }

  private repetitiveInstruction(): void {
    this.intervalo = setInterval(() => {
      this.obtenerListaTipoOrden(false);
    }, 10000);
  }

  public async obtenerListaTipoOrden(mostrarLoader: boolean = true): Promise<any> {
    return this.tipoOrdenes.obtenerListaTipoOrden().toPromise().then(
      respuesta => {
        this.datosTabla = respuesta.tipoOrdenes || [];
        this.aplicarFiltros();
        this.ch.markForCheck();
      },
      error => {
        if (mostrarLoader) this.messages.mensajeGenerico('Error al obtener información', 'error');
      }
    );
  }

  public setFiltroEstado(nuevoEstado: string): void {
    this.filtroEstado = nuevoEstado;
    this.aplicarFiltros();
  }

  public aplicarFiltros(): void {
    let resultado = this.datosTabla;

    if (this.textoBusqueda.trim() !== '') {
      const query = this.textoBusqueda.toLowerCase();
      resultado = resultado.filter((item: any) => 
        item.type_orders?.toLowerCase().includes(query) ||
        item.color?.toLowerCase().includes(query)
      );
    }

    if (this.filtroEstado === 'activos') {
      resultado = resultado.filter((item: any) => 
        item.activo === true || item.activo === 1 || String(item.estado).toLowerCase() === 'activo'
      );
    } else if (this.filtroEstado === 'inactivos') {
      resultado = resultado.filter((item: any) => 
        item.activo === false || item.activo === 0 || String(item.estado).toLowerCase() === 'inactivo'
      );
    }

    this.datosFiltrados = resultado;
    this.paginaActual = 1;
    this.actualizarPaginacion();
  }

  public actualizarPaginacion(): void {
    this.totalPaginas = Math.ceil(this.datosFiltrados.length / this.elementosPorPagina) || 1;
    this.paginas = Array.from({ length: this.totalPaginas }, (_, i) => i + 1);
    
    const inicio = (this.paginaActual - 1) * this.elementosPorPagina;
    const fin = inicio + this.elementosPorPagina;
    this.datosPaginados = this.datosFiltrados.slice(inicio, fin);
  }

  public cambiarPagina(pagina: number): void {
    if (pagina >= 1 && pagina <= this.totalPaginas && pagina !== this.paginaActual) {
      this.messages.mensajeEsperar();
      this.paginaActual = pagina;
      this.actualizarPaginacion();
      
      setTimeout(() => {
        this.messages.cerrarMensajes();
        this.ch.markForCheck();
      }, 250);
    }
  }

  public cambiarStatus(tipoOrden: any): void {
    const esActivo = tipoOrden.activo === true || tipoOrden.activo === 1 || String(tipoOrden.estado).toLowerCase() === 'activo';

    this.messages.mensajeConfirmacionCustom(
      `¿Está seguro de ${esActivo ? 'inactivar' : 'activar'} el tipo de orden?`,
      'question',
      `${esActivo ? 'Inactivar' : 'Activar'} tipo de orden`
    ).then(res => {
      if (!res.isConfirmed) return;

      this.messages.mensajeEsperar();

      this.tipoOrdenes.cambiarStatusTipoOrden(tipoOrden.id_type_orders).subscribe(
        respuesta => {
          this.obtenerListaTipoOrden(false).then(() => {
            this.messages.mensajeGenerico(respuesta.mensaje, 'success', respuesta.title);
          });
        }, error => {
          this.messages.mensajeGenerico('error', 'error');
        }
      );
    });
  }

  public abrirModalRegistrotipoMantenimiento(pktipoOrden: number): void {
    const data: any = {
      pktipoOrden: pktipoOrden
    };

    this.modal.abrirModalConComponente(RegistrarTipoOrden, data, 'md-modal');
  }

  ngOnDestroy(): void {
    clearInterval(this.intervalo);
  }
}