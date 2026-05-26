import { Injectable } from '@angular/core';
import { Router } from '@angular/router';
import { MessagesService } from '../messages/messages';

@Injectable({
  providedIn: 'root'
})
export class SessionService {

  private timeout: any;
  private tiempoInactividad = 120000;

  private listenersActivos = false;

  private mousemoveFn = () => this.resetTimer();
  private keydownFn = () => this.resetTimer();
  private clickFn = () => this.resetTimer();

  constructor(
    private router: Router,
    private messages: MessagesService
  ) { }

  iniciarContador() {

    const token = localStorage.getItem('token_orden_gitlatam');

    if (!token || this.router.url.includes('login')) {
      this.detenerContador();
      return;
    }

    if (this.listenersActivos) return;

    this.listenersActivos = true;

    this.resetTimer();

    window.addEventListener('mousemove', this.mousemoveFn);
    window.addEventListener('keydown', this.keydownFn);
    window.addEventListener('click', this.clickFn);
  }

  resetTimer() {
    clearTimeout(this.timeout);

    this.timeout = setTimeout(() => {
      this.expirarSesion();
    }, this.tiempoInactividad);
  }

 expirarSesion() {

  const token = localStorage.getItem('token_orden_gitlatam');

  if (!token) {
    this.detenerContador();
    return;
  }

  this.detenerContador();

  localStorage.clear();
  sessionStorage.clear();


  this.messages.mensajeGenerico(
    'Tu sesión expiró por inactividad',
    'warning',
    'Sesión expirada'
  );

  this.router.navigate(['/login']);

  setTimeout(() => {
    window.location.replace('/login');
  }, 100);
}

  detenerContador() {
    clearTimeout(this.timeout);

    if (!this.listenersActivos) return;

    window.removeEventListener('mousemove', this.mousemoveFn);
    window.removeEventListener('keydown', this.keydownFn);
    window.removeEventListener('click', this.clickFn);

    this.listenersActivos = false;
  }
}