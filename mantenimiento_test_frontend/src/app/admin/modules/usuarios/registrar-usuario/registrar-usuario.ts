import { ChangeDetectorRef, Component, Input } from '@angular/core';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { MessagesService } from '../../../services/messages/messages';
import { UsuariosService } from '../../../services/api/usuarios/usuarios';
import { ModalService } from '../../../services/modal/modal';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-registrar-usuario',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule],
  templateUrl: './registrar-usuario.html',
  styleUrl: './registrar-usuario.css',
})
export class RegistrarUsuario {

  @Input() pkUsuario: any = null;

  protected formUsuario!: FormGroup;

  protected listaRol:              any[] = []; 
  protected listaempleado:         any[] = [];
  protected listabusinesEmpleados: any[] = [];
  
  constructor(
    private modal:    ModalService,
    private fb:       FormBuilder,
    private messages: MessagesService,
    private usuarios: UsuariosService,
    private ch:       ChangeDetectorRef
  ){}

  async ngOnInit(): Promise<void> {
    this.messages.mensajeEsperar();

    this.crearFormUsuario();
    await this.obtenerRecursosRegistroUsuario();

    if (this.pkUsuario != null) await this.obtenerDetalleUsuario(this.pkUsuario);

    this.messages.cerrarMensajes();
    
  }

  private crearFormUsuario(): void{
    this.formUsuario = this.fb.group ({
      id_employee:     ['', [Validators.required]],
      id_rol_users:    ['', [Validators.required]],
      busines_mail:    [null, [Validators.required, Validators.pattern('[a-zA-Zá-úÁ-Ú0-9 .,-_@#$%&+{}()?¿!¡\n\r\t]*')]],
      password:        [null, [Validators.pattern('[a-zA-Zá-úÁ-Ú0-9 .,-_@#$%&+{}()?¿!¡\n\r\t]*')]]
    });
  } 

  public async obtenerDetalleUsuario(pkUsuario: number): Promise<void> {
    return this.usuarios.obtenerDetalleUsuario(pkUsuario).toPromise().then(
      respuesta => {
        const usuario = respuesta.usuario;

        this.formUsuario.get('id_rol_users')?.setValue(usuario.id_rol_users);
        this.formUsuario.get('id_employee')?.setValue(usuario.id_employee);
        this.formUsuario.get('busines_mail')?.setValue(usuario.busines_mail);
        this.formUsuario.get('password')?.setValue(usuario.password);
      }
    )
  }

    private async obtenerRecursosRegistroUsuario(): Promise<void> {
      try {
  
        const respuesta: any = await this.usuarios.obtenerRecursosRegistroUsuario().toPromise();
  
        this.listaRol = respuesta.recursos.listaRol;
        this.listaempleado = respuesta.recursos.listaempleado;
        console.log(this.listaempleado);
  
        this.ch.detectChanges();
      } catch (error) {
        this.messages.mensajeGenerico('error', 'error');
      }
    }
  
      protected registrarUsuario(): void {
      
          if (this.formUsuario.invalid) {
              this.messages.mensajeGenerico(
                  'Aún hay campos vacíos o incorrectos.',
                  'info',
                  'Validación'
              );
              return;
          }
      
          this.messages.mensajeConfirmacionCustom(
              '¿Está seguro de registrar el usuario?',
              'question',
              'Registrar usuario'
          ).then(res => {
              if (!res.isConfirmed) return;
      
              this.messages.mensajeEsperar();
              const usuario = this.formUsuario.value;
              this.usuarios.registrarUsuario(usuario).subscribe({      
                  next: (res: any) => {
                      this.messages.cerrarMensajes();
                      this.messages.mensajeGenerico(
                          res.message,
                          'success',
                          res.title
                      );
                  },
      
                  error: (err) => {
                      this.messages.cerrarMensajes();
                      if (err.status === 409) {
                          this.messages.mensajeGenerico(
                              err.error.message,
                              'info',
                              err.error.title
                          );
                          return;
                      }
      
                      this.messages.mensajeGenerico(
                          'Error al registrar usuario',
                          'error',
                          'Error'
                      );
                  }
              });
      
          });
      }
    
	protected actualizarUsuario(): void {
		if(this.formUsuario.invalid) {
			this.messages.mensajeGenerico('Aún hay campos vacíos o que no cumplen con la estructura correcta.', 'info', 'Los campos requeridos están marcados con un *');
			return;
		}

		this.messages.mensajeConfirmacionCustom('¿Está seguro de continuar con la actualización del usuario?',
			'question', 'Actualizar usuario').then(
				res => {
					if(!res.isConfirmed) return;

					this.messages.mensajeEsperar();

					const data: any = {
						pkUsuario: this.pkUsuario,
						usuario: this.formUsuario.value
					};

					this.usuarios.actualizarUsuario(data).toPromise().then(
						respuesta => {
							this.obtenerDetalleUsuario(this.pkUsuario).then(() => {
								this.messages.mensajeGenerico(respuesta.mensaje, 'success', respuesta.title);
							});
						}, error => {
							this.messages.mensajeGenerico('error', 'error');
						}
					)
				});
	}

  get cambiosForm(): boolean {
		return this.formUsuario.dirty;
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
			});
	}
}

