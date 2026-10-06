import { CommonModule } from '@angular/common';
import { ChangeDetectorRef, Component, ElementRef, HostListener, Input, OnInit, ViewChild } from '@angular/core';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { ModalService } from '../../../services/modal/modal';
import { MessagesService } from '../../../services/messages/messages';
import { OrdenesService } from '../../../services/api/ordenes/ordenes';
import { UsuariosService } from '../../../services/api/usuarios/usuarios';
import { AsignarOrden } from '../asignar-orden/asignar-orden';

@Component({
  selector: 'app-registrar-orden',
  imports: [CommonModule, ReactiveFormsModule],
  standalone: true,
  templateUrl: './registrar-orden.html',
  styleUrl: './registrar-orden.css',
})
export class RegistrarOrden implements OnInit {
  @Input() pkOrden: any = null;
  @ViewChild('problemTextarea') problemTextarea!: ElementRef<HTMLTextAreaElement>;

  protected formOrden!: FormGroup;
  protected esAdminOTecnico: boolean = false;
  protected puedeEditar: boolean = false;

  protected listaAreas:           any[] = [];
  protected listaMaquinas:        any[] = [];
  protected listaDepartamentos:   any[] = [];
  protected listaEmpleados:       any[] = [];
  protected listatipoMantenimiento: any[] = [];
  protected listatipoOrden:       any[] = [];
  protected listaprioridadOrden:  any[] = [];
  protected listacatalogoMaquina: any[] = [];

  protected usuariosAsignados: string[] = [];

  images: any[] = [];
  files: File[] = [];

  protected imagenSeleccionadaZoom: string | null = null;
  protected isDragging: boolean = false;

  // Control de menú desplegable activo
  protected dropdownAbierto: string | null = null;

  constructor(
    private modal:    ModalService,
    private messages: MessagesService,
    private ordenes:  OrdenesService,
    private usuarios: UsuariosService,
    private ch:       ChangeDetectorRef,
    private fb:       FormBuilder
  ) {}

  async ngOnInit(): Promise<void> {
    this.messages.mensajeEsperar();
    try {
      this.crearFormOrden();
      
      await this.verificarPermisosDesdeApi();
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
      console.error('Error en ngOnInit:', error);
      this.messages.mensajeGenerico('Ocurrió un error al cargar la orden', 'error');
    } finally {
      this.messages.cerrarMensajes();
      this.ch.detectChanges();
    }
  }

  // --- Métodos para Desplegables 3D ---
  public toggleDropdown(nombreMenu: string): void {
    if (this.pkOrden !== null && !this.puedeEditar) return;
    this.dropdownAbierto = this.dropdownAbierto === nombreMenu ? null : nombreMenu;
    this.ch.detectChanges();
  }

  public seleccionarOpcion(campoControl: string, valorId: any): void {
    this.formOrden.get(campoControl)?.setValue(valorId);
    this.formOrden.get(campoControl)?.markAsDirty();
    this.dropdownAbierto = null;
    this.ch.detectChanges();
  }

  public obtenerNombreSeleccionado(campoControl: string, lista: any[], campoId: string, campoNombre: string): string {
    const valorActual = this.formOrden.get(campoControl)?.value;
    if (!valorActual || !lista || lista.length === 0) return '';
    const encontrado = lista.find(item => Number(item[campoId]) === Number(valorActual));
    return encontrado ? encontrado[campoNombre] : '';
  }

  @HostListener('document:click', ['$event'])
  public cerrarMenusAlHacerClicFuera(event: Event): void {
    const target = event.target as HTMLElement;
    if (!target.closest('.custom-dropdown-container')) {
      this.dropdownAbierto = null;
      this.ch.detectChanges();
    }
  }

  // --- Autoajuste del Textarea ---
  public adjustTextareaHeight(event: Event): void {
    const textarea = event.target as HTMLTextAreaElement;
    if (textarea) {
      textarea.style.height = 'auto';
      textarea.style.height = (textarea.scrollHeight + 2) + 'px';
    }
  }

  private adjustTextareaFromElement(el: HTMLTextAreaElement): void {
    if (el) {
      el.style.height = 'auto';
      el.style.height = (el.scrollHeight + 2) + 'px';
    }
  }

  private crearFormOrden(): void {
    this.formOrden = this.fb.group({
      id_type_orders:       ['', Validators.required],
      id_area:              ['', Validators.required],
      id_departaments:      ['', Validators.required],
      id_cat_machines:      ['', Validators.required], 
      id_machines:          ['', Validators.required],
      id_employee:          [''],
      problem_description:  ['', Validators.required], 
      id_type_maintenances: ['', Validators.required],
      id_priority:          ['', Validators.required],
      order_folio:          ['ORD-GEN-M'],
      idUsuario:            [[]]
    });
  }

