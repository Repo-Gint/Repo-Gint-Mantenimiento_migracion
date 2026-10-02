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
  protected formSolucion!: FormGroup; // <--- Inicializado correctamente
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

  constructor(
    private messages: MessagesService,
    private ordenes: OrdenesService,
    private ch: ChangeDetectorRef,
    private fb: FormBuilder,
    private route: ActivatedRoute,
    private modal: ModalService
  ) {}

  async ngOnInit(): Promise<void> {
    this.messages.mensajeEsperar();
    try {
      this.crearFormChat();
      this.crearFormOrdenBase();
      this.crearFormSolucionBase(); // <--- Creamos la estructura del form de solución
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

    this.listaAreas              = Array.isArray(recursos?.listaareas) ? recursos.listaareas : [];
    this.listaMaquinas           = Array.isArray(recursos?.listamaquinas) ? recursos.listamaquinas : [];
    this.listaDepartamentos      = Array.isArray(recursos?.listadepartamentos) ? recursos.listadepartamentos : [];
    this.listaEmpleados          = Array.isArray(recursos?.listaempleados) ? recursos.listaempleados : [];
    this.listatipoMantenimiento  = Array.isArray(recursos?.listatipomantenimiento) ? recursos.listatipomantenimiento : [];
    this.listatipoOrden          = Array.isArray(recursos?.listatipoorden) ? recursos.listatipoorden : [];
    this.listaprioridadOrden     = Array.isArray(recursos?.listaprioridadorden) ? recursos.listaprioridadorden : [];
    this.listacatalogogoMaquina  = Array.isArray(recursos?.listacatalogomaquina) ? recursos.listacatalogomaquina : [];
    
    this.ch.detectChanges();
  }

  public async obtenerMensajesOrden(idOrder: number): Promise<void> {
    try {
      const respuesta: any = await this.ordenes.obtenerMensajesOrden(idOrder).toPromise();
      this.mensajesOrden = respuesta?.mensajes || respuesta?.data || [];
      this.ch.detectChanges();
    } catch (error) {
      console.error('Error al cargar mensajes:', error);
      this.messages.mensajeGenerico('Error al cargar el historial de mensajes', 'error');
    }
  }

  public async obtenerDetalleOrden(pkOrden: number): Promise<void> {
    try {
      const respuesta: any = await this.ordenes.obtenerDetalleOrden(pkOrden).toPromise();
      const orden = respuesta.orden || respuesta;
      const evidencias = respuesta.evidencias || [];
      this.datosTabla = orden ? [orden] : []; 

      const idArea = orden.id_area || orden.id_areas;
      const idCatMaquina = orden.id_cat_machines || orden.id_cat_machine || orden.id_catalogo_maquina;

      if (idArea && idCatMaquina) {
        try {
          const res: any = await this.ordenes.obtenerMaquinasPorAreaYCategoria(idArea, idCatMaquina).toPromise();
          const datos = res?.listamaquinas || res?.maquinas || res?.data || res;
          this.listaMaquinas = Array.isArray(datos) ? datos : [];
        } catch (error) {
          this.listaMaquinas = [];
        }
      }

      this.formOrden.patchValue({
        id_area: idArea,
        id_type_orders: orden.id_type_orders,
        id_cat_machines: idCatMaquina,
        id_machines: orden.id_machines,
        id_priority: orden.id_priority,
        id_employee: orden.id_employee,
        id_departaments: orden.id_departaments,
        id_type_maintenances: orden.id_type_maintenances,
        problem_description: orden.problem_description
      }, { emitEvent: false });

      this.images = evidencias;
      this.ch.detectChanges();
    } catch (error) {
      this.messages.mensajeGenerico('error', 'error');
    }
  }

  protected async enviarMensaje(): Promise<void> {
    if (this.formChat.invalid) {
      this.messages.mensajeGenerico('El asunto y el contenido del mensaje son obligatorios', 'info', 'Campos requeridos');
      return;
    }

    this.messages.mensajeConfirmacionCustom(
      '¿Está seguro de enviar este mensaje?', 'question', 'Enviar mensaje'
    ).then(async res => {
      if (!res.isConfirmed) return;

      this.messages.mensajeEsperar();

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
      }
    });
  }

  // Manejo de cambio de estatus desde el selector
  protected cambiarEstatusOrden(orden: any): void {
    const nuevoEstatus = Number(orden.id_status_order);

    if (nuevoEstatus === 2) {
      // Estatus "En proceso": Envía notificación automática de correo
      this.actualizarEstatusYEnviarCorreo(orden, nuevoEstatus);
    } else if (nuevoEstatus === 3) {
      // Estatus "Finalizado": Abre modal de solución
      this.abrirModalSolucion(orden);
    } else {
      // Estatus base (Abierto u otros)
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

    // Cargar monedas usando el servicio correspondiente o de catálogos
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
        
        // Cerrar modal de bootstrap
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