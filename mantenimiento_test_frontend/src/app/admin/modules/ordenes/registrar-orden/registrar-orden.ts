import { CommonModule } from '@angular/common';
import { ChangeDetectorRef, Component, Input } from '@angular/core';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { ModalService } from '../../../services/modal/modal';
import { MessagesService } from '../../../services/messages/messages';
import { OrdenesService } from '../../../services/api/ordenes/ordenes';
import { AsignarOrden } from '../asignar-orden/asignar-orden';

@Component({
  selector: 'app-registrar-orden',
  imports: [CommonModule, ReactiveFormsModule],
  standalone: true,
  templateUrl: './registrar-orden.html',
  styleUrl: './registrar-orden.css',
})
export class RegistrarOrden {
  @Input() pkOrden: any = null;

  protected formOrden!: FormGroup;

  protected listaAreas:             any[] = [];
  protected listaMaquinas:          any[] = [];
  protected listaDepartamentos:     any[] = [];
  protected listaEmpleados:         any[] = [];
  protected listatipoMantenimiento: any[] = [];
  protected listatipoOrden:         any[] = [];
  protected listaprioridadOrden:    any[] = [];
  protected listacatalogoMaquina:   any[] = [];

  protected usuariosAsignados: any[] = [];

  images: any[] = [];
  files: File[] = [];

  constructor(
    private modal:    ModalService,
    private messages: MessagesService,
    private ordenes:  OrdenesService,
    private ch:       ChangeDetectorRef,
    private fb:       FormBuilder
  ) {}

  async ngOnInit(): Promise<void> {
    this.messages.mensajeEsperar();
    try {this.crearFormOrden();
      await this.obtenerRecursosRegistroOrden();
      this.formOrden.get('id_area')?.valueChanges.subscribe(() => {
        this.cargarMaquinasFiltradas();
      });

      this.formOrden.get('id_cat_machines')?.valueChanges.subscribe(() => {
        this.cargarMaquinasFiltradas();
      });

      if (this.pkOrden != null) {
        await this.obtenerDetalleOrden(this.pkOrden);
      }
    } catch (error) {
      this.messages.mensajeGenerico('error', 'error');
    } finally {

      this.messages.cerrarMensajes();
    }
  }

  private crearFormOrden(): void {
    this.formOrden = this.fb.group({
      id_type_orders:       ['', Validators.required],
      id_area:              ['', Validators.required],
      id_departaments:      ['', Validators.required],
      id_cat_machines:      ['', Validators.required], 
      id_machines:          ['', Validators.required],
      id_employee:          ['', Validators.required],
      problem_description:  ['', Validators.required], 
      id_type_maintenances: ['', Validators.required],
      id_priority:          ['', Validators.required],
      order_folio:          ['ORD-GEN-M'],
      idUsuario: [[]]
    });
  }

  private cargarMaquinasFiltradas(limpiarSeleccion: boolean = true): void {
    const idArea = this.formOrden.get('id_area')?.value;
    const idCatMaquina = this.formOrden.get('id_cat_machines')?.value;

    if (!idArea || !idCatMaquina) {
      this.listaMaquinas = [];
      if (limpiarSeleccion) {
        this.formOrden.get('id_machines')?.setValue('');
      }
      return;
    }

    this.ordenes.obtenerMaquinasPorAreaYCategoria(idArea, idCatMaquina).toPromise().then(
      (respuesta: any) => {
        const datos = respuesta?.listamaquinas || respuesta?.maquinas || respuesta?.data || respuesta;
        this.listaMaquinas = Array.isArray(datos) ? datos : [];
        this.ch.detectChanges();
      },
      error => {
        this.listaMaquinas = [];
        this.ch.detectChanges();
      }
    );
  }

  public async obtenerDetalleOrden(pkOrden: number): Promise<void> {
    try {
      const respuesta: any = await this.ordenes.obtenerDetalleOrden(pkOrden).toPromise();
      const orden = respuesta.orden || respuesta;
      const evidencias = respuesta.evidencias || [];

      const idArea = orden.id_area || orden.id_areas;
      const idCatMaquina = orden.id_cat_machines || orden.id_cat_machine || orden.id_catalogo_maquina;

      if (idArea && idCatMaquina) {
        try {
          const res: any = await this.ordenes.obtenerMaquinasPorAreaYCategoria(idArea, idCatMaquina).toPromise();
          const datos = res?.listamaquinas || res?.maquinas || res?.data || res;
          this.listaMaquinas = Array.isArray(datos) ? datos : [];
        } catch (error) {
          this.listaMaquinas = [];
        }
      }

      this.formOrden.patchValue({
        id_area: idArea,
        id_type_orders: orden.id_type_orders,
        id_cat_machines: idCatMaquina,
        id_machines: orden.id_machines,
        id_priority: orden.id_priority,
        id_employee: orden.id_employee,
        id_departaments: orden.id_departaments,
        id_type_maintenances: orden.id_type_maintenances,
        problem_description: orden.problem_description
      }, { emitEvent: false });


      this.images = evidencias;
      this.ch.detectChanges();
    } catch (error) {this.modal.cerrarModal();
      this.messages.mensajeGenerico('error', 'error');
    }
  }

