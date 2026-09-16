import { CommonModule } from '@angular/common';
import { ChangeDetectorRef, Component } from '@angular/core';
import { ModalService } from '../../../../services/modal/modal';
import { Monedaservice } from '../../../../services/api/Monedas/monedas';
import { MessagesService } from '../../../../services/messages/messages';
import { RegistrarMonedas } from '../registrar-monedas/registrar-monedas';

@Component({
  selector: 'app-consulta-monedas',
  imports: [CommonModule],
  standalone: true,
  templateUrl: './consulta-monedas.html',
  styleUrl: './consulta-monedas.css',
})
export class ConsultaMonedas {
  protected datosTabla: any = [];
  private intervalo: any;


  constructor(
    private modal: ModalService,
    private monedas: Monedaservice,
    private messages: MessagesService,
    private ch: ChangeDetectorRef
  ) { }

  async ngOnInit(): Promise<any> {
    this.messages.mensajeEsperar();

    await this.obtenerListaMonedas();
    this.repetitiveInstruction();

    this.messages.cerrarMensajes();
  }

  private repetitiveInstruction(): void {
    this.intervalo = setInterval(() => {
      this.obtenerListaMonedas();
    }, 10000);
  }

  public async obtenerListaMonedas(): Promise<any> {
    return this.monedas.obtenerListaMonedas().toPromise().then(
      respuesta => {
        this.datosTabla = respuesta.monedas;
        this.ch.markForCheck();
      }
    );
  }

  public cambiarStatus(moneda: any): void {

    this.messages.mensajeConfirmacionCustom(
      `¿Está seguro de ${moneda.activo ? 'inactivar' : 'activar'} el moneda?`,
      'question',
      `${moneda.activo ? 'Inactivar' : 'Activar'} moneda`
    ).then(res => {
      if (!res.isConfirmed) return;

      this.messages.mensajeEsperar();

      this.monedas.cambiarStatusMoneda(moneda.id_coins).subscribe(
        respuesta => {
          this.obtenerListaMonedas().then(() => {
            this.messages.mensajeGenerico(respuesta.mensaje, 'success', respuesta.title);
          });
        }, error => {
          this.messages.mensajeGenerico('error', 'error');
        }
      );
    });
  }

  public abrirModalRegistrarMoneda(pkMoneda: number): void {
    const data: any = {
      pkMoneda: pkMoneda
    };

    this.modal.abrirModalConComponente(RegistrarMonedas, data, 'md-modal');
  }

  ngOnDestroy(): void {
    clearInterval(this.intervalo);
  }

}
