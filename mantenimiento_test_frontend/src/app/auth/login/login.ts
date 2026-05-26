import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { MessagesService } from '../../admin/services/messages/messages';
import { LoginService } from '../services/login/login';

@Component({
  selector: 'app-login',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule],
  templateUrl: './login.html',
  styleUrl: './login.css',
})
export class Login implements OnInit {
  protected formLogin!: FormGroup;

  constructor (
    private fb: FormBuilder, 
    private loginService: LoginService,
    private router: Router,
    private messages: MessagesService
  ) {}

  ngOnInit(): void {
    this.crearFormLogin();
  }

  private crearFormLogin(): void {
    this.formLogin = this.fb.group({
      busines_mail:   [null, [Validators.required,Validators.email,Validators.pattern('[a-zA-Zá-úÁ-Ú0-9 .,-_:@#$%&+{}()?¿!¡\n]*')]],
			password: [null, [Validators.required,Validators.pattern('[a-zA-Zá-úÁ-Ú0-9 .,-_:@#$%&+{}()?¿!¡\n]*')]]
    });
  }

  protected iniciarSesion(): void {
    if (this.formLogin.invalid) {
      this.messages.mensajeGenerico(
        'Aún hay campos vacíos o que no cumplen con la estructura correcta',
        'info', 
        'Los campos requeridos están marcados con un *'
      );
      return;
    }

    const credenciales: any = {
      busines_mail: this.formLogin.value.busines_mail, 
      password: this.formLogin.value.password,
    };

    this.messages.mensajeEsperar();

    this.messages.mensajeEsperar();

    this.loginService.login(credenciales).toPromise().then(
      (respuesta: any) => {
        if(respuesta.success === 204 || respuesta.success === false) {
          this.messages.mensajeGenerico(
            respuesta.mensaje, 
            'warning', 
            respuesta.title
          );
          return;
        }

        if (respuesta.token) {
          localStorage.setItem('token_orden_gitlatam', respuesta.token);
        }

        this.messages.cerrarMensajes();
        this.router.navigate(['/']);
      },

      error=> {
        this.messages.cerrarMensajes();
        this.messages.mensajeGenerico(
          'Ocurrio un error al Iniciar Sessión',
          'error'
        );
      }
    );
  }
}