  public async obtenerRecursosRegistroOrden(): Promise<void> {
    const respuesta: any = await this.ordenes.obtenerRecursosRegistroOrden().toPromise();
    const recursos = respuesta?.recursos || respuesta;

    this.listaAreas             = Array.isArray(recursos?.listaareas) ? recursos.listaareas : [];
    this.listaMaquinas          = Array.isArray(recursos?.listamaquinas) ? recursos.listamaquinas : [];
    this.listaDepartamentos     = Array.isArray(recursos?.listadepartamentos) ? recursos.listadepartamentos : [];
    this.listaEmpleados         = Array.isArray(recursos?.listaempleados) ? recursos.listaempleados : [];
    this.listatipoMantenimiento = Array.isArray(recursos?.listatipomantenimiento) ? recursos.listatipomantenimiento : [];
    this.listatipoOrden         = Array.isArray(recursos?.listatipoorden) ? recursos.listatipoorden : [];
    this.listaprioridadOrden    = Array.isArray(recursos?.listaprioridadorden) ? recursos.listaprioridadorden : [];
    this.listacatalogoMaquina   = Array.isArray(recursos?.listacatalogomaquina) ? recursos.listacatalogomaquina : [];
    
    this.ch.detectChanges();
  }

  protected onFileSelected(event: any) {
    const selectedFiles = event.target.files;

    for (let file of selectedFiles) {
      if (!file.type.startsWith('image/')) continue;

      this.files.push(file);

      const reader = new FileReader();
      reader.onload = (e: any) => {
        this.images.push({id_eviden_order: 0,
          url_evidence_image: e.target.result
        });
        this.ch.detectChanges();
      };
      reader.readAsDataURL(file);
    }
    event.target.value = '';
  }

  protected eliminarEvidenciaOrden(index: number, id_eviden_order: number): void {
    this.messages.mensajeConfirmacionCustom(
      '¿Está seguro de eliminar la evidencia del orden?', 'question', 'Eliminar evidencia'
    ).then(
      res => {
        if (!res.isConfirmed) return;

        this.messages.mensajeEsperar();
        this.ordenes.eliminarEvidenciaOrden(id_eviden_order).toPromise().then(
          respuesta => {
            this.obtenerDetalleOrden(respuesta.pkOrden).then(() => {
              this.images.splice(index, 1);
              this.files.splice(index, 1);
              this.ch.detectChanges();

              this.messages.cerrarMensajes();
            });
          }, error => {
            this.messages.mensajeGenerico('error', 'error');
          }
        );
      }
    );
  }

  protected registrarOrden(): void {
    if (!this.pkOrden && this.formOrden.invalid) {
      this.messages.mensajeGenerico('Aún hay campos vacíos o inválidos.', 'info', 'Campos requeridos');
      return;
    }

    this.messages.mensajeConfirmacionCustom(
      '¿Está seguro de registrar la orden con evidencias?', 'question', 'Registrar orden'
    ).then(res => {
      if (!res.isConfirmed) return;

      this.messages.mensajeEsperar();

      const formData = new FormData();

      Object.keys(this.formOrden.value).forEach(key => {
						formData.append(key, this.formOrden.value[key]);
					});

      this.files.forEach(file => {
        formData.append('images[]', file);
      });

      this.ordenes.registrarOrden(formData).toPromise().then(
        (respuesta: any) => {
          this.obtenerDetalleOrden(respuesta.pkOrden).then(() => {
            this.messages.mensajeGenerico(respuesta.mensaje, 'success', respuesta.title);
          });
          this.modal.cerrarModal();
        },
        error => {
          this.messages.mensajeGenerico('error', 'error');
        }
      );
    });
  }

  protected actualizarOrden(): void {
    if (this.formOrden.invalid) {
      this.messages.mensajeGenerico('Aún hay campos vacíos o que no cumplen con la estructura correcta.',
        'info', 'Los campos requeridos están marcados con un *'
      );
      return;
    }

    this.messages.mensajeConfirmacionCustom('¿Está seguro de continuar con la actualización del orden?',
      'question', 'Actualizar orden').then(
        res => {
          if (!res.isConfirmed) return;

          this.messages.mensajeEsperar();

          const formData = new FormData();

          Object.keys(this.formOrden.value).forEach(key => {
            formData.append(key, this.formOrden.value[key]);
          });

          formData.append('pkOrden', String(this.pkOrden));

          if (this.files && this.files.length > 0) {
            this.files.forEach(file => {
              formData.append('images[]', file);
            });
          }

          this.ordenes.actualizarOrden(formData).subscribe({
            next: (respuesta: any) => {
              const id = respuesta?.pkOrden || this.pkOrden;
              if (!id) {this.messages.mensajeGenerico('No se pudo obtener el ID del orden.', 'error');
                return;
              }

              this.obtenerDetalleOrden(id).then(() => {
                this.messages.mensajeGenerico(respuesta.mensajes, 'success', respuesta.title);
              });
            },
            error: (error) => {
              this.messages.mensajeGenerico('Ocurrió un error al actualizar el orden.', 'error'
              );
            }
          });
        });
  }

    protected abrirAsignar(): void { this.modal.abrirModalConComponente(AsignarOrden, {pkOrden: this.pkOrden, folio: `ORD-${this.pkOrden}`, onAsignacionExitosa: () => {
          this.obtenerDetalleOrden(this.pkOrden); }}, 'md-modal');
  }
  get cambiosForm(): boolean {
    return this.formOrden.dirty || this.images.length > 0;
  }

  public cerrarModal(): void {
    if (!this.cambiosForm) {this.modal.cerrarModal();
      return;
    }

    this.messages.mensajeConfirmacionCustom(
      '¿Está seguro de cerrar sin guardar cambios?', 'question', 'Cancelar registro'
    ).then(res => {
      if (!res.isConfirmed) return;
      this.modal.cerrarModal();
    });
  }
}