import { ChangeDetectorRef, Component, Input, OnInit } from '@angular/core';
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
export class RegistrarUsuario implements OnInit {

  @Input() pkUsuario: any = null;

  protected formUsuario!: FormGroup;

  protected listaRol:             any[] = []; 
  protected listaempleado:        any[] = [];
  protected listaPermisos:        any[] = []; 
  protected permisosSeleccionados: number[] = []; 
  
  // Ahora se muestran los permisos siempre que se seleccione cualquier rol
  protected mostrarPermisos: boolean = false;

  protected dropdownEmpleadoAbierto: boolean = false;
  protected dropdownRolAbierto: boolean = false;
  protected empleadoSeleccionadoTexto: string = '';
  protected rolSeleccionadoTexto: string = '';

  protected passwordStrength: number = 0;
  protected strengthColorClass: string = 'bg-danger';
  protected passwordFeedbackText: string = 'Contraseña vacía';

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

    if (this.pkUsuario != null) {
      await this.obtenerDetalleUsuario(this.pkUsuario);
    }

    this.messages.cerrarMensajes();
  }

  private crearFormUsuario(): void {
    const passwordValidators = this.pkUsuario == null 
      ? [Validators.required, Validators.minLength(6)] 
      : [Validators.minLength(6)];

    this.formUsuario = this.fb.group({
      id_employee:   ['', [Validators.required]],
      id_rol_users:  ['', [Validators.required]],
      busines_mail:  ['', [Validators.required, Validators.email]],
      password:      ['', passwordValidators],
      permisos:      [[]]
    });
  } 

  protected toggleDropdown(tipo: 'empleado' | 'rol'): void {
    if (tipo === 'empleado') {
      this.dropdownEmpleadoAbierto = !this.dropdownEmpleadoAbierto;
      this.dropdownRolAbierto = false;
    } else {
      this.dropdownRolAbierto = !this.dropdownRolAbierto;
      this.dropdownEmpleadoAbierto = false;
    }
  }

  protected cerrarDropdowns(): void {
    this.dropdownEmpleadoAbierto = false;
    this.dropdownRolAbierto = false;
  }

  protected seleccionarEmpleado(id: any, nombre: string): void {
    this.formUsuario.get('id_employee')?.setValue(id);
    this.empleadoSeleccionadoTexto = id ? nombre : '';
    this.dropdownEmpleadoAbierto = false;
    this.formUsuario.get('id_employee')?.markAsTouched();
    this.ch.detectChanges();
  }

  protected seleccionarRol(id: any, nombre: string): void {
    this.formUsuario.get('id_rol_users')?.setValue(id);
    this.rolSeleccionadoTexto = id ? nombre : '';
    this.dropdownRolAbierto = false;
    this.formUsuario.get('id_rol_users')?.markAsTouched();
    
    // Muestra los permisos al seleccionar cualquier rol válido
    this.verificarVisibilidadPermisos(id);
    this.ch.detectChanges();
  }

  protected onPasswordInput(event: any): void {
    const val = event.target.value || '';
    const len = val.length;

    if (len === 0) {
      this.passwordStrength = 0;
      this.strengthColorClass = 'bg-danger';
      this.passwordFeedbackText = 'Contraseña vacía';
    } else if (len <= 2) {
      this.passwordStrength = 1;
      this.strengthColorClass = 'bg-danger';
      this.passwordFeedbackText = 'Seguridad Baja (Muy corta)';
    } else if (len === 3 || len === 4 || len === 5) {
      this.passwordStrength = 2;
      this.strengthColorClass = 'bg-warning';
      this.passwordFeedbackText = 'Seguridad Media';
    } else if (len === 6 || len === 7) {
      this.passwordStrength = 3;
      this.strengthColorClass = 'bg-warning';
      this.passwordFeedbackText = 'Seguridad Buena';
    } else {
      this.passwordStrength = 4;
      this.strengthColorClass = 'bg-success';
      this.passwordFeedbackText = 'Seguridad Alta (Segura)';
    }
    this.ch.detectChanges();
  }

  protected togglePermisoCard(idPermission: number): void {
    if (this.esPermisoSeleccionado(idPermission)) {
      this.permisosSeleccionados = this.permisosSeleccionados.filter(id => id !== idPermission);
    } else {
      this.permisosSeleccionados.push(idPermission);
    }
    this.ch.detectChanges();
  }

  private verificarVisibilidadPermisos(idRol: any): void {
    // Muestra los permisos siempre que haya un rol seleccionado
    if (!idRol) {
      this.mostrarPermisos = false;
      this.permisosSeleccionados = [];
    } else {
      this.mostrarPermisos = true;
    }
    this.ch.detectChanges();
  }

  protected onPermisoChange(idPermission: number, event: any): void {}

  protected esPermisoSeleccionado(idPermission: number): boolean {
    return this.permisosSeleccionados.includes(idPermission);
  }

  public async obtenerDetalleUsuario(pkUsuario: number): Promise<void> {
    return this.usuarios.obtenerDetalleUsuario(pkUsuario).toPromise().then(
      respuesta => {
        const usuario = respuesta.usuario;

        this.formUsuario.get('id_rol_users')?.setValue(usuario.id_rol_users);
        this.formUsuario.get('id_employee')?.setValue(usuario.id_employee);
        this.formUsuario.get('busines_mail')?.setValue(usuario.busines_mail);
        
        const empEncontrado = this.listaempleado.find(e => String(e.id_employee) === String(usuario.id_employee));
        if (empEncontrado) this.empleadoSeleccionadoTexto = empEncontrado.Name;

        const rolEncontrado = this.listaRol.find(r => String(r.id_roles) === String(usuario.id_rol_users));
        if (rolEncontrado) this.rolSeleccionadoTexto = rolEncontrado.roles;

        this.permisosSeleccionados = usuario.permisos || [];
        this.verificarVisibilidadPermisos(usuario.id_rol_users);

        if (usuario.password) {
          this.onPasswordInput({ target: { value: '123456' } });
        }
        this.ch.detectChanges();
      }
    );
  }

  private async obtenerRecursosRegistroUsuario(): Promise<void> {
    try {
      const respuesta: any = await this.usuarios.obtenerRecursosRegistroUsuario().toPromise();
 
      this.listaRol      = respuesta.recursos.listaRol;
      this.listaempleado = respuesta.recursos.listaempleado;
      this.listaPermisos = respuesta.recursos.listaPermisos || [];
 
      const rolActual = this.formUsuario.get('id_rol_users')?.value;
      if (rolActual) {
        this.verificarVisibilidadPermisos(rolActual);
      }

      this.ch.detectChanges();
    } catch (error) {
      this.messages.mensajeGenerico('error', 'error');
    }
  }
 
  protected validarCamposYRegistrar(): void {
    if (this.formUsuario.invalid) {
      this.formUsuario.markAllAsTouched();
      
      const camposFaltantes: string[] = [];
      if (this.formUsuario.get('id_employee')?.invalid) camposFaltantes.push('Empleado');
      if (this.formUsuario.get('busines_mail')?.invalid) camposFaltantes.push('Correo Empresarial (válido)');
      if (this.formUsuario.get('password')?.invalid) camposFaltantes.push('Contraseña (mínimo 6 caracteres)');
      if (this.formUsuario.get('id_rol_users')?.invalid) camposFaltantes.push('Rol de Usuario');

      const mensajeHtml = camposFaltantes.length > 0 
        ? `Faltan los siguientes campos por llenar o son incorrectos:<br><br>• ${camposFaltantes.join('<br>• ')}`
        : 'Aún hay campos vacíos o incorrectos.';

      this.messages.mensajeGenerico(
        mensajeHtml,
        'info',
        'Campos Obligatorios'
      );
      return;
    }
   
    this.registrarUsuario();
  }

  protected registrarUsuario(): void {
      this.messages.mensajeConfirmacionCustom(
          '¿Está seguro de registrar el usuario con sus permisos?',
          'question',
          'Registrar usuario'
      ).then(res => {
        if (!res.isConfirmed) return;
 
        this.messages.mensajeEsperar();
        
        const payload = {
          ...this.formUsuario.value,
          permisos: this.mostrarPermisos ? this.permisosSeleccionados : []
        };

        this.usuarios.registrarUsuario(payload).subscribe({      
            next: (res: any) => {
                this.messages.cerrarMensajes();
                this.messages.mensajeGenerico(
                    res.mensaje, 'success', res.title
                );
                this.modal.cerrarModal();
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
                this.messages.mensajeGenerico('Error al registrar usuario', 'error', 'Error');
            }
        });
      });
  }

  protected validarCamposYActualizar(): void {
    if (this.formUsuario.invalid) {
      this.formUsuario.markAllAsTouched();
      
      const camposFaltantes: string[] = [];
      if (this.formUsuario.get('id_employee')?.invalid) camposFaltantes.push('Empleado');
      if (this.formUsuario.get('busines_mail')?.invalid) camposFaltantes.push('Correo Empresarial (válido)');
      if (this.formUsuario.get('password')?.invalid && this.formUsuario.get('password')?.value) camposFaltantes.push('Contraseña (mínimo 6 caracteres si se modifica)');
      if (this.formUsuario.get('id_rol_users')?.invalid) camposFaltantes.push('Rol de Usuario');

      const mensajeHtml = camposFaltantes.length > 0 
        ? `Faltan los siguientes campos por llenar o son incorrectos:<br><br>• ${camposFaltantes.join('<br>• ')}`
        : 'Aún hay campos vacíos o incorrectos.';

      this.messages.mensajeGenerico(
        mensajeHtml,
        'info',
        'Campos Obligatorios'
      );
      return;
    }

    this.actualizarUsuario();
  }
    
  protected actualizarUsuario(): void {
    this.messages.mensajeConfirmacionCustom('¿Está seguro de continuar con la actualización del usuario?',
      'question', 'Actualizar usuario').then(
        res => {
          if(!res.isConfirmed) return;

          this.messages.mensajeEsperar();

          const data: any = {
            pkUsuario: this.pkUsuario,
            usuario: {
              ...this.formUsuario.value,
              permisos: this.mostrarPermisos ? this.permisosSeleccionados : []
            }
          };

          this.usuarios.actualizarUsuario(data).toPromise().then(
            respuesta => {
              this.obtenerDetalleUsuario(this.pkUsuario).then(() => {
                this.messages.mensajeGenerico(respuesta.mensaje, 'success', respuesta.title);
                this.modal.cerrarModal();
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