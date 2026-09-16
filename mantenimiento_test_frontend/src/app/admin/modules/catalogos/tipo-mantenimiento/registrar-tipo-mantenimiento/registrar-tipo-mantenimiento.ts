import { CommonModule } from '@angular/common';
import { ChangeDetectorRef, Component, Input } from '@angular/core';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { ModalService } from '../../../../services/modal/modal';
import { MessagesService } from '../../../../services/messages/messages';
import { TipoMantenimientoService } from '../../../../services/api/tipoMantenimiento/tipo-mantenimiento';

@Component({
  selector: 'app-registrar-tipo-mantenimiento',
  imports: [CommonModule, ReactiveFormsModule],
  standalone: true,
  templateUrl: './registrar-tipo-mantenimiento.html',
  styleUrl: './registrar-tipo-mantenimiento.css',
})
export class RegistrarTipoMantenimiento {
  @Input() pktipoMantenimiento: any = null;

  protected formtipoMantenimiento!: FormGroup;

  constructor(
    private modal: ModalService,
    private ch: ChangeDetectorRef,
    private fb: FormBuilder,
    private messages: MessagesService,
    private tipoMantenimientos: TipoMantenimientoService,
  ) { }

  async ngOnInit(): Promise<any> {
    this.messages.mensajeEsperar();

    this.crearFormtipoMantenimiento();

    if (this.pktipoMantenimiento != null) await this.obtenerDetalletipoMantenimiento(this.pktipoMantenimiento);

    this.messages.cerrarMensajes();
  }

  protected crearFormtipoMantenimiento(): void {
    this.formtipoMantenimiento = this.fb.group({
      type_maintenances: [null, [Validators.required, Validators.pattern('^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+$')]],
      color: [null, [Validators.required, Validators.pattern('^#([A-Fa-f0-9]{6})$')]],
      acronym: [null, [Validators.required, Validators.pattern('^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+$')]],
    });
  }

  public async obtenerDetalletipoMantenimiento(pktipoMantenimiento: number): Promise<any> {

    return this.tipoMantenimientos.obtenerDetalletipoMantenimiento(pktipoMantenimiento).toPromise().then(
      respuesta => {
        const tipoMantenimiento = respuesta.tipoMantenimiento;
        this.formtipoMantenimiento.get('type_maintenances')?.setValue(tipoMantenimiento.type_maintenances);
        this.formtipoMantenimiento.get('color')?.setValue(tipoMantenimiento.color);
        this.formtipoMantenimiento.get('acronym')?.setValue(tipoMantenimiento.acronym);
      });
  }

  protected registrarTipoMantenimiento(): void {
    if (this.formtipoMantenimiento.invalid) {
      this.messages.mensajeGenerico('Aún hay campos vacíos o que no cumplen con la estructura correcta.',
        'info', 'Los campos requeridos están marcados con un *'
      );
      return;
    }

    this.messages.mensajeConfirmacionCustom('¿Está seguro de continuar con el registro del tipo de mantenimiento?',
      'question', 'Registrar tipo de mantenimiento'
    ).then(res => {
      if (!res.isConfirmed) return;
      this.messages.mensajeEsperar();

      const tipoMantenimiento: any = this.formtipoMantenimiento.value;

      this.tipoMantenimientos.registrarTipoMantenimiento(tipoMantenimiento).toPromise().then(
        respuesta => {
          this.pktipoMantenimiento = respuesta.pktipoMantenimiento;
          this.ch.markForCheck();

          this.obtenerDetalletipoMantenimiento(respuesta.pktipoMantenimiento).then(() => {
            this.messages.mensajeGenerico(respuesta.mensaje, 'success', respuesta.title);
          });
        }, error => {
          this.messages.mensajeGenerico('error', 'error');
        }
      )
    })
  }

  protected actualizartipoMantenimiento(): void {
    if (this.formtipoMantenimiento.invalid) {
      this.messages.mensajeGenerico('Aún hay campos vacíos o que no cumplen con la estructura correcta.', 'info', 'Los campos requeridos están marcados con un *');
      return;
    }

    this.messages.mensajeConfirmacionCustom('¿Está seguro de continuar con la actualización del tipo de mantenimiento?',
      'question', 'Actualizar tipo de mantenimiento').then(
        res => {
          if (!res.isConfirmed) return;

          this.messages.mensajeEsperar();

          const data: any = {
            pktipoMantenimiento: this.pktipoMantenimiento,
            tipoMantenimiento: this.formtipoMantenimiento.value
          };

          this.tipoMantenimientos.actualizartipoMantenimiento(data).toPromise().then(
            respuesta => {
              this.obtenerDetalletipoMantenimiento(this.pktipoMantenimiento).then(() => {
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
    return this.formtipoMantenimiento.dirty;
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
