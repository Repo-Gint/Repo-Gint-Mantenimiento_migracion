import { CommonModule } from '@angular/common';
import { ChangeDetectorRef, Component, OnDestroy } from '@angular/core';
import { AreasService } from '../../../../services/api/areas/areas';
import { ModalService } from '../../../../services/modal/modal';
import { RegistrarArea } from '../registrar-area/registrar-area';
import { MessagesService } from '../../../../services/messages/messages';

@Component({
	selector: 'app-consulta-areas',
	imports: [CommonModule],
	standalone: true,
	templateUrl: './consulta-areas.html',
	styleUrl: './consulta-areas.css',
})
export class ConsultaAreas implements OnDestroy {
	protected datosTabla: any = [];

	private intervalo: any;

	constructor(
		private modal:    ModalService,
		private areas:    AreasService,
		private messages: MessagesService,
		private ch:       ChangeDetectorRef
	) { }

	async ngOnInit(): Promise<any> {
		this.messages.mensajeEsperar();

		await this.obtenerListaAreas();
		this.repetitiveInstruction();

		this.messages.cerrarMensajes();
	}

	public repetitiveInstruction(): void {
		this.intervalo = setInterval(() => {
			this.obtenerListaAreas();
		}, 10000);
	}

	public async obtenerListaAreas(): Promise<any> {
		return this.areas.obtenerListaAreas().toPromise().then(
			respuesta => {
				this.datosTabla = respuesta.areas;
				this.ch.markForCheck();
			}
		)
	}

protected cambiarStatus(area: any): void {

	const estaActivo = Number(area.activo) === 1; // 🔥 clave
	const accion = estaActivo ? 'inactivar' : 'activar';
	const titulo = estaActivo ? 'Inactivar area' : 'Activar area';

	this.messages.mensajeConfirmacionCustom(
		`¿Está seguro de ${accion} el area?`,
		'question',
		titulo
	).then(res => {
		if (!res.isConfirmed) return;

		this.messages.mensajeEsperar();

		this.areas.cambiarStatusArea(area.id_area).subscribe(
			(respuesta) => {

				// opcional pero recomendable
				area.activo = estaActivo ? 0 : 1;

				this.obtenerListaAreas().then(() => {
					this.messages.mensajeGenerico(
						respuesta.mensaje,
						'success',
						respuesta.title
					);
				});
			},
			(error) => {
				this.messages.mensajeGenerico(
					'Error al cambiar el estado del área',
					'error',
					'Error'
				);
			}
		);
	});
}
	public abrirModalRegistrarArea(pkArea: number): void {
		const data: any = {
			pkArea: pkArea
		};

		this.modal.abrirModalConComponente(RegistrarArea, data, 'md-modal');
	}

	ngOnDestroy(): void {
		clearInterval(this.intervalo);
	}
}