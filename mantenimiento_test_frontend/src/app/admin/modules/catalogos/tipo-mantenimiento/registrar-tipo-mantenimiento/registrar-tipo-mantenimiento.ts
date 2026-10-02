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

  protected seleccionarColor(colorHex: string): void {
    this.formtipoMantenimiento.get('color')?.setValue(colorHex);
    this.formtipoMantenimiento.get('color')?.markAsTouched();
    this.formtipoMantenimiento.get('color')?.markAsDirty();
  }

  // Verifica si el campo es inválido
  protected campoEsInvalido(nombreCampo: string): boolean {
    const campo = this.formtipoMantenimiento.get(nombreCampo);
    return !!(campo && campo.invalid && (campo.touched || campo.dirty));
  }

  // Verifica si el campo es válido (para mostrar la palomita verde)
  protected campoEsValido(nombreCampo: string): boolean {
    const campo = this.formtipoMantenimiento.get(nombreCampo);
    return !!(campo && campo.valid && (campo.touched || campo.dirty));
  }

  protected obtenerMensajeError(nombreCampo: string): string {
    const campo = this.formtipoMantenimiento.get(nombreCampo);
    if (!campo || !campo.errors) return '';

    if (campo.errors['required']) return 'Este campo es obligatorio.';
    if (campo.errors['pattern']) {
      if (nombreCampo === 'color') return 'Formato HEX inválido (Ej: #EF4444).';
      return 'Solo se permiten letras y espacios.';
    }
    return 'Campo inválido.';
  }

  private obtenerCamposInvalidosTexto(): string {
    const camposNombres: { [key: string]: string } = {
      type_maintenances: 'Tipo de Mantenimiento',
      color: 'Color',
      acronym: 'Abreviatura'
    };

    const pendientes: string[] = [];
    Object.keys(this.formtipoMantenimiento.controls).forEach(key => {
      const control = this.formtipoMantenimiento.get(key);
      if (control && control.invalid) {
        pendientes.push(camposNombres[key] || key);
      }
    });

    return pendientes.length > 0 ? pendientes.join(', ') : '';
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
      this.formtipoMantenimiento.markAllAsTouched();
      const camposFaltantes = this.obtenerCamposInvalidosTexto();
      
      this.messages.mensajeGenerico(
        `Por favor verifica los siguientes campos: ${camposFaltantes}.`,
        'info',
        'Campos incompletos o incorrectos'
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
      this.formtipoMantenimiento.markAllAsTouched();
      const camposFaltantes = this.obtenerCamposInvalidosTexto();

      this.messages.mensajeGenerico(
        `Por favor verifica los siguientes campos: ${camposFaltantes}.`,
        'info',
        'Campos incompletos o incorrectos'
      );
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