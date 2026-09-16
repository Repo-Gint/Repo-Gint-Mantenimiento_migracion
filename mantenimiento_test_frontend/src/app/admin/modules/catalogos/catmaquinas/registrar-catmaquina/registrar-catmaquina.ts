import { CommonModule } from '@angular/common';
import { ChangeDetectorRef, Component, Input, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { ModalService } from '../../../../services/modal/modal';
import { MessagesService } from '../../../../services/messages/messages';
import { CatmaquinasService } from '../../../../services/api/catmaquinas/catmaquinas';

@Component({
  selector: 'app-registrar-catmaquina',
  imports: [CommonModule, ReactiveFormsModule],
  standalone: true,
  templateUrl: './registrar-catmaquina.html',
  styleUrl: './registrar-catmaquina.css',
})
export class RegistrarCatmaquina implements OnInit {
  @Input() pkCatalogoMaquina: any = null;

  protected formCatMaquina!: FormGroup;

  constructor(
    private modal: ModalService,
    private ch: ChangeDetectorRef,
    private fb: FormBuilder,
    private messages: MessagesService,
    private catmaquinas: CatmaquinasService
  ) {}

  async ngOnInit(): Promise<any> {
    this.messages.mensajeEsperar();

    this.crearFormCatMaquinas();

    if (this.pkCatalogoMaquina != null) {
      await this.obtenerDetalleCatalogoMaquina(this.pkCatalogoMaquina);
    }

    this.messages.cerrarMensajes();
  }

  protected crearFormCatMaquinas(): void {
    this.formCatMaquina = this.fb.group({
      cat_machines: [null, [Validators.required, Validators.pattern('^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\\s]+$')]]
    });
  }

  public async obtenerDetalleCatalogoMaquina(pkCatalogoMaquina: number): Promise<any> {
    return this.catmaquinas.obtenerDetalleCatalogoMaquina(pkCatalogoMaquina).toPromise().then(
      respuesta => {
        const catmaquina = respuesta.catmaquinas;
        this.formCatMaquina.get('cat_machines')?.setValue(catmaquina.cat_machines);
      }
    );
  }

  protected registrarCatalogoMaquina(): void {
    if (this.formCatMaquina.invalid) {
      this.messages.mensajeGenerico('Aún hay campos vacíos o que no cumplen con la estructura correcta.',
        'info', 'Los campos requeridos están marcados con un *'
      );
      return;
    }

    this.messages.mensajeConfirmacionCustom('¿Está seguro de continuar con el registro del catalogo máquina?',
      'question', 'Registrar Catálogo Máquina'
    ).then(res => {
      if (!res.isConfirmed) return;
      this.messages.mensajeEsperar();

      const catmaquina: any = this.formCatMaquina.value;

      this.catmaquinas.registrarCatalogoMaquina(catmaquina).toPromise().then(
        respuesta => {
          this.pkCatalogoMaquina = respuesta.pkCatalogoMaquina;
          this.ch.markForCheck();

          this.obtenerDetalleCatalogoMaquina(respuesta.pkCatalogoMaquina).then(() => {
            this.messages.mensajeGenerico(respuesta.mensaje, 'success', respuesta.title);
          });
        }, error => {
          this.messages.mensajeGenerico('error', 'error');
        }
      );
    });
  }

  protected actualizarCatalogoMaquina(): void {
    if (this.formCatMaquina.invalid) {
      this.messages.mensajeGenerico('Aún hay campos vacíos o que no cumplen con la estructura correcta.', 'info', 'Los campos requeridos están marcados con un *');
      return;
    }

    this.messages.mensajeConfirmacionCustom('¿Está seguro de continuar con la actualización del catálogo máquina?',
      'question', 'Actualizar Catálogo Máquina').then(
        res => {
          if (!res.isConfirmed) return;

          this.messages.mensajeEsperar();

          const data: any = {
            pkCatalogoMaquina: this.pkCatalogoMaquina,
            catmaquina: this.formCatMaquina.value
          };

          this.catmaquinas.actualizarCatalogoMaquina(data).toPromise().then(
            respuesta => {
              this.obtenerDetalleCatalogoMaquina(this.pkCatalogoMaquina).then(() => {
                this.messages.mensajeGenerico(respuesta.mensaje, 'success', respuesta.title);
              });
            }, error => {
              this.messages.mensajeGenerico('error', 'error');
            }
          );
        }
      );
  }

  get cambiosForm(): boolean {
    return this.formCatMaquina.dirty;
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
      }
    );
  }
}