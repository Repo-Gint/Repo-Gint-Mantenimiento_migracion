import { MessagesService } from './../../../services/messages/messages';
import { CommonModule } from '@angular/common';
import { ChangeDetectorRef, Component, Input } from '@angular/core';
import { ModalService } from '../../../services/modal/modal';
import { OrdenesService } from '../../../services/api/ordenes/ordenes';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { DropdownComponent } from '../../../components/dropdown/dropdown';

@Component({
  selector: 'app-asignar-orden',
  imports: [CommonModule, ReactiveFormsModule, DropdownComponent],
  standalone: true,
  templateUrl: './asignar-orden.html',
  styleUrl: './asignar-orden.css',
})
export class AsignarOrden {

  @Input() pkOrden: any = null;
  @Input() folio: any = null;
  @Input() onAsignacionExitosa?: () => void; // Callback para notificar al padre

  protected formOrden!: FormGroup;
  protected listaUsuarios: any[] = [];
  protected yaTeniaUsuariosAsignados: boolean = false;

  constructor(
    private modal:    ModalService,
    private messages: MessagesService,
    private ch:       ChangeDetectorRef,
    private ordenes:  OrdenesService,
    private fb:       FormBuilder
  ) {}

  async ngOnInit(): Promise<void> {
    this.formOrden = this.fb.group({ idUsuario: [[], Validators.required] });
    this.messages.mensajeEsperar();
    await this.obtenerUsuariosAsignacion();
    this.messages.cerrarMensajes();
  }

  private async obtenerUsuariosAsignacion(): Promise<void> {
    return this.ordenes.obtenerUsuariosAsignacion(this.pkOrden).toPromise().then(
      respuesta => {
        this.listaUsuarios = respuesta.usuarios || [];
        this.yaTeniaUsuariosAsignados = this.listaUsuarios.some(u => u.checked === true);
        this.ch.detectChanges();
      }, 
      error => {
        this.messages.mensajeGenerico('Ocurrió un error al cargar los usuarios.', 'error');
      }
    );
  }

  get usuariosSeleccionados(): any[] {
    return this.listaUsuarios.filter(item => item.checked);
  }

  private getNombresSeleccionados(): string {
    return this.usuariosSeleccionados
      .map(u => u.label)
      .join(', ');
  }

  public asignarOrden(): void {
    const nombres = this.getNombresSeleccionados();
    const esReasignacion = this.yaTeniaUsuariosAsignados;

    const accion = esReasignacion ? 'reasignar' : 'asignar';
    const titulo = esReasignacion ? 'Reasignar orden' : 'Asignar orden';

    this.messages.mensajeConfirmacionCustom(
      `¿Estás seguro de ${accion} el orden ${this.folio} a ${nombres}?`,
      'question',
      titulo
    ).then(res => {

      if (!res.isConfirmed) return;

      this.messages.mensajeEsperar();

      const data = {
        pkOrden: this.pkOrden,
        idUsuario: this.usuariosSeleccionados.map(item => item.value)
      };

      this.ordenes.asignarOrden(data).toPromise().then(
        (respuesta: any) => {
          this.messages.mensajeGenerico(respuesta.mensaje || 'Proceso completado con éxito', 'success');
          
          // Ejecutamos el callback para recargar los usuarios en el modal principal al instante
          if (this.onAsignacionExitosa) {
            this.onAsignacionExitosa();
          }

          this.modal.cerrarModal();
        }, 
        error => {
          this.messages.mensajeGenerico(error?.error?.mensaje || 'Ocurrió un error', 'error');
        }
      );

    });
  }

  public cerrarModal(): void {
    this.messages.mensajeConfirmacionCustom(
      '¿Está seguro de cancelar la asignación?', 'question', 'Cancelar asignación'
    ).then(res => {
      if (!res.isConfirmed) return;
      this.modal.cerrarModal();
    });
  }

}