import { CommonModule } from '@angular/common';
import { ChangeDetectorRef, Component } from '@angular/core';
import { ModalService } from '../../../../services/modal/modal';
import { MaquinasService } from '../../../../services/api/maquinas/maquinas';
import { MessagesService } from '../../../../services/messages/messages';
import { RegistrarMaquinas } from '../registrar-maquinas/registrar-maquinas';

@Component({
  selector: 'app-consulta-maquinas',
  imports: [CommonModule],
  standalone: true,
  templateUrl: './consulta-maquinas.html',
  styleUrl: './consulta-maquinas.css',
})
export class ConsultaMaquinas {
  protected datosTabla: any = [];

  private intervalo: any;

  constructor (
    private modal: ModalService,
    private maquinas: MaquinasService,
    private messages: MessagesService,
    private ch: ChangeDetectorRef
  ) {}

  async ngOnInit(): Promise<any> {
    this.messages.mensajeEsperar();

    await this.obtenerListaMaquinas();
    this.repetitiveInstruction();

    this.messages.cerrarMensajes();
  }

  private repetitiveInstruction(): void {
    this.intervalo = setInterval(() => {
      this.obtenerListaMaquinas();
    }, 10000);
  }

  public async obtenerListaMaquinas(): Promise<any> {
    return this.maquinas.obtenerListaMaquinas().toPromise().then(
      respuesta => {
        this.datosTabla = respuesta.maquinas;
        this.ch.markForCheck();
      }
    );
  }

  public cambiarStatus(maquina: any): void {

    this.messages.mensajeConfirmacionCustom(
			`¿Está seguro de ${maquina.activo ? 'inactivar' : 'activar'} el maquina?`,
			'question',
			`${maquina.activo ? 'Inactivar' : 'Activar'} maquina`
		).then(res => {
      if (!res.isConfirmed) return; 

      this.messages.mensajeEsperar();

      this.maquinas.cambiarStatusMaquina(maquina.id_machines).subscribe(
        respuesta => {
          this.obtenerListaMaquinas().then(() => {
            this.messages.mensajeGenerico(respuesta.mensaje, 'success', respuesta.title);
          });
        }, error => {
          this.messages.mensajeGenerico('error', 'error');
        }
      );
    });
  }

  public abrirModalRegistroMaquina(pkMaquina: number): void {
    const data: any = {
      pkMaquina: pkMaquina
    };

    this.modal.abrirModalConComponente(RegistrarMaquinas, data, 'md-modal');
  }

  ngOnDestroy(): void {
    clearInterval(this.intervalo);
  }
}
