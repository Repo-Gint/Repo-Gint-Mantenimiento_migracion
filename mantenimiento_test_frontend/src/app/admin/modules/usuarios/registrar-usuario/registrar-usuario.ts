import { ChangeDetectorRef, Component, Input } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { MessagesService } from '../../../services/messages/messages';
import { Usuarios } from '../../../services/api/usuarios/usuarios';
import { ModalService } from '../../../services/modal/modal';

@Component({
  selector: 'app-registrar-usuario',
  standalone: true,
  imports: [],
  templateUrl: './registrar-usuario.html',
  styleUrl: './registrar-usuario.css',
})
export class RegistrarUsuario {

  protected formUsuario!: FormGroup;

  protected listaAreas:            any[] = []; 
  protected listaEmpleados:        any[] = [];
  protected listabusinesEmpleados: any[] = [];
  
  constructor(
    private modal: ModalService,
    private fb: FormBuilder,
    private messages: MessagesService,
    private usuarios: Usuarios,
    private ch: ChangeDetectorRef
  ){}

  async ngOnInit(): Promise<any> {
    this.messages.mensajeEsperar();

    this.crearFormUsuario();
    
  }

  private crearFormUsuario(): void{
    this.formUsuario = this.fb.group ({
      id_rol_users:    ['', [Validators.required]],
      id_employee:     ['', [Validators.required]],
      id_busines_mail: ['', [Validators.required]],
      name:            [null, [Validators.required, Validators.pattern('[a-zA-Zá-úÁ-Ú ]*')]],
      password:        [null, [Validators.pattern('[a-zA-Zá-úÁ-Ú0-9 .,-_@#$%&+{}()?¿!¡\n\r\t]*')]]
    });
  } 

  private async obtenerRecursosRegistroUsuario(): Promise<void> {
    try {
      const respuesta: any = await this.usuarios.obtenerRecursosRegistroUsuario().toPromise();

      this.listaAreas = respuesta.recursos.listaareas;

      this.ch.detectChanges();
    } catch (error) {
      this.messages.mensajeGenerico('error', 'error');
    }
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

