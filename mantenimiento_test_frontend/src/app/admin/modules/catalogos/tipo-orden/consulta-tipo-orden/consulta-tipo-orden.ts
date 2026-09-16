import { CommonModule } from '@angular/common';
import { ChangeDetectorRef, Component } from '@angular/core';
import { ModalService } from '../../../../services/modal/modal';
import { MessagesService } from '../../../../services/messages/messages';
import { TipoOrdenService } from '../../../../services/api/tipoOrden/tipo-orden';
import { RegistrarTipoOrden } from '../registrar-tipo-orden/registrar-tipo-orden';

@Component({
  selector: 'app-consulta-tipo-orden',
  imports: [CommonModule],
  standalone: true,
  templateUrl: './consulta-tipo-orden.html',
  styleUrl: './consulta-tipo-orden.css',
})
export class ConsultaTipoOrden {
  protected datosTabla: any = [];
  private intervalo: any;

  constructor(
    private modal: ModalService,
    private ch: ChangeDetectorRef,
    private messages: MessagesService,
    private tipoOrdenes: TipoOrdenService
  ) { }

  async ngOnInit(): Promise<any> {
    this.messages.mensajeEsperar();

    await this.obtenerListaTipoOrden();
    this.repetitiveInstruction();

    this.messages.cerrarMensajes();
  }

  private repetitiveInstruction(): void {
		this.intervalo = setInterval(() => {
			this.obtenerListaTipoOrden();
		}, 10000);
	}

  public async obtenerListaTipoOrden(): Promise<any> {
    return this.tipoOrdenes.obtenerListaTipoOrden().toPromise().then(
      respuesta => {
        this.datosTabla = respuesta.tipoOrdenes;
        this.ch.markForCheck();
      }
    );
  }

  public cambiarStatus(tipoOrden: any): void {
    this.messages.mensajeConfirmacionCustom(
      `¿Está seguro de ${tipoOrden.activo ? 'inactivar' : 'activar'} el tipo de orden?`,
      'question',
      `${tipoOrden.activo ? 'Inactivar' : 'Activar'} tipo de orden`
    ).then(res => {
      if (!res.isConfirmed) return;

      this.messages.mensajeEsperar();

      this.tipoOrdenes.cambiarStatusTipoOrden(tipoOrden.id_type_orders).subscribe(
        respuesta => {
          this.obtenerListaTipoOrden().then(() => {
            this.messages.mensajeGenerico(respuesta.mensaje, 'success', respuesta.title);
          });
        }, error => {
          this.messages.mensajeGenerico('error', 'error');
        }
      );
    });
  }

  public abrirModalRegistrotipoMantenimiento(pktipoOrden: number): void {
    const data: any = {
      pktipoOrden: pktipoOrden
    };

    this.modal.abrirModalConComponente(RegistrarTipoOrden, data, 'md-modal');
  }

  ngOnDestroy(): void {
    clearInterval(this.intervalo);
  }

}
