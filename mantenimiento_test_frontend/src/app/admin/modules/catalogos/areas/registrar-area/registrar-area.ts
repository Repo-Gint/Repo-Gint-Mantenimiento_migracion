import { CommonModule } from '@angular/common';
import { ChangeDetectorRef, Component, Input, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { ModalService } from '../../../../services/modal/modal';
import { MessagesService } from '../../../../services/messages/messages';
import { AreasService } from '../../../../services/api/areas/areas';

@Component({
    selector: 'app-registrar-area',
    standalone: true,
    imports: [CommonModule, ReactiveFormsModule],
    templateUrl: './registrar-area.html',
    styleUrl: './registrar-area.css',
})
export class RegistrarArea implements OnInit {
    @Input() pkArea: any = null;

    protected formArea!: FormGroup;

    constructor(
        private modal: 	  ModalService,
        private ch:    	  ChangeDetectorRef,
        private fb:    	  FormBuilder,
        private messages: MessagesService,
        private areas:    AreasService
    ) { }

    async ngOnInit(): Promise<any> {
        this.messages.mensajeEsperar();
        this.crearFormAreas();
        if (this.pkArea != null) await this.obtenerDetalleArea(this.pkArea);
        this.messages.cerrarMensajes();
    }

    protected crearFormAreas(): void {
        this.formArea = this.fb.group({
            area: 	 [null, [Validators.required, Validators.pattern('^[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9 -]+$')]],
            acronym: [null, [Validators.required, Validators.pattern('^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+$')]],
            color: 	 [null, [Validators.required, Validators.pattern('^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$')]],
        });
    }

    // Auxiliar para selector de color
    protected seleccionarColor(colorHex: string): void {
        this.formArea.get('color')?.setValue(colorHex);
        this.formArea.get('color')?.markAsTouched();
        this.formArea.get('color')?.markAsDirty();
    }

    // Comprueba si un campo es inválido
    protected campoEsInvalido(nombreCampo: string): boolean {
        const campo = this.formArea.get(nombreCampo);
        return !!(campo && campo.invalid && (campo.touched || campo.dirty));
    }

    // Comprueba si un campo es válido (para mostrar la palomita verde)
    protected campoEsValido(nombreCampo: string): boolean {
        const campo = this.formArea.get(nombreCampo);
        return !!(campo && campo.valid && (campo.touched || campo.dirty));
    }

    // Mensaje dinámico de error según la regla fallida
    protected obtenerMensajeError(nombreCampo: string): string {
        const campo = this.formArea.get(nombreCampo);
        if (!campo || !campo.errors) return '';

        if (campo.errors['required']) return 'Este campo es obligatorio.';
        if (campo.errors['pattern']) {
            if (nombreCampo === 'color') return 'Formato HEX inválido (Ej: #0abfec).';
            return 'Contiene caracteres no permitidos.';
        }
        return 'Campo inválido.';
    }

    // Genera la lista de los campos incompletos para la alerta
    private obtenerCamposInvalidosTexto(): string {
        const camposNombres: { [key: string]: string } = {
            area:    'Área',
            acronym: 'Abreviatura (Acronym)',
            color:   'Color'
        };

        const pendientes: string[] = [];
        Object.keys(this.formArea.controls).forEach(key => {
            const control = this.formArea.get(key);
            if (control && control.invalid) {
                pendientes.push(camposNombres[key] || key);
            }
        });

        return pendientes.length > 0 ? pendientes.join(', ') : '';
    }

    public async obtenerDetalleArea(PkArea: number): Promise<any> {
        return this.areas.obtenerDetalleArea(PkArea).toPromise().then(
            respuesta => {
                const area = respuesta.area;
                this.formArea.get('area')?.setValue(area.area);
                this.formArea.get('acronym')?.setValue(area.acronym);
                this.formArea.get('color')?.setValue(area.color);
            }
        );
    }

    protected registrarArea(): void {
        if (this.formArea.invalid) {
            this.formArea.markAllAsTouched();
            const camposFaltantes = this.obtenerCamposInvalidosTexto();

            this.messages.mensajeGenerico(`Por favor verifica los siguientes campos: ${camposFaltantes}.`, 'info', 'Campos incompletos o incorrectos'
            );
            return;
        }

        this.messages
            .mensajeConfirmacionCustom('¿Está seguro de continuar con el registro del área?', 'question', 'Registrar área'
            ).then(res => {
                if (!res.isConfirmed) return;
                this.messages.mensajeEsperar();

                const area: any = this.formArea.value;

                this.areas.registrarArea(area).toPromise().then(
                    respuesta => {
                        this.pkArea = respuesta.pkArea;
                        this.ch.markForCheck();

                        this.obtenerDetalleArea(respuesta.pkArea).then(() => {
                            this.messages.mensajeGenerico(respuesta.mensaje, 'success', respuesta.title);
                        });

                    }, error => {
                        this.messages.mensajeGenerico('error', 'error');
                    }
                );
            });
    }

    protected actualizarArea(): void {
        if (this.formArea.invalid) {
            this.formArea.markAllAsTouched();
            const camposFaltantes = this.obtenerCamposInvalidosTexto();

            this.messages.mensajeGenerico(`Por favor verifica los siguientes campos: ${camposFaltantes}.`, 'info', 'Campos incompletos o incorrectos'
            );
            return;
        }

        this.messages.mensajeConfirmacionCustom('¿Está seguro de continuar con la actualización del area?',
            'question', 'Actualizar area').then(
                res => {
                    if (!res.isConfirmed) return;

                    this.messages.mensajeEsperar();

                    const data: any = {
                        pkArea: this.pkArea,
                        area: this.formArea.value
                    };

                    this.areas.actualizarArea(data).toPromise().then(
                        respuesta => {
                            this.obtenerDetalleArea(this.pkArea).then(() => {
                                this.messages.mensajeGenerico(respuesta.mensaje, 'success', respuesta.title);
                            });
                        }, error => {
                            this.messages.mensajeGenerico('error', 'error');
                        }
                    )
                }
            );
    }

    get cambiosForm(): boolean {
        return this.formArea.dirty;
    }

    public cerrarModal(): void {
        if (!this.cambiosForm) {this.modal.cerrarModal();
            return;
        }

        this.messages.mensajeConfirmacionCustom('¿Está seguro de cerrar sin guardar cambios?', 'question', 'Cancelar registro'
        ).then(
            res => {
                if (!res.isConfirmed) return;
                this.modal.cerrarModal();
            }
        )
    }
}