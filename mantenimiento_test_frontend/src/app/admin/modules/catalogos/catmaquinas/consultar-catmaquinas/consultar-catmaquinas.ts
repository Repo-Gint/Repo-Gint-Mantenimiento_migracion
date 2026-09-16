import { CommonModule } from '@angular/common';
import { ChangeDetectorRef, Component } from '@angular/core';
import { ModalService } from '../../../../services/modal/modal';
import { MessagesService } from '../../../../services/messages/messages';
import { RegistrarCatmaquina } from '../registrar-catmaquina/registrar-catmaquina';
import { CatmaquinasService } from '../../../../services/api/catmaquinas/catmaquinas';

@Component({
  selector: 'app-consultar-catmaquinas',
  imports: [CommonModule],
  standalone: true,
  templateUrl: './consultar-catmaquinas.html',
  styleUrl: './consultar-catmaquinas.css',
})
export class ConsultarCatmaquinas {
  protected datosTabla: any = [];

  private intervalo: any;

  constructor(
    private modal:    ModalService,
    private messages: MessagesService,
    private ch:       ChangeDetectorRef,
    private catmaquinas: CatmaquinasService
  ) {}

  async ngOnInit(): Promise<any> {
    this.messages.mensajeEsperar();

    await this.obtenerListaCatalogoMaquina();
    this.repetitiveInstruction();

    this.messages.cerrarMensajes()
  }

  private repetitiveInstruction(): void {
    this.intervalo = setInterval(() => {
      this.obtenerListaCatalogoMaquina();
    }, 10000);
  }

  public async obtenerListaCatalogoMaquina(): Promise<any> {
    return this.catmaquinas.obtenerListaCatalogoMaquina().toPromise().then(
      respuesta => {
        this.datosTabla = respuesta.maquinaCatalogos;
        this.ch.markForCheck();
      }
    );
  }

    public cambiarStatusCatalogoMaquina(catmaquina: any): void {

    this.messages.mensajeConfirmacionCustom(
			`¿Está seguro de ${catmaquina.activo ? 'inactivar' : 'activar'} el catalogo maquina?`,
			'question',
			`${catmaquina.activo ? 'Inactivar' : 'Activar'} catalogo maquina`
		).then(res => {
      if (!res.isConfirmed) return; 

      this.messages.mensajeEsperar();

      this.catmaquinas.cambiarStatusCatalogoMaquina(catmaquina.id_machines).subscribe(
        respuesta => {
          this.obtenerListaCatalogoMaquina().then(() => {
            this.messages.mensajeGenerico(respuesta.mensaje, 'success', respuesta.title);
          });
        }, error => {
          this.messages.mensajeGenerico('error', 'error');
        }
      );
    });
  }


    public abrirModalRegistroCatMaquina(pkCatalogoMaquina: number): void {
      const data: any = {
        pkCatalogoMaquina: pkCatalogoMaquina
      };
  
      this.modal.abrirModalConComponente(RegistrarCatmaquina, data, 'md-modal');
    }

    ngOnDestroy(): void {
		clearInterval(this.intervalo);
	}

}
