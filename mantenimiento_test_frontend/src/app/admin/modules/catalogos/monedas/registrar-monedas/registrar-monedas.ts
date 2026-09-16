import { CommonModule } from '@angular/common';
import { ChangeDetectorRef, Component, Input } from '@angular/core';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { ModalService } from '../../../../services/modal/modal';
import { MessagesService } from '../../../../services/messages/messages';
import { Monedaservice } from '../../../../services/api/Monedas/monedas';

@Component({
  selector: 'app-registrar-monedas',
  imports: [CommonModule, ReactiveFormsModule],
  standalone: true,
  templateUrl: './registrar-monedas.html',
  styleUrl: './registrar-monedas.css',
})
export class RegistrarMonedas {
  @Input() pkMoneda: any = null;

  protected formMoneda!: FormGroup;

  constructor (
    private modal: ModalService, 
    private ch: ChangeDetectorRef, 
    private fb: FormBuilder,
    private messages: MessagesService,
    private monedas: Monedaservice
  ) {}

  async ngOnInit(): Promise<any> {
    this.messages.mensajeEsperar();

    this.crearFormMonedas();

    if (this.pkMoneda != null) await this.obtenerDetalleMoneda(this.pkMoneda);

    this.messages.cerrarMensajes();
  }

  protected crearFormMonedas(): void {
    this.formMoneda = this.fb.group({
      coin: [null, [Validators.required, Validators.pattern('^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+$')]],
    });
  }

  public async obtenerDetalleMoneda(pkMoneda: number): Promise<any> {

    return this.monedas.obtenerDetalleMoneda(pkMoneda).toPromise().then(
      respuesta => {

        const moneda = respuesta.monedas;

        this.formMoneda.get('coin')?.setValue(moneda.coin);
      }
    );
  }

  protected registrarMoneda(): void {
    if (this.formMoneda.invalid) {
     this.messages.mensajeGenerico('Aún hay campos vacíos o que no cumplen con la estructura correcta.',
				'info', 'Los campos requeridos están marcados con un *'
			);
			return;
		}

    this.messages.mensajeConfirmacionCustom('¿Está seguro de continuar con el registro de la moneda?',
				'question', 'Registrar moneda'
			).then(res=> {

        if (!res.isConfirmed) return;
        this.messages.mensajeEsperar();

        const moneda: any = this.formMoneda.value;

        this.monedas.registrarMoneda(moneda).toPromise().then(
          respuesta => {
            this.pkMoneda = respuesta.pkMoneda;
            this.ch.markForCheck();

            this.obtenerDetalleMoneda(respuesta.pkMoneda).then(() => {
              this.messages.mensajeGenerico(respuesta.mensaje, 'success', respuesta.title);
            });
          }, error => {
            this.messages.mensajeGenerico('error', 'error');
          }
        );
      })
  }

  protected actualizarMoneda(): void {
        if (this.formMoneda.invalid) {
      this.messages.mensajeGenerico('Aún hay campos vacíos o que no cumplen con la estructura correcta.', 'info', 'Los campos requeridos están marcados con un *');
      return;
    }

    this.messages.mensajeConfirmacionCustom('¿Está seguro de continuar con la actualización del departamento?',
        'question', 'Actualizar departamento').then(
          res => {
            if (!res.isConfirmed) return;

            this.messages.mensajeEsperar();

            const data: any = {
              pkMoneda: this.pkMoneda,
              moneda: this.formMoneda.value
            };

            this.monedas.actualizarMoneda(data).toPromise().then(
              respuesta => {
                this.obtenerDetalleMoneda(this.pkMoneda).then(() => {
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
    return this.formMoneda.dirty;
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
