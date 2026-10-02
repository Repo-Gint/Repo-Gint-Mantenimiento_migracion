import { CommonModule } from '@angular/common';
import { ChangeDetectorRef, Component, Input } from '@angular/core';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { ModalService } from '../../../../services/modal/modal';
import { MessagesService } from '../../../../services/messages/messages';
import { MaquinasService } from '../../../../services/api/maquinas/maquinas';

@Component({
  selector: 'app-registrar-maquinas',
  imports: [CommonModule, ReactiveFormsModule],
  standalone: true,
  templateUrl: './registrar-maquinas.html',
  styleUrl: './registrar-maquinas.css',
})
export class RegistrarMaquinas {
  @Input() pkMaquina: any = null;

  protected formMaquina!: FormGroup;

  protected listaAreas:             any[] = [];
  protected listacatalogoMaquina:   any[] = [];

  constructor(
    private modal:    ModalService,
    private ch:       ChangeDetectorRef,
    private fb:       FormBuilder,
    private messages: MessagesService,
    private maquinas: MaquinasService,
  ) {}

  async ngOnInit(): Promise<any> {
    this.messages.mensajeEsperar();

    this.crearFormMaquinas();
    await this.obtenerRecursosRegistroMaquina();

    if (this.pkMaquina != null) await this.obtenerDetalleMaquina(this.pkMaquina);

    this.messages.cerrarMensajes();
  }

  protected crearFormMaquinas(): void {
    this.formMaquina = this.fb.group({
      id_area:         ['', [Validators.required]],
      id_cat_machines: ['', [Validators.required]],
      machines:   [null, [Validators.required, Validators.pattern('^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\\s.-]+$')]],
      brand:      [null, [Validators.required, Validators.pattern('^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\\s.-]+$')]],
      model:      [null, [Validators.required, Validators.pattern('^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\\s.-]+$')]],
      serial:     [null, [Validators.required, Validators.pattern('^[a-zA-Z0-9-]+$')]],
      year:       [null, [Validators.required, Validators.pattern('^[0-9]{4}$')]],
      weight:     [null, [Validators.required, Validators.pattern('^[0-9]+(\\.[0-9]+)?$')]],
      voltaje:    [null, [Validators.required, Validators.pattern('^[0-9]+(\\.[0-9]+)?$')]],
      amperage:   [null, [Validators.required, Validators.pattern('^[0-9]+(\\.[0-9]+)?$')]],
      frequency:  [null, [Validators.required, Validators.pattern('^[0-9]+(\\.[0-9]+)?$')]],
      kva:        [null, [Validators.required, Validators.pattern('^[0-9]+(\\.[0-9]+)?$')]],
    });
  }

  public async obtenerRecursosRegistroMaquina(): Promise<any> {
    try {
      const respuesta: any = await this.maquinas.obtenerRecursosRegistroMaquina().toPromise();
      const recursos = respuesta?.recursos || respuesta;

      this.listaAreas             = recursos?.listaareas || recursos?.lista_areas || [];
      this.listacatalogoMaquina = recursos?.listacatalogomaquina || recursos?.listacatmachines || recursos?.lista_catalogo_maquina || [];
      
      this.ch.detectChanges();
    } catch (error) {
      console.error('Error al obtener recursos:', error);
      this.messages.mensajeGenerico('error', 'error');
    }
  }

  public async obtenerDetalleMaquina(pkMaquina: number): Promise<any> {
    return this.maquinas.obtenerDetalleMaquina(pkMaquina).toPromise().then(
      respuesta => {
        const maquina = respuesta.maquinas;

        this.formMaquina.get('id_area')?.setValue(maquina.id_area);
        this.formMaquina.get('id_cat_machines')?.setValue(maquina.id_cat_machines);
        this.formMaquina.get('machines')?.setValue(maquina.machines);
        this.formMaquina.get('brand')?.setValue(maquina.brand);
        this.formMaquina.get('model')?.setValue(maquina.model);
        this.formMaquina.get('year')?.setValue(maquina.year);
        this.formMaquina.get('serial')?.setValue(maquina.serial);
        this.formMaquina.get('weight')?.setValue(maquina.weight);
        this.formMaquina.get('voltaje')?.setValue(maquina.voltaje);
        this.formMaquina.get('amperage')?.setValue(maquina.amperage);
        this.formMaquina.get('frequency')?.setValue(maquina.frequency);
        this.formMaquina.get('kva')?.setValue(maquina.kva);
      }
    );
  }

  protected registrarMaquina(): void {
    if (this.formMaquina.invalid) {
      this.messages.mensajeGenerico('Aún hay campos vacíos o que no cumplen con la estructura correcta.',
        'info', 'Los campos requeridos están marcados con un *'
      );
      return;
    }

    this.messages.mensajeConfirmacionCustom('¿Está seguro de continuar con el registro de la máquina?',
        'question', 'Registrar máquina'
      ).then(res => {
        if (!res.isConfirmed) return;
        this.messages.mensajeEsperar();

        const maquina: any = this.formMaquina.value;

        this.maquinas.registrarMaquina(maquina).toPromise().then(
          respuesta => {
            this.pkMaquina = respuesta.pkMaquina;
            this.ch.markForCheck();

            this.obtenerDetalleMaquina(respuesta.pkMaquina).then(() => {
              this.messages.mensajeGenerico(respuesta.mensaje, 'success', respuesta.title);
            });
          }, error => {
            this.messages.mensajeGenerico('error', 'error');
          }
        );
      });
  }

  protected actualizarMaquina(): void {
    if (this.formMaquina.invalid) {
      this.messages.mensajeGenerico('Aún hay campos vacíos o que no cumplen con la estructura correcta.', 'info', 'Los campos requeridos están marcados con un *');
      return;
    }

    this.messages.mensajeConfirmacionCustom('¿Está seguro de continuar con la actualización de la máquina?',
        'question', 'Actualizar máquina').then(
          res => {
            if (!res.isConfirmed) return;

            this.messages.mensajeEsperar();

            const data: any = {
              pkMaquina: this.pkMaquina,
              maquina: this.formMaquina.value
            };

            this.maquinas.actualizarMaquina(data).toPromise().then(
              respuesta => {
                this.obtenerDetalleMaquina(this.pkMaquina).then(() => {
                  this.messages.mensajeGenerico(respuesta.mensaje, 'success', respuesta.title)
                });
              }, error => {
                this.messages.mensajeGenerico('error', 'error')
              }
            );
          }
        )
  }

  get cambiosForm(): boolean {
    return this.formMaquina.dirty;
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
    )
  }
}