import { CommonModule } from '@angular/common';
import { ChangeDetectorRef, Component, Input, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, FormsModule, ReactiveFormsModule, Validators } from '@angular/forms';
import { ActivatedRoute } from '@angular/router';
import { MessagesService } from '../../../services/messages/messages';
import { OrdenesService } from '../../../services/api/ordenes/ordenes';
import { ModalService } from '../../../services/modal/modal';

@Component({
  selector: 'app-chat-ordenes',
  imports: [CommonModule, ReactiveFormsModule, FormsModule],
  standalone: true,
  templateUrl: './chat-orders.html',
  styleUrl: './chat-orders.css',
})
export class ChatOrdenesComponent implements OnInit {
  @Input() pkOrden: any = null;

  protected datosTabla: any = []; 
  protected formChat!: FormGroup;
  protected formOrden!: FormGroup; 
  protected formSolucion!: FormGroup;
  protected mensajesOrden: any[] = [];
  protected images: any[] = [];    
  protected listaMonedas: any[] = [];

  protected listaAreas:             any[] = [];
  protected listaMaquinas:          any[] = [];
  protected listaDepartamentos:     any[] = [];
  protected listaEmpleados:         any[] = [];
  protected listatipoMantenimiento: any[] = [];
  protected listatipoOrden:         any[] = [];
  protected listaprioridadOrden:    any[] = [];
  protected listacatalogogoMaquina: any[] = [];

  protected cargandoMensajes: boolean = false;
  protected enviandoMensajeState: boolean = false;

  constructor(
    private messages: MessagesService,
    private ordenes: OrdenesService,
    private ch: ChangeDetectorRef,
    private fb: FormBuilder,
    private route: ActivatedRoute,
    private modal: ModalService
  ) {}

  get esOrdenFinalizada(): boolean {
    return Number(this.datosTabla[0]?.id_status_order) === 3;
  }

  async ngOnInit(): Promise<void> {
    this.messages.mensajeEsperar();
    try {
      this.crearFormChat();
      this.crearFormOrdenBase();
      this.crearFormSolucionBase();
      await this.obtenerRecursosRegistroOrden();

      if (this.pkOrden == null) {
        const paramKeys = this.route.snapshot.paramMap.keys;
        for (const key of paramKeys) {
          const val = this.route.snapshot.paramMap.get(key);
          if (val && !isNaN(Number(val))) {
            this.pkOrden = Number(val);
            break;
          }
        }
      }

      if (this.pkOrden != null) {
        await Promise.all([
          this.obtenerDetalleOrden(this.pkOrden),
          this.obtenerMensajesOrden(this.pkOrden)
        ]);
      } else {
        console.warn('Advertencia: pkOrden es nulo, no se puede consultar la orden.');
      }
    } catch (error) {
      console.error('Error en ngOnInit:', error);
      this.messages.mensajeGenerico('error', 'error');
    } finally {
      this.messages.cerrarMensajes();
    }
  }

  /**
   * Determina si se debe mostrar una etiqueta separadora de fecha entre dos mensajes
   */
  protected mostrarSeparadorFecha(index: number): boolean {
    if (index === 0) return true;
    const fechaActual = this.obtenerFechaSolo(this.mensajesOrden[index]?.created_at || this.mensajesOrden[index]?.send_date);
    const fechaAnterior = this.obtenerFechaSolo(this.mensajesOrden[index - 1]?.created_at || this.mensajesOrden[index - 1]?.send_date);
    return fechaActual !== fechaAnterior;
  }

  /**
   * Determina si se debe mostrar el nombre del remitente en el mensaje actual
   */
  protected mostrarRemitente(index: number): boolean {
    if (index === 0) return true;
    if (this.mostrarSeparadorFecha(index)) return true;

    const actual = this.mensajesOrden[index];
    const anterior = this.mensajesOrden[index - 1];

    const remitenteActual = actual.remitente_nombre || actual.remitente || actual.es_admin;
    const remitenteAnterior = anterior.remitente_nombre || anterior.remitente || anterior.es_admin;

    return remitenteActual !== remitenteAnterior;
  }

  /**
   * Extrae solo YYYY-MM-DD
   */
  private obtenerFechaSolo(fechaStr: string): string {
    if (!fechaStr) return '';
    return fechaStr.split(' ')[0] || fechaStr.split('T')[0] || '';
  }

  /**
   * Extrae la hora en formato 12h o 24h (ej. 22:30)
   */
  protected obtenerHora(fechaStr: string): string {
    if (!fechaStr) return '';
    const partes = fechaStr.split(' ');
    if (partes.length > 1) {
      const horaMin = partes[1].split(':');
      return `${horaMin[0]}:${horaMin[1]}`;
    }
    return fechaStr;
  }

  private crearFormChat(): void {
    this.formChat = this.fb.group({
      subject: ['', Validators.required],
      content: ['', Validators.required]
    });
  }

  private crearFormOrdenBase(): void {
    this.formOrden = this.fb.group({
      id_area: [null],
      id_type_orders: [null],
      id_cat_machines: [null],
      id_machines: [null],
      id_priority: [null],
      id_employee: [null],
      id_departaments: [null],
      id_type_maintenances: [null],
      problem_description: ['']
    });
  }

  private crearFormSolucionBase(): void {
    this.formSolucion = this.fb.group({
      id_order: [null, Validators.required],
      id_status_order: [3, Validators.required],
      business_mail: [''],
      id_users: [1],
      id_coin: [null, Validators.required],
      order_cost: [null, Validators.required],
      resolution: ['', Validators.required],
      spare_parts: [''],
      materials: ['']
    });
  }

  public async obtenerRecursosRegistroOrden(): Promise<void> {
    const respuesta: any = await this.ordenes.obtenerRecursosRegistroOrden().toPromise();
    const recursos = respuesta?.recursos || respuesta;

    this.listaAreas             = Array.isArray(recursos?.listaareas) ? recursos.listaareas : [];
    this.listaMaquinas          = Array.isArray(recursos?.listamaquinas) ? recursos.listamaquinas : [];
    this.listaDepartamentos     = Array.isArray(recursos?.listadepartamentos) ? recursos.listadepartamentos : [];
    this.listaEmpleados         = Array.isArray(recursos?.listaempleados) ? recursos.listaempleados : [];
    this.listatipoMantenimiento = Array.isArray(recursos?.listatipomantenimiento) ? recursos.listatipomantenimiento : [];
    this.listatipoOrden         = Array.isArray(recursos?.listatipoorden) ? recursos.listatipoorden : [];
    this.listaprioridadOrden    = Array.isArray(recursos?.listaprioridadorden) ? recursos.listaprioridadorden : [];
    this.listacatalogogoMaquina = Array.isArray(recursos?.listacatalogomaquina) ? recursos.listacatalogomaquina : [];
    
    this.ch.detectChanges();
  }

  public async obtenerMensajesOrden(idOrder: number): Promise<void> {
    this.cargandoMensajes = true;
    this.ch.detectChanges();

    try {
      const respuesta: any = await this.ordenes.obtenerMensajesOrden(idOrder).toPromise();
      this.mensajesOrden = respuesta?.mensajes || respuesta?.data || [];
    } catch (error) {
      console.error('Error al cargar mensajes:', error);
      this.messages.mensajeGenerico('Error al cargar el historial de mensajes', 'error');
    } finally {
      this.cargandoMensajes = false;
      this.ch.detectChanges();
    }
  }

  public async obtenerDetalleOrden(pkOrden: number): Promise<void> {
    try {
      const respuesta: any = await this.ordenes.obtenerDetalleOrden(pkOrden).toPromise();
      const orden = respuesta.orden || respuesta;
      const evidencias = respuesta.evidencias || [];

      setTimeout(() => {
        this.datosTabla = orden ? [orden] : [];
        this.images = evidencias;

        if (this.esOrdenFinalizada) {
          this.formChat.disable({ emitEvent: false });
        } else {
          this.formChat.enable({ emitEvent: false });
        }

        this.formOrden.patchValue({
          id_area: orden?.id_area || orden?.id_areas,
          id_type_orders: orden?.id_type_orders,
          id_cat_machines: orden?.id_cat_machines || orden?.id_cat_machine || orden?.id_catalogo_maquina,
          id_machines: orden?.id_machines,
          id_priority: orden?.id_priority,
          id_employee: orden?.id_employee,
          id_departaments: orden?.id_departaments,
          id_type_maintenances: orden?.id_type_maintenances,
          problem_description: orden?.problem_description
        }, { emitEvent: false });

        this.ch.detectChanges();
      }, 0);

      const idArea = orden?.id_area || orden?.id_areas;
      const idCatMaquina = orden?.id_cat_machines || orden?.id_cat_machine || orden?.id_catalogo_maquina;

      if (idArea && idCatMaquina) {
        try {
          const res: any = await this.ordenes.obtenerMaquinasPorAreaYCategoria(idArea, idCatMaquina).toPromise();
          const datos = res?.listamaquinas || res?.maquinas || res?.data || res;
          this.listaMaquinas = Array.isArray(datos) ? datos : [];
        } catch (error) {
          this.listaMaquinas = [];
        }
      }

    } catch (error) {
      console.error('Error al obtener detalle:', error);
      this.messages.mensajeGenerico('Ocurrió un error al consultar el detalle de la orden', 'error');
    }
  }

