import { Component } from '@angular/core';
import { RouterLink } from '@angular/router';
import { ModalService } from '../../services/modal/modal';
import { RegistrarUsuario } from '../../modules/usuarios/registrar-usuario/registrar-usuario';
import { RegistrarArea } from '../../modules/catalogos/areas/registrar-area/registrar-area';
import { RegistrarDepartamento } from '../../modules/catalogos/departamentos/registrar-departamento/registrar-departamento';
import { RegistrarMaquinas } from '../../modules/catalogos/maquinas/registrar-maquinas/registrar-maquinas';
import { RegistrarMonedas } from '../../modules/catalogos/monedas/registrar-monedas/registrar-monedas';
import { RegistrarTipoMantenimiento } from '../../modules/catalogos/tipo-mantenimiento/registrar-tipo-mantenimiento/registrar-tipo-mantenimiento';
import { RegistrarTipoOrden } from '../../modules/catalogos/tipo-orden/registrar-tipo-orden/registrar-tipo-orden';
import { RegistrarOrden } from '../../modules/ordenes/registrar-orden/registrar-orden';
import { RegistrarCatmaquina } from '../../modules/catalogos/catmaquinas/registrar-catmaquina/registrar-catmaquina';

@Component({
  selector: 'app-sidenav',
  standalone: true,
  imports: [RouterLink],
  templateUrl: './sidenav.html',
  styleUrl: './sidenav.css',
})
export class Sidenav {
  constructor(
    private modal: ModalService
  ) { }

  public abrirModalRegistrarUsuario(): void {
    this.modal.abrirModalConComponente(RegistrarUsuario, {}, 'lg-modal');
  }

  public abrirModalRegistrarArea(): void {
    this.modal.abrirModalConComponente(RegistrarArea, {}, 'md-modal');
  }

  public abrirModalRegistrarDepartamento(): void {
    this.modal.abrirModalConComponente(RegistrarDepartamento, {}, 'md-moodal')
  }

  public abrirModalRegistrarMaquinas(): void {
    this.modal.abrirModalConComponente(RegistrarMaquinas, {}, 'md-modal')
  }

  public abrirModalRegistrarMonedas(): void {
    this.modal.abrirModalConComponente(RegistrarMonedas, {}, 'md-modal')
  }

  public abrirModalRegistrartipoOrden(): void {
    this.modal.abrirModalConComponente(RegistrarTipoOrden, {}, 'md-modal')
  }

  public abrirModalRegistrartipoMantenimiento(): void {
    this.modal.abrirModalConComponente(RegistrarTipoMantenimiento, {}, 'md-modal')
  }

  public abrirModalRegistrarOrden(): void {
    this.modal.abrirModalConComponente(RegistrarOrden, {}, 'md-modal' )
  }

  public abrirModalRegistrarCatalogoMaquina(): void {
    this.modal.abrirModalConComponente(RegistrarCatmaquina, {}, 'md-modal' )
  }

}