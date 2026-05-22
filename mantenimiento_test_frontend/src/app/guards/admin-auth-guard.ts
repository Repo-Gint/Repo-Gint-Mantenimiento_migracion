import { Injectable } from '@angular/core';
import { CanActivate, CanActivateChild, Router } from '@angular/router';
import { ModalService } from '../admin/services/modal/modal';
import { MessagesService } from '../admin/services/messages/messages';

@Injectable({
  providedIn: 'root'
})
export class AuthGuard implements CanActivate, CanActivateChild {

  constructor(
    private messages: MessagesService,
    private router: Router
  ) {}

  checkAuth(): boolean {
    const token = localStorage.getItem('token_tickets_faske');

    if (token) {
      return true;
    }

    this.messages.mensajeGenerico(
    'Debes iniciar sesión para acceder',
    'warning',
    'Acceso no autorizado'
  );

    this.router.navigate(['/login']);
    return false;
  }

  canActivate(): boolean {
    return this.checkAuth();
  }

  canActivateChild(): boolean {
    return this.checkAuth();
  }
}