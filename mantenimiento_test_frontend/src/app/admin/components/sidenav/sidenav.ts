import { Component } from '@angular/core';
import { RouterLink } from '@angular/router';
import { ModalService } from '../../services/modal/modal';
import { RegistrarUsuario } from '../../modules/usuarios/registrar-usuario/registrar-usuario';
import { RegistrarArea } from '../../modules/catalogos/areas/registrar-area/registrar-area';
import { RegistrarDepartamento } from '../../modules/catalogos/departamentos/registrar-departamento/registrar-departamento';

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
  ){}

public abrirModalRegistrarUsuario(): void {
		this.modal.abrirModalConComponente(RegistrarUsuario, {}, 'lg-modal');
	}

  public abrirModalRegistrarArea(): void {
		this.modal.abrirModalConComponente(RegistrarArea, {}, 'md-modal');
	}

  public abrirModalRegistrarDepartamento(): void {
    this.modal.abrirModalConComponente(RegistrarDepartamento, {}, 'md-moodal')
  }
}