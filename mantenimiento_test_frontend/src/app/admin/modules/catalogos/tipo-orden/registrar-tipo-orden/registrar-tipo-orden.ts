import { CommonModule } from '@angular/common';
import { ChangeDetectorRef, Component, Input } from '@angular/core';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { ModalService } from '../../../../services/modal/modal';
import { MessagesService } from '../../../../services/messages/messages';
import { TipoOrdenService } from '../../../../services/api/tipoOrden/tipo-orden';

@Component({
  selector: 'app-registrar-tipo-orden',
  imports: [CommonModule, ReactiveFormsModule],
  standalone: true,
  templateUrl: './registrar-tipo-orden.html',
  styleUrl: './registrar-tipo-orden.css',
})
export class RegistrarTipoOrden {
  @Input() pktipoOrden: any = null;

  protected formtipoOrden!: FormGroup;

  constructor(
    private modal: ModalService,
    private ch: ChangeDetectorRef,
    private fb: FormBuilder,
    private messages: MessagesService,
    private tipoOrdenes: TipoOrdenService
  ) { }

  async ngOnInit(): Promise<any> {
    this.messages.mensajeEsperar();

    this.crearFormtipoOrdenes();

    if (this.pktipoOrden != null) await this.obtenerDetalleTipoOrden(this.pktipoOrden);

    this.messages.cerrarMensajes();
  }

  protected crearFormtipoOrdenes(): void {
    this.formtipoOrden = this.fb.group({
      type_orders: [null, [Validators.required, Validators.pattern('^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+$')]],
      color:       [null, [Validators.required, Validators.pattern('^#([A-Fa-f0-9]{6})$')]],
    });
  }

  public async obtenerDetalleTipoOrden(pktipoOrden: number): Promise<any> {

    return this.tipoOrdenes.obtenerDetalleTipoOrden(pktipoOrden).toPromise().then(
      respuesta => {
        const tipoOrden = respuesta.tipoOrdenes;
        this.formtipoOrden.get('type_orders')?.setValue(tipoOrden.type_orders);
        this.formtipoOrden.get('color')?.setValue(tipoOrden.color);
      });
  }

  protected registrarTipoOrden(): void {
    
    if (this.formtipoOrden.invalid) {
      this.messages.mensajeGenerico('Aún hay campos vacíos o que no cumplen con la estructura correcta.',
        'info', 'Los campos requeridos están marcados con un *'
      );
      return;
    }

    this.messages.mensajeConfirmacionCustom('¿Está seguro de continuar con el registro del tipo de orden?',
      'question', 'Registrar tipo de orden'
    ).then(res => {
      if (!res.isConfirmed) return;
      this.messages.mensajeEsperar();

      const tipoOrden: any = this.formtipoOrden.value;

      this.tipoOrdenes.registrarTipoOrden(tipoOrden).toPromise().then(
        respuesta => {
          console.log('RESPUESTA REGISTRO:', respuesta);
          this.pktipoOrden = respuesta.pktipoOrden;
          this.ch.markForCheck();

          this.obtenerDetalleTipoOrden(respuesta.pktipoOrden).then(() => {
            this.messages.mensajeGenerico(respuesta.mensaje, 'success', respuesta.title);
          });
        }, error => {
          this.messages.mensajeGenerico('error', 'error');
        }
      )
    });
  }

  protected actualizarTipoOrden(): void {
    if (this.formtipoOrden.invalid) {
      this.messages.mensajeGenerico('Aún hay campos vacíos o que no cumplen con la estructura correcta.', 'info', 'Los campos requeridos están marcados con un *');
      return;
    }

    this.messages.mensajeConfirmacionCustom('¿Está seguro de continuar con la actualización del tipo de mantenimiento?',
      'question', 'Actualizar tipo de mantenimiento').then(
        res => {
          if (!res.isConfirmed) return;

          this.messages.mensajeEsperar();

          const data: any = {
            pktipoOrden: this.pktipoOrden,
            tipoOrden: this.formtipoOrden.value
          };

          this.tipoOrdenes.actualizarTipoOrden(data).toPromise().then(
            respuesta => {
              this.obtenerDetalleTipoOrden(this.pktipoOrden).then(() => {
                this.messages.mensajeGenerico(respuesta.mensaje, 'success', respuesta.title)
              });
            }, error => {
              this.messages.mensajeGenerico('error', 'error')
            }
          )
        }
      )
  }

  get cambiosForm(): boolean {
    return this.formtipoOrden.dirty;
  }

  public cerrarModal(): void {
    if (!this.cambiosForm) {
      this.modal.cerrarModal();
      return;
    }

    this.messages.mensajeConfirmacionCustom('¿Está seguro de cerrar sin guardar cambios?',
      'question',
      'Cancelar registro'
    ).then(
      res => {
        if (!res.isConfirmed) return;

        this.modal.cerrarModal();
      }
    )
  }
}
