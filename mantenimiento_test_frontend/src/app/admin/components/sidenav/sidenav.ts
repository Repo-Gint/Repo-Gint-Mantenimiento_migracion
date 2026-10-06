import { Component, OnInit, ChangeDetectorRef, HostBinding } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Router, RouterModule } from '@angular/router';
import { UsuariosService } from '../../services/api/usuarios/usuarios';
import { MessagesService } from '../../services/messages/messages';
import { ModalService } from '../../services/modal/modal';

import { RegistrarOrden } from '../../modules/ordenes/registrar-orden/registrar-orden';
import { RegistrarUsuario } from '../../modules/usuarios/registrar-usuario/registrar-usuario';
import { RegistrarArea } from '../../modules/catalogos/areas/registrar-area/registrar-area';
import { RegistrarDepartamento } from '../../modules/catalogos/departamentos/registrar-departamento/registrar-departamento';
import { RegistrarMaquinas } from '../../modules/catalogos/maquinas/registrar-maquinas/registrar-maquinas';
import { RegistrarCatmaquina } from '../../modules/catalogos/catmaquinas/registrar-catmaquina/registrar-catmaquina';
import { RegistrarMonedas } from '../../modules/catalogos/monedas/registrar-monedas/registrar-monedas';
import { RegistrarTipoMantenimiento } from '../../modules/catalogos/tipo-mantenimiento/registrar-tipo-mantenimiento/registrar-tipo-mantenimiento';
import { RegistrarTipoOrden } from '../../modules/catalogos/tipo-orden/registrar-tipo-orden/registrar-tipo-orden';

@Component({
  selector: 'app-sidenav',
  standalone: true,
  imports: [CommonModule, RouterModule],
  templateUrl: './sidenav.html',
  styleUrls: ['./sidenav.css']
})
export class Sidenav implements OnInit {
  protected permisosUsuario: string[] = [];
  protected rolUsuario: number = 1;

  // VARIABLE PARA CONTROLAR LA ANIMACIÓN DEL MENÚ
  protected isMenuOpen: boolean = false;

  // ESTO ASIGNA LA CLASE 'nav-open' DIRECTAMENTE AL COMPONENTE <app-sidenav>
  @HostBinding('class.nav-open') get openClass() {
    return this.isMenuOpen;
  }

  constructor(
    private usuarios: UsuariosService,
    private messages: MessagesService,
    private modal: ModalService,
    private router: Router,
    private ch: ChangeDetectorRef
  ) {}

  async ngOnInit(): Promise<void> {
    await this.cargarPermisosUsuario();
  }

  // FUNCIÓN PARA ABRIR/CERRAR EL SIDENAV
  protected toggleMenu(): void {
    this.isMenuOpen = !this.isMenuOpen;
  }

  private async cargarPermisosUsuario(): Promise<void> {
    try {
      const respuesta: any = await this.usuarios.obtenerPermisosUsuarioActual().toPromise();
      this.permisosUsuario = respuesta?.permisos || [];
      this.rolUsuario = Number(respuesta?.id_rol_users || 1);
      this.ch.detectChanges();
    } catch (error) {
      this.permisosUsuario = [];
      this.rolUsuario = 1;
    }
  }

  protected tienePermiso(slug: string): boolean {
    const esAdminOTecnico = (this.rolUsuario === 2 || this.rolUsuario === 3);
    return esAdminOTecnico || this.permisosUsuario.includes(slug);
  }

  // --- MÉTODOS PARA ABRIR MODALES DE REGISTRO ---

  protected abrirModalRegistrarOrden(): void {
    this.modal.abrirModalConComponente(RegistrarOrden, { pkOrden: null }, 'lg-modal');
  }

  protected abrirModalRegistrarUsuario(): void {
    this.modal.abrirModalConComponente(RegistrarUsuario, {}, 'lg-modal');
  }

  protected abrirModalRegistrarArea(): void {
    this.modal.abrirModalConComponente(RegistrarArea, {}, 'md-modal');
  }

  protected abrirModalRegistrarDepartamento(): void {
    this.modal.abrirModalConComponente(RegistrarDepartamento, {}, 'md-modal');
  }

  protected abrirModalRegistrarMaquinas(): void {
    this.modal.abrirModalConComponente(RegistrarMaquinas, {}, 'md-modal');
  }

  protected abrirModalRegistrarCatalogoMaquina(): void {
    this.modal.abrirModalConComponente(RegistrarCatmaquina, {}, 'md-modal');
  }

  protected abrirModalRegistrarMonedas(): void {
    this.modal.abrirModalConComponente(RegistrarMonedas, {}, 'md-modal');
  }

  protected abrirModalRegistrartipoMantenimiento(): void {
    this.modal.abrirModalConComponente(RegistrarTipoMantenimiento, {}, 'md-modal');
  }

  protected abrirModalRegistrartipoOrden(): void {
    this.modal.abrirModalConComponente(RegistrarTipoOrden, {}, 'md-modal');
  }
}