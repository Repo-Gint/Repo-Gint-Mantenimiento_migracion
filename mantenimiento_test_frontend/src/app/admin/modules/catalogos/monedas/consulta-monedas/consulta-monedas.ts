import { CommonModule } from '@angular/common';
import { ChangeDetectorRef, Component, HostListener, OnInit, OnDestroy } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { ModalService } from '../../../../services/modal/modal';
import { Monedaservice } from '../../../../services/api/Monedas/monedas';
import { MessagesService } from '../../../../services/messages/messages';
import { RegistrarMonedas } from '../registrar-monedas/registrar-monedas';

@Component({
  selector: 'app-consulta-monedas',
  imports: [CommonModule, FormsModule],
  standalone: true,
  templateUrl: './consulta-monedas.html',
  styleUrl: './consulta-monedas.css',
})
export class ConsultaMonedas implements OnInit, OnDestroy {
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
    private monedas: Monedaservice,
    private messages: MessagesService,
    private ch: ChangeDetectorRef
  ) { }

  async ngOnInit(): Promise<any> {
    this.messages.mensajeEsperar();
    await this.obtenerListaMonedas();
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
      this.obtenerListaMonedas(false);
    }, 10000);
  }

  public async obtenerListaMonedas(mostrarLoader: boolean = true): Promise<any> {
    return this.monedas.obtenerListaMonedas().toPromise().then(
      respuesta => {
        this.datosTabla = respuesta.monedas || [];
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
        item.coin?.toLowerCase().includes(query)
      );
    }

    if (this.filtroEstado === 'activos') {
      resultado = resultado.filter((item: any) =>
        item.active === 1 || item.active === true || String(item.estado).toLowerCase() === 'activo'
      );
    } else if (this.filtroEstado === 'inactivos') {
      resultado = resultado.filter((item: any) =>
        item.active === 0 || item.active === false || String(item.estado).toLowerCase() === 'inactivo'
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

  public cambiarStatus(moneda: any): void {
    const esActivo = moneda.active === 1 || moneda.active === true || String(moneda.estado).toLowerCase() === 'activo';

    this.messages.mensajeConfirmacionCustom(
      `¿Está seguro de ${esActivo ? 'inactivar' : 'activar'} el moneda?`,
      'question',
      `${esActivo ? 'Inactivar' : 'Activar'} moneda`
    ).then(res => {
      if (!res.isConfirmed) return;

      this.messages.mensajeEsperar();

      this.monedas.cambiarStatusMoneda(moneda.id_coins).subscribe(
        respuesta => {
          this.obtenerListaMonedas(false).then(() => {
            this.messages.mensajeGenerico(respuesta.mensaje, 'success', respuesta.title);
          });
        }, error => {
          this.messages.mensajeGenerico('error', 'error');
        }
      );
    });
  }

  public abrirModalRegistrarMoneda(pkMoneda: number): void {
    const data: any = {
      pkMoneda: pkMoneda
    };

    this.modal.abrirModalConComponente(RegistrarMonedas, data, 'md-modal');
  }

  ngOnDestroy(): void {
    clearInterval(this.intervalo);
  }
}