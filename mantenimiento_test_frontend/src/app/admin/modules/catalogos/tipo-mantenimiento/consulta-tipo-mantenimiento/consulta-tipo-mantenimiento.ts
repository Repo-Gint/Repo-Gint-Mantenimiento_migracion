import { CommonModule } from '@angular/common';
import { ChangeDetectorRef, Component } from '@angular/core';
import { ModalService } from '../../../../services/modal/modal';
import { MessagesService } from '../../../../services/messages/messages';
import { RegistrarTipoMantenimiento } from '../registrar-tipo-mantenimiento/registrar-tipo-mantenimiento';
import { TipoMantenimientoService } from '../../../../services/api/tipoMantenimiento/tipo-mantenimiento';

@Component({
	selector: 'app-consulta-tipo-mantenimiento',
	imports: [CommonModule],
	standalone: true,
	templateUrl: './consulta-tipo-mantenimiento.html',
	styleUrl: './consulta-tipo-mantenimiento.css',
})
export class ConsultaTipoMantenimiento {
	protected datosTabla: any = [];
	private intervalo: any;

	constructor(
		private modal: ModalService,
		private ch: ChangeDetectorRef,
		private messages: MessagesService,
		private tipoMantenimientos: TipoMantenimientoService
	) { }

	async ngOnInit(): Promise<any> {
		this.messages.mensajeEsperar();

		await this.obtenerListatipoMantenimientos();
		this.repetitiveInstruction();

		this.messages.cerrarMensajes();
	}

	private repetitiveInstruction(): void {
		this.intervalo = setInterval(() => {
			this.obtenerListatipoMantenimientos();
		}, 10000);
	}

	public async obtenerListatipoMantenimientos(): Promise<any> {
		return this.tipoMantenimientos.obtenerListatipoMantenimientos().toPromise().then(
			respuesta => {
				this.datosTabla = respuesta.tipoMantenimientos;
				this.ch.markForCheck();
			}
		);
	}

	public cambiarStatus(tipoMantenimiento: any): void {
		this.messages.mensajeConfirmacionCustom(
			`¿Está seguro de ${tipoMantenimiento.activo ? 'inactivar' : 'activar'} el tipo de mantenimiento?`,
			'question',
			`${tipoMantenimiento.activo ? 'Inactivar' : 'Activar'} tipo de mantenimiento`
		).then(res => {
			if (!res.isConfirmed) return;

			this.messages.mensajeEsperar();

			this.tipoMantenimientos.cambiarStatustipoMantenimiento(tipoMantenimiento.id_type_maintenances).subscribe(
				respuesta => {
					this.obtenerListatipoMantenimientos().then(() => {
						this.messages.mensajeGenerico(respuesta.mensaje, 'success', respuesta.title);
					});
				}, error => {
					this.messages.mensajeGenerico('error', 'error');
				}
			);
		});
	}

	public abrirModalRegistrotipoMantenimiento(pktipoMantenimiento: number): void {
		const data: any = {
			pktipoMantenimiento: pktipoMantenimiento
		};

		this.modal.abrirModalConComponente(RegistrarTipoMantenimiento, data, 'md-modal');
	}

	ngOnDestroy(): void {
		clearInterval(this.intervalo);
	}
}
