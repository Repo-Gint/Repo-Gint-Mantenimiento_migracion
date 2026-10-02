import { CommonModule } from '@angular/common';
import { ChangeDetectorRef, Component, OnDestroy, OnInit } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { MessagesService } from '../../../services/messages/messages';
import { ModalService } from '../../../services/modal/modal';
import { UsuariosService } from '../../../services/api/usuarios/usuarios';
import { firstValueFrom } from 'rxjs';
import { RegistrarUsuario } from '../registrar-usuario/registrar-usuario';

@Component({
  selector: 'app-consultar-usuarios',
  imports: [CommonModule, FormsModule],
  standalone: true,
  templateUrl: './consultar-usuarios.html',
  styleUrl: './consultar-usuarios.css',
})
export class ConsultarUsuarios implements OnInit, OnDestroy {
  protected datosTabla: any = [];
  protected textoBusqueda: string = '';
  private intervalo: any;

  constructor(
    private modal: ModalService,
    private usuarios: UsuariosService,
    private messages: MessagesService,
    private ch: ChangeDetectorRef,
  ) {} 

  async ngOnInit(): Promise<any> {
    this.messages.mensajeEsperar();

    await this.obtenerListaGeneralUsuarios();
    this.repetitiveInstruction();

    this.messages.cerrarMensajes();
  }

  private repetitiveInstruction(): void {
    this.intervalo = setInterval(() => {
      this.obtenerListaGeneralUsuarios();
    }, 10000);
  }

  public async obtenerListaGeneralUsuarios(): Promise<any> {
    try {
      const respuesta = await firstValueFrom(this.usuarios.obtenerListaGeneralUsuarios());
      this.datosTabla = respuesta.usuarios;
      this.ch.markForCheck();
    } catch (error: any) {
      const respuesta = error.error || {
        message: 'Error inesperado',
        title: 'Error'
      };

      this.messages.mensajeGenerico(
        respuesta.message,
        'error',
        respuesta.title
      );
    }
  }

  // --- GETTER PARA EL BUSCADOR DINÁMICO ---
  get usuariosFiltrados() {
    if (!this.textoBusqueda || this.textoBusqueda.trim() === '') {
      return this.datosTabla;
    }
    const query = this.textoBusqueda.toLowerCase();
    return this.datosTabla.filter((usuario: any) => 
      (usuario.empleado && usuario.empleado.toLowerCase().includes(query)) ||
      (usuario.rol && usuario.rol.toLowerCase().includes(query)) ||
      (usuario.busines_mail && usuario.busines_mail.toLowerCase().includes(query))
    );
  }

  protected cambiarStatus(usuario: any): void {
    this.messages.mensajeConfirmacionCustom(
      `¿Está seguro de ${usuario.active ? 'inactivar' : 'activar'} el usuario?`,
      'question',
      `${usuario.active ? 'Inactivar' : 'Activar'} usuario`
    ).then(res => {
      if (!res.isConfirmed) return;

      this.messages.mensajeEsperar();

      this.usuarios.cambiarStatusUsuario(usuario.id_users).subscribe(
        respuesta => {
          this.obtenerListaGeneralUsuarios().then(() => {
            this.messages.mensajeGenerico(
              respuesta.message, 
              'success',
              respuesta.title
            );
          });
        },
        error => {
          const respuesta = error.error || {
            message: 'Error inesperado',
            title: 'Error'
          };

          this.messages.mensajeGenerico(
            respuesta.message,
            'error',
            respuesta.title
          );
        }
      );
    });
  }

  public abrirModalRegistrarUsuario(pkUsuario: number): void {
    const data: any = {
      pkUsuario: pkUsuario
    };

    this.modal.abrirModalConComponente(RegistrarUsuario, data, 'lg-modal');
  }

  ngOnDestroy(): void {
    clearInterval(this.intervalo);
  }
}