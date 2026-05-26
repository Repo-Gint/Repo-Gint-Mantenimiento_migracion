import { Component } from '@angular/core';
import { MessagesService } from '../../services/messages/messages';
import { Router } from '@angular/router';
import { Usuarios } from '../../services/api/usuarios/usuarios';

@Component({
  selector: 'app-navbar',
  standalone: true,
  imports: [],
  templateUrl: './navbar.html',
  styleUrl: './navbar.css',
})
export class Navbar {

  constructor (
    private usuariosService: Usuarios,
    private messages: MessagesService,
    private router: Router
  ) {}

  protected cerrarSesion(): void{ 
    this.messages.mensajeConfirmacionCustom(
      '¿Está seguro de cerrar sesión?',
      'question',
      'Cerrar sesión'
    ).then(res => {
      if (!res.isConfirmed) return;

      this.messages.mensajeEsperar();

      this.usuariosService.cerrarSesion().subscribe({
        next: (respuesta: any) => {
          this.messages.mensajeGenerico(
            respuesta.mensaje, 
            'success',
            'Cerrar sesión',
          );

          localStorage.clear();

          this.router.navigate(['/login']);
        },

        error: (error: any) => {

          this.messages.mensajeGenerico(
            error?.error?.mensaje || 'Ocurrió un error al cerrar sesión.',
            'error'
          );

          localStorage.clear();
          this.router.navigate(['/login']);
        }
      });
    });
  }
}