import { CommonModule } from '@angular/common';
import { ChangeDetectorRef, Component } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { ModalService } from '../../../services/modal/modal';
import { MessagesService } from '../../../services/messages/messages';
import { OrdenesService } from '../../../services/api/ordenes/ordenes';
import { AreasService } from '../../../services/api/areas/areas';
import { RegistrarOrden } from '../registrar-orden/registrar-orden';

@Component({
  selector: 'app-consulta-ordenes',
  imports: [CommonModule, FormsModule],
  standalone: true,
  templateUrl: './consulta-ordenes.html',
  styleUrl: './consulta-ordenes.css',
})
export class ConsultaOrdenes {
  protected datosTabla: any = [];

  private intervalo: any;

  protected listaAreas: any[] = [];
	protected listaStatus: any[] = [];

  protected id_area: any = '';
	protected id_status: any = '';

  constructor(
    private modal:    ModalService,
    private messages: MessagesService,
    private ch:       ChangeDetectorRef,
    private areas:    AreasService,
    private ordenes: OrdenesService
  ) {}

  async ngOnInit(): Promise<any> {
    this.messages.mensajeEsperar();

    await this.obtenerListaAreas();
    await this.obtenerStatusOrdenes(); 

    this.messages.cerrarMensajes();
    
    this.repetitiveInstruction(); 
  }

  private repetitiveInstruction(): void {
		this.intervalo = setInterval(() => {
			if (this.id_area == '' || this.id_status == '') {
				this.datosTabla = [];
				return;
			}

			if (navigator.onLine) this.ObtenerListaGeneralOrdenes();
		}, 10000);
	}

  private async obtenerStatusOrdenes(): Promise<any> {
		return this.ordenes.obtenerStatusOrdenes().toPromise().then(
			respuesta => {
				this.listaStatus = respuesta.ordenes;
				this.ch.markForCheck();
			}
		);
	}

  private async obtenerListaAreas(): Promise<any> {
		return this.areas.obtenerListaAreas().toPromise().then(
			respuesta => {
				this.listaAreas = respuesta.areas;
				this.ch.markForCheck();
			}
		)
	}

  protected async busquedaFiltros(): Promise<any> {
		clearInterval(this.intervalo);

		if (this.id_area == '' || this.id_status == '') return;

		this.messages.mensajeEsperar();
		await this.ObtenerListaGeneralOrdenes().then(() => {
			this.messages.cerrarMensajes();
			this.repetitiveInstruction();
		});
	}


  protected async ObtenerListaGeneralOrdenes(): Promise<any> {
		const data: any = {
			pkArea: this.id_area,
			pkStatus: this.id_status
		};

		return this.ordenes.ObtenerListaGeneralOrdenes(data).toPromise().then(
			respuesta => {
				this.datosTabla = respuesta.ordenes;
				this.ch.markForCheck();
			}
		)
	}


  protected cancelarOrden(id_order: number): void {
		this.messages.mensajeConfirmacionCustom(
			'¿Está seguro de continuar con la cancelación del orden?',
			'question', 'Cancelar orden'
		).then(res => {

			if (!res.isConfirmed) return;

			this.messages.mensajeEsperar();

			this.ordenes.cancelarOrden(id_order).subscribe({
				next: (respuesta: any) => {

					this.messages.mensajeGenerico(
						respuesta.mensaje, 'success', 'Cancelar orden'
					);
					this.ObtenerListaGeneralOrdenes();
				},

				error: (error) => {
					this.messages.mensajeGenerico(
						error?.error?.mensaje || 'Ocurrió un error al cancelar el orden.',
						'error'
					);
				}
			});

		});
	}

  getStatusIcon(status: string): string {
		switch (status?.toLowerCase()) {
			case 'pendiente':
				return 'bi-hourglass-split text-warning';
			case 'en proceso':
				return 'bi-gear-fill text-primary';
			case 'en espera':
				return 'bi-pause-circle-fill text-secondary';
			case 'terminado':
				return 'bi-check-circle-fill text-success';
			case 'cancelado':
				return 'bi-x-circle-fill text-danger';
			default:
				return 'bi-question-circle text-dark';
		}
	}

getStatusNombre(id: any): string {
  const s = this.listaStatus?.find(x => x.id_status_order == id);
  return s ? s.status : '';
}

  public abrirModalRegistrarOrdenes(pkOrden: number): void {
		const data: any = {
			pkOrden: pkOrden
		};

    this.modal.abrirModalConComponente(RegistrarOrden, data, 'lg-modal');
	}
	ngOnDestroy(): void {
		clearInterval(this.intervalo);
	}


}