  protected async enviarMensaje(): Promise<void> {
    if (this.esOrdenFinalizada) {
      this.messages.mensajeGenerico('La orden está finalizada y no se pueden agregar más mensajes', 'info');
      return;
    }

    if (this.formChat.invalid) {
      this.messages.mensajeGenerico('El asunto y el contenido del mensaje son obligatorios', 'info', 'Campos requeridos');
      return;
    }

    this.messages.mensajeConfirmacionCustom(
      '¿Está seguro de enviar este mensaje?', 'question', 'Enviar mensaje'
    ).then(async res => {
      if (!res.isConfirmed) return;

      this.enviandoMensajeState = true;
      this.ch.detectChanges();

      try {
        const ordenActual = this.datosTabla[0] || {};
        const correoNegocio = ordenActual.busines_mail || ordenActual.correo_electronico || 'usuario_admin@grupointerconsult.com';

        const payload = {
          id_order: this.pkOrden,
          id_users: 1,
          busines_mail: correoNegocio,
          remitente_nombre: 'Administración', 
          subject: this.formChat.get('subject')?.value,
          content: this.formChat.get('content')?.value,
          es_admin: true 
        };

        const respuesta: any = await this.ordenes.enviarMensajeOrden(payload).toPromise();
        
        this.formChat.reset();
        await this.obtenerMensajesOrden(this.pkOrden);

        this.messages.mensajeGenerico(respuesta.mensaje || 'Mensaje enviado con éxito', 'success', respuesta.title || 'Éxito');
      } catch (error) {
        console.error('Error al enviar el mensaje:', error);
        this.messages.mensajeGenerico('Ocurrió un error al enviar el mensaje', 'error');
      } finally {
        this.enviandoMensajeState = false;
        this.ch.detectChanges();
      }
    });
  }

  protected cambiarEstatusOrden(orden: any): void {
    if (this.esOrdenFinalizada) {
      this.messages.mensajeGenerico('La orden está finalizada y no se puede modificar el estatus', 'info');
      return;
    }

    const nuevoEstatus = Number(orden.id_status_order);

    if (nuevoEstatus === 2) {
      this.actualizarEstatusYEnviarCorreo(orden, nuevoEstatus);
    } else if (nuevoEstatus === 3) {
      this.abrirModalSolucion(orden);
    } else {
      this.actualizarEstatusBD(orden.id_order, nuevoEstatus);
    }
  }

  protected actualizarEstatusBD(idOrder: number, idStatus: number): void {
    this.messages.mensajeEsperar();
    this.ordenes.cambiarStatusYSolucionarOrden({ id_order: idOrder, id_status_order: idStatus }).subscribe({
      next: (res: any) => {
        this.messages.mensajeGenerico('Estatus actualizado correctamente', 'success');
        this.obtenerDetalleOrden(this.pkOrden);
      },
      error: (err) => {
        console.error(err);
        this.messages.mensajeGenerico('Error al actualizar el estatus', 'error');
      }
    });
  }

  protected actualizarEstatusYEnviarCorreo(orden: any, nuevoEstatus: number): void {
    this.messages.mensajeEsperar();
    const payload = {
      id_order: orden.id_order,
      id_status_order: nuevoEstatus
    };

    this.ordenes.cambiarStatusYSolucionarOrden(payload).subscribe({
      next: (res: any) => {
        this.messages.mensajeGenerico('Estatus actualizado y notificación enviada', 'success');
        this.obtenerDetalleOrden(this.pkOrden);
      },
      error: (err) => {
        console.error(err);
        this.messages.mensajeGenerico('Error al actualizar el estatus', 'error');
      }
    });
  }

  protected abrirModalSolucion(orden: any): void {
    this.formSolucion.patchValue({
      id_order: orden.id_order,
      id_status_order: 3,
      business_mail: orden.busines_mail,
      id_users: 1 
    });

    this.ordenes.obtenerListaMonedas().subscribe({
      next: (res: any) => {
        this.listaMonedas = res.monedas || res.data || res;
      },
      error: (err) => {
        console.error('Error al cargar monedas:', err);
      }
    });

    const modalElement = document.getElementById('modalSolucionOrden');
    const modal = new (window as any).bootstrap.Modal(modalElement);
    modal.show();
  }

  protected guardarSolucion(): void {
    if (this.formSolucion.invalid) {
      this.messages.mensajeGenerico('Complete los campos requeridos para la solución', 'info');
      return;
    }

    this.messages.mensajeEsperar();
    this.ordenes.cambiarStatusYSolucionarOrden(this.formSolucion.value).subscribe({
      next: (res: any) => {
        this.messages.mensajeGenerico('Orden finalizada y solución registrada con éxito', 'success');
        
        const modalElement = document.getElementById('modalSolucionOrden');
        const modal = (window as any).bootstrap.Modal.getInstance(modalElement);
        if (modal) modal.hide();

        this.obtenerDetalleOrden(this.pkOrden);
      },
      error: (err) => {
        console.error(err);
        this.messages.mensajeGenerico('Error al registrar la solución', 'error');
      }
    });
  }
}