import { CommonModule } from '@angular/common';
import { ChangeDetectorRef, Component, OnInit, OnDestroy } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { ModalService } from '../../../services/modal/modal';
import { MessagesService } from '../../../services/messages/messages';
import { OrdenesService } from '../../../services/api/ordenes/ordenes';
import { AreasService } from '../../../services/api/areas/areas';
import { UsuariosService } from '../../../services/api/usuarios/usuarios';
import { RegistrarOrden } from '../registrar-orden/registrar-orden';
import { Router } from '@angular/router';

@Component({
  selector: 'app-consulta-ordenes',
  imports: [CommonModule, FormsModule],
  standalone: true,
  templateUrl: './consulta-ordenes.html',
  styleUrl: './consulta-ordenes.css',
})
export class ConsultaOrdenes implements OnInit, OnDestroy {
  protected datosTabla: any = [];
  private intervalo: any;

  protected listaAreas: any[] = [];
  protected listaStatus: any[] = [];

  protected id_area: any = '';
  protected id_status: any = '';

  protected puedeEditar: boolean = false;
  protected puedeCancelar: boolean = false;
  protected puedeEliminar: boolean = false;

  // Propiedades para buscador y paginación
  protected textoBusqueda: string = '';
  protected paginaActual: number = 1;
  protected itemsPorPagina: number = 5;

  constructor(
    private modal: ModalService,
    private messages: MessagesService,
    private ch: ChangeDetectorRef,
    private areas: AreasService,
    private ordenes: OrdenesService,
    private usuarios: UsuariosService,
    private router: Router,
  ) {}

  async ngOnInit(): Promise<any> {
    this.messages.mensajeEsperar();

    // Verificamos los permisos mediante la API antes de renderizar la tabla y acciones
    await this.verificarPermisosUsuario();

    await this.obtenerListaAreas();
    await this.obtenerStatusOrdenes(); 

    this.messages.cerrarMensajes();
    
    this.repetitiveInstruction(); 
  }

  ngOnDestroy(): void {
    if (this.intervalo) {
      clearInterval(this.intervalo);
    }
  }

  private async verificarPermisosUsuario(): Promise<void> {
    try {
      const respuesta: any = await this.usuarios.obtenerPermisosUsuarioActual().toPromise();
      const permisos: string[] = respuesta?.permisos || [];
      const rolUsuario = Number(respuesta?.id_rol_users || 1);

      const esAdminOTecnico = (rolUsuario === 2 || rolUsuario === 3);
      this.puedeEditar = esAdminOTecnico || permisos.includes('editar_orden');
      this.puedeCancelar = esAdminOTecnico || permisos.includes('cancelar_orden');
      this.puedeEliminar = esAdminOTecnico || permisos.includes('eliminar_orden');
    } catch (error) {
      this.puedeEditar = false;
      this.puedeCancelar = false;
      this.puedeEliminar = false;
    }
  }

  private repetitiveInstruction(): void {
    this.intervalo = setInterval(() => {
      if (this.id_area == '' || this.id_status == '') {
        this.datosTabla = [];
        return;
      }

      if (navigator.onLine) this.ObtenerListaGeneralOrdenes();
    }, 10000);
  }

  private async obtenerStatusOrdenes(): Promise<any> {
    return this.ordenes.obtenerStatusOrdenes().toPromise().then(
      respuesta => {
        this.listaStatus = respuesta.ordenes || [];
        this.ch.markForCheck();
      }
    );
  }

  private async obtenerListaAreas(): Promise<any> {
    return this.areas.obtenerListaAreas().toPromise().then(
      respuesta => {
        this.listaAreas = respuesta.areas || [];
        this.ch.markForCheck();
      }
    );
  }

  protected async busquedaFiltros(): Promise<any> {
    clearInterval(this.intervalo);

    if (this.id_area == '' || this.id_status == '') return;

    this.messages.mensajeEsperar();
    await this.ObtenerListaGeneralOrdenes().then(() => {
      this.messages.cerrarMensajes();
      this.repetitiveInstruction();
    });
  }

  protected async ObtenerListaGeneralOrdenes(): Promise<any> {
    const data = {
      pkArea: this.id_area,
      pkStatus: this.id_status
    };

    return this.ordenes.ObtenerListaGeneralOrdenes(data).toPromise()
      .then((respuesta: any) => {
        this.datosTabla = respuesta?.ordenes ?? [];
        this.ch.markForCheck();
      })
      .catch(error => {
        console.error('Error al obtener órdenes:', error);
      });
  }