  private async verificarPermisosDesdeApi(): Promise<void> {
    try {
      const respuesta: any = await this.usuarios.obtenerPermisosUsuarioActual().toPromise();
      const permisos: string[] = respuesta?.permisos || [];
      const rolUsuario = Number(respuesta?.id_rol_users || 1);

      this.esAdminOTecnico = (rolUsuario === 2 || rolUsuario === 3);
      this.puedeEditar = this.esAdminOTecnico || permisos.includes('editar_orden');

      if (this.pkOrden !== null && !this.puedeEditar) {
        this.formOrden.disable();
      } else {
        this.formOrden.enable();
      }
    } catch (error) {
      this.puedeEditar = false;
      if (this.pkOrden === null) {
        this.formOrden.enable();
      }
    } finally {
      this.ch.detectChanges();
    }
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
    if (!pkOrden || String(pkOrden) === 'undefined') {
      console.warn('Advertencia: ID de orden inválido.');
      return;
    }

    try {
      const respuesta: any = await this.ordenes.obtenerDetalleOrden(pkOrden).toPromise();
      const orden = respuesta.orden || respuesta;
      const evidencias = respuesta.evidencias || [];

      const asignados = respuesta.usuariosAsignados || respuesta.usuarios_asignados || orden?.usuariosAsignados || [];
      
      if (Array.isArray(asignados)) {
        this.usuariosAsignados = asignados.map((u: any) => typeof u === 'string' ? u : (u.Name || u.name || ''));
      } else {
        this.usuariosAsignados = [];
      }

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

      this.images = Array.isArray(evidencias) ? evidencias.map((img: any) => ({
        id_eviden_order: img.id_eviden_order || 0,
        url_evidence_image: img.url_evidence_image || img.url || '',
        nombreArchivo: 'Evidencia_Guardada.jpg',
        tamanoTexto: 'Sincronizado',
        progreso: 100
      })) : [];
      
      this.ch.detectChanges();

      setTimeout(() => {
        if (this.problemTextarea && this.problemTextarea.nativeElement) {
          this.adjustTextareaFromElement(this.problemTextarea.nativeElement);
        }
      }, 50);

    } catch (error) {
      console.error('Error al obtener detalle de orden:', error);
      this.messages.mensajeGenerico('Error al obtener el detalle de la orden', 'error');
    }
  }

  public async obtenerRecursosRegistroOrden(): Promise<void> {
    const respuesta: any = await this.ordenes.obtenerRecursosRegistroOrden().toPromise();
    const recursos = respuesta?.recursos || respuesta;

    this.listaAreas              = Array.isArray(recursos?.listaareas) ? recursos.listaareas : [];
    this.listaMaquinas           = Array.isArray(recursos?.listamaquinas) ? recursos.listamaquinas : [];
    this.listaDepartamentos      = Array.isArray(recursos?.listadepartamentos) ? recursos.listadepartamentos : [];
    this.listaEmpleados          = Array.isArray(recursos?.listaempleados) ? recursos.listaempleados : [];
    this.listatipoMantenimiento  = Array.isArray(recursos?.listatipomantenimiento) ? recursos.listatipomantenimiento : [];
    this.listatipoOrden          = Array.isArray(recursos?.listatipoorden) ? recursos.listatipoorden : [];
    this.listaprioridadOrden     = Array.isArray(recursos?.listaprioridadorden) ? recursos.listaprioridadorden : [];
    this.listacatalogoMaquina    = Array.isArray(recursos?.listacatalogomaquina) ? recursos.listacatalogomaquina : [];
    
    this.ch.detectChanges();
  }

  // --- Handlers Drag & Drop ---
  protected onDragOver(event: DragEvent): void {
    event.preventDefault();
    event.stopPropagation();
    this.isDragging = true;
  }

  protected onDragLeave(event: DragEvent): void {
    event.preventDefault();
    event.stopPropagation();
    this.isDragging = false;
  }

  protected onDrop(event: DragEvent): void {
    event.preventDefault();
    event.stopPropagation();
    this.isDragging = false;
    if (event.dataTransfer && event.dataTransfer.files.length > 0) {
      this.procesarArchivos(Array.from(event.dataTransfer.files));
    }
  }

  protected onFileSelected(event: any): void {
    if (this.pkOrden !== null && !this.puedeEditar) return;
    const selectedFiles = event.target.files;
    if (selectedFiles && selectedFiles.length > 0) {
      this.procesarArchivos(Array.from(selectedFiles));
    }
    event.target.value = '';
  }

  private procesarArchivos(archivos: File[]): void {
    for (let file of archivos) {
      if (!file.type.startsWith('image/')) continue;

      this.files.push(file);

      const fileSizeMb = (file.size / (1024 * 1024)).toFixed(2) + ' Mb';
      const reader = new FileReader();

      reader.onload = (e: any) => {
        const nuevaImagen = {
          id_eviden_order: 0,
          url_evidence_image: e.target.result,
          nombreArchivo: file.name,
          tamanoTexto: fileSizeMb,
          progreso: 0
        };

        this.images.push(nuevaImagen);
        this.ch.detectChanges();

        const interval = setInterval(() => {
          nuevaImagen.progreso += 10;
          this.ch.detectChanges();

          if (nuevaImagen.progreso >= 100) {
            clearInterval(interval);
            nuevaImagen.progreso = 100;
            this.ch.detectChanges();
            this.messages.mensajeGenerico(`Se subió con éxito tu imagen "${file.name}"`, 'success', 'Archivo Adjuntado');
          }
        }, 60);
      };

      reader.readAsDataURL(file);
    }
  }

  protected eliminarEvidenciaOrden(index: number, id_eviden_order: number): void {
    if (this.pkOrden !== null && !this.puedeEditar) return;

    this.messages.mensajeConfirmacionCustom(
      '¿Está seguro de eliminar la evidencia del orden?', 
      'question', 
      'Eliminar evidencia'
    ).then(res => {
      if (!res.isConfirmed) return;

      if (!id_eviden_order || id_eviden_order === 0) {
        this.images.splice(index, 1);
        this.files.splice(index, 1);
        this.ch.detectChanges();
        return;
      }

      this.messages.mensajeEsperar();
      this.ordenes.eliminarEvidenciaOrden(id_eviden_order).toPromise().then(
        (respuesta: any) => {
          const idDetalle = respuesta?.pkOrden || respuesta?.id_order || this.pkOrden;

          if (idDetalle) {
            this.obtenerDetalleOrden(idDetalle).then(() => {
              this.images.splice(index, 1);
              this.files.splice(index, 1);
              this.ch.detectChanges();
              this.messages.cerrarMensajes();
            });
          } else {
            this.images.splice(index, 1);
            this.ch.detectChanges();
            this.messages.cerrarMensajes();
          }
        }, 
        error => {
          console.error('Error al eliminar evidencia:', error);
          this.messages.mensajeGenerico('Ocurrió un error al eliminar la evidencia.', 'error');
        }
      );
    });
  }

  protected registrarOrden(): void {
    if (this.formOrden.invalid) {
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
          const targetId = respuesta?.pkOrden || respuesta?.id_order;
          if (targetId) {
            this.obtenerDetalleOrden(targetId).then(() => {
              this.messages.mensajeGenerico(respuesta.mensaje, 'success', respuesta.title);
            });
          } else {
            this.messages.mensajeGenerico(respuesta?.mensaje || 'Registrado con éxito', 'success');
          }
          this.modal.cerrarModal();
        },
        error => {
          console.error(error);
          this.messages.mensajeGenerico('Ocurrió un error al registrar la orden', 'error');
        }
      );
    });
  }

  protected actualizarOrden(): void {
    if (!this.puedeEditar) {
      this.messages.mensajeGenerico(
        'No tienes el permiso del administrador, contacta al administrador para que te lo autorice.',
        'error',
        'Acceso denegado'
      );
      return;
    }

    if (this.formOrden.invalid) {
      this.messages.mensajeGenerico(
        'Aún hay campos vacíos o que no cumplen con la estructura correcta.',
        'info', 
        'Los campos requeridos están marcados con un *'
      );
      return;
    }

    this.messages.mensajeConfirmacionCustom(
      '¿Está seguro de continuar con la actualización del orden?',
      'question', 
      'Actualizar orden'
    ).then(res => {
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
          const id = respuesta?.pkOrden || respuesta?.id_order || this.pkOrden;
          if (!id) {
            this.messages.mensajeGenerico('No se pudo obtener el ID del orden.', 'error');
            return;
          }

          this.obtenerDetalleOrden(id).then(() => {
            this.messages.mensajeGenerico(respuesta?.mensaje || respuesta?.mensajes || 'Actualizado con éxito', 'success', respuesta?.title || 'Éxito');
          });
        },
        error: (error) => {
          const mensajeTexto = error?.error?.mensaje || 'Ocurrió un error al actualizar el orden.';
          const tituloTexto = error?.error?.title || 'Acceso denegado';
          this.messages.mensajeGenerico(mensajeTexto, 'error', tituloTexto);
        }
      });
    });
  }

  protected abrirAsignar(): void {
    if (!this.puedeEditar) return;

    this.modal.abrirModalConComponente(AsignarOrden, {
      pkOrden: this.pkOrden, 
      folio: `ORD-${this.pkOrden}`, 
      onAsignacionExitosa: () => {
        setTimeout(() => {
          this.obtenerDetalleOrden(this.pkOrden);
        }, 0);
      }
    }, 'md-modal');
  }

  protected abrirPreviewImagen(url: string): void {
    this.imagenSeleccionadaZoom = url;
    this.ch.detectChanges();
  }

  protected cerrarPreviewImagen(): void {
    this.imagenSeleccionadaZoom = null;
    this.ch.detectChanges();
  }

  get cambiosForm(): boolean {
    return this.formOrden.dirty || this.files.length > 0;
  }

  public cerrarModal(): void {
    if (!this.cambiosForm) {
      this.modal.cerrarModal();
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