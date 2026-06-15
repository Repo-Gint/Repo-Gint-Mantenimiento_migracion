import { CommonModule } from '@angular/common';
import { ChangeDetectorRef, Component, Input } from '@angular/core';
import { ModalService } from '../../../../services/modal/modal';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { MessagesService } from '../../../../services/messages/messages';
import { DepartamentosService } from '../../../../services/api/departamentos/departamentos';

@Component({
  selector: 'app-registrar-departamento',
  imports: [CommonModule, ReactiveFormsModule],
  standalone: true,
  templateUrl: './registrar-departamento.html',
  styleUrl: './registrar-departamento.css',
})
export class RegistrarDepartamento {
  @Input() pkDepartamento: any = null;

  protected formDepartamento!: FormGroup;

  constructor (
    private modal: ModalService,
    private ch: ChangeDetectorRef, 
    private fb: FormBuilder, 
    private messages: MessagesService, 
    private departamentos: DepartamentosService
  ) {}

  async ngOnInit(): Promise<any> {
    this.messages.mensajeEsperar();

    this.crearFormDepartamentos();

    if (this.pkDepartamento != null) await this.obtenerDetalleDepartamento(this.pkDepartamento); 

    this.messages.cerrarMensajes();
  }

  protected crearFormDepartamentos(): void {
    this.formDepartamento = this.fb.group({
      Departament_ES: [null, [Validators.required, Validators.pattern('^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+$')]],
      Departament_EN: [null, [Validators.required, Validators.pattern('^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+$')]],
      Acronym:        [null, [Validators.required, Validators.pattern('^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+$')]],
    });
  }

  public async obtenerDetalleDepartamento(pkDepartamento: number): Promise<any> {

    return this.departamentos.obtenerDetalleDepartamento(pkDepartamento).toPromise().then(
      respuesta => {

        const departamento = respuesta.departamento;

        this.formDepartamento.get('Departament_ES')?.setValue(departamento.Departament_ES);
        this.formDepartamento.get('Departament_EN')?.setValue(departamento.Departament_EN);
        this.formDepartamento.get('Acronym')?.setValue(departamento.Acronym);
      }
    );
  }

  protected registrarDepartamento(): void {

    if (this.formDepartamento.invalid) {
      this.messages.mensajeGenerico('Aún hay campos vacíos o que no cumplen con la estructura correcta.',
				'info', 'Los campos requeridos están marcados con un *'
			);
			return;
		}

    this.messages.mensajeConfirmacionCustom('¿Está seguro de continuar con el registro del departamento?',
				'question', 'Registrar departamento'
			).then(res=> {

        if (!res.isConfirmed) return;
        this.messages.mensajeEsperar();

        const departamento: any = this.formDepartamento.value;

        this.departamentos.registrarDepartamento(departamento).toPromise().then(
          respuesta => {

            this.pkDepartamento = respuesta.pkDepartamento;
            this.ch.markForCheck();

            this.obtenerDetalleDepartamento(respuesta.pkDepartamento).then(() => {
              this.messages.mensajeGenerico(respuesta.mensaje, 'success', respuesta.title);
            });
          }, error => {
            this.messages.mensajeGenerico('error', 'error');
          }
        );
      })
  }

  protected actualizarDepartamento(): void {
    if (this.formDepartamento.invalid) {
      this.messages.mensajeGenerico('Aún hay campos vacíos o que no cumplen con la estructura correcta.', 'info', 'Los campos requeridos están marcados con un *');
      return;
    }

    this.messages.mensajeConfirmacionCustom('¿Está seguro de continuar con la actualización del departamento?',
        'question', 'Actualizar departamento').then(
          res => {
            if (!res.isConfirmed) return;

            this.messages.mensajeEsperar();

            const data: any = {
              pkDepartamento: this.pkDepartamento, 
              departamento: this.formDepartamento.value
            };

            this.departamentos.actualizarDepartamento(data).toPromise().then(
              respuesta => {

                this.obtenerDetalleDepartamento(this.pkDepartamento).then(() => {
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
		return this.formDepartamento.dirty;
	}

	public cerrarModal(): void {
		if (!this.cambiosForm) {
			this.modal.cerrarModal();
			return;
		}

		this.messages.mensajeConfirmacionCustom(
			'¿Está seguro de cerrar sin guardar cambios?',
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