  protected cancelarOrden(id_order: number): void {
    if (!this.puedeCancelar) {
      this.messages.mensajeGenerico(
        'No tienes el permiso del administrador, contacta al administrador para que te lo autorice.',
        'error',
        'Acceso denegado'
      );
      return;
    }

    this.messages.mensajeConfirmacionCustom(
      '¿Está seguro de continuar con la cancelación del orden?',
      'question', 'Cancelar orden'
    ).then(res => {
      if (!res.isConfirmed) return;

      this.messages.mensajeEsperar();

      this.ordenes.cancelarOrden(id_order).subscribe({
        next: (respuesta: any) => {
          this.messages.mensajeGenerico(
            respuesta.mensaje, 'success', 'Cancelar orden'
          );
          this.ObtenerListaGeneralOrdenes();
        },
        error: (error) => {
          this.messages.mensajeGenerico(
            error?.error?.mensaje || 'Ocurrió un error al cancelar el orden.',
            'error'
          );
        }
      });
    });
  }

  protected eliminarOrden(id_order: number): void {
    if (!this.puedeEliminar) {
      this.messages.mensajeGenerico(
        'No tienes permisos para eliminar órdenes. Contacta al administrador.',
        'error',
        'Acceso denegado'
      );
      return;
    }

    this.messages.mensajeConfirmacionCustom(
      '¿Está seguro de que desea eliminar permanentemente esta orden? Esta acción no se puede deshacer.',
      'warning',
      'Eliminar orden'
    ).then(res => {
      if (!res.isConfirmed) return;

      this.messages.mensajeEsperar();

      this.ordenes.eliminarOrden(id_order).subscribe({
        next: (respuesta: any) => {
          this.messages.mensajeGenerico(
            respuesta.mensaje || 'Orden eliminada con éxito',
            'success',
            'Eliminado'
          );
          this.ObtenerListaGeneralOrdenes();
        },
        error: (error) => {
          this.messages.mensajeGenerico(
            error?.error?.mensaje || 'Ocurrió un error al eliminar la orden.',
            'error'
          );
        }
      });
    });
  }

  getStatusIcon(status: string): string {
    switch (status?.toLowerCase()) {
      case 'pendiente':
      case 'abierto':
        return 'bi-hourglass-split text-warning';
      case 'en proceso':
        return 'bi-gear-fill text-primary';
      case 'terminado':
      case 'finalizado':
        return 'bi-check-circle-fill text-success';
      case 'cancelado':
        return 'bi-x-circle-fill text-danger';
      default:
        return 'bi-question-circle text-muted';
    }
  }

  getStatusNombre(id: any): string {
    const s = this.listaStatus?.find(x => x.id_status_order == id);
    return s ? s.status : '';
  }

  getAreaNombre(id: any): string {
    const a = this.listaAreas?.find(x => x.id_area == id);
    return a ? a.area : '';
  }

  public abrirModalRegistrarOrdenes(pkOrden: number | null = null): void {
    if (!this.puedeEditar) {
      this.messages.mensajeGenerico(
        'No tienes el permiso del administrador, contacta al administrador para que te lo autorice.',
        'error',
        'Acceso denegado'
      );
      return;
    }

    if (pkOrden !== null && (!pkOrden || String(pkOrden) === 'undefined')) {
      console.warn('Se intentó abrir el modal con un ID de orden inválido.');
      return;
    }

    const data: any = {
      pkOrden: pkOrden
    };

    this.modal.abrirModalConComponente(RegistrarOrden, data, 'lg-modal');
  }

  public abrirChatOrden(pkOrden: number): void {
    if (!pkOrden || String(pkOrden) === 'undefined') {
      console.warn('Se intentó abrir el chat con un ID de orden inválido.');
      return;
    }

    this.router.navigate(['/chat-ordenes', pkOrden]);
  }

  get ordenesFiltradas() {
    if (!this.textoBusqueda || this.textoBusqueda.trim() === '') {
      return this.datosTabla;
    }
    const query = this.textoBusqueda.toLowerCase();
    return this.datosTabla.filter((orden: any) => 
      (orden.folio && orden.folio.toLowerCase().includes(query)) ||
      (orden.machines && orden.machines.toLowerCase().includes(query)) ||
      (orden.Departament_ES && orden.Departament_ES.toLowerCase().includes(query)) ||
      (orden.area && orden.area.toLowerCase().includes(query)) ||
      (orden.Name && orden.Name.toLowerCase().includes(query)) ||
      (orden.type_maintenances && orden.type_maintenances.toLowerCase().includes(query)) ||
      (orden.type_orders && orden.type_orders.toLowerCase().includes(query)) ||
      (orden.priority && orden.priority.toLowerCase().includes(query)) ||
      (orden.status && orden.status.toLowerCase().includes(query))
    );
  }

  get ordenesPaginadas() {
    const inicio = (this.paginaActual - 1) * this.itemsPorPagina;
    return this.ordenesFiltradas.slice(inicio, inicio + this.itemsPorPagina);
  }

  get totalPaginas(): number {
    return Math.ceil(this.ordenesFiltradas.length / this.itemsPorPagina) || 1;
  }

  protected cambiarPagina(pagina: number): void {
    if (pagina < 1 || pagina > this.totalPaginas) return;
    this.paginaActual = pagina;
    this.ch.markForCheck();
  }
}