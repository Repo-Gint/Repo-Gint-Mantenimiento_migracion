import { CommonModule } from '@angular/common';
import { ChangeDetectorRef, Component, OnDestroy } from '@angular/core';
import { ModalService } from '../../../../services/modal/modal';
import { DepartamentosService } from '../../../../services/api/departamentos/departamentos';
import { MessagesService } from '../../../../services/messages/messages';
import { RegistrarDepartamento } from '../registrar-departamento/registrar-departamento';

@Component({
  selector: 'app-consulta-departamentos',
  imports: [CommonModule],
  standalone: true,
  templateUrl: './consulta-departamentos.html',
  styleUrl: './consulta-departamentos.css',
})
export class ConsultaDepartamentos implements OnDestroy {
  protected datosTabla: any = [];

  private intervalo: any;

  constructor(
    private modal: ModalService,
    private departamentos: DepartamentosService, 
    private messages: MessagesService,
    private ch: ChangeDetectorRef
  ) {}

  async ngOnInit(): Promise<any> {
    this.messages.mensajeEsperar();

    await this.obtenerListaDepartamentos();
    this.repetitiveInstruction();

    this.messages.cerrarMensajes();
  }

  	private repetitiveInstruction(): void {
		this.intervalo = setInterval(() => {
			this.obtenerListaDepartamentos();
		}, 10000);
	}

public async obtenerListaDepartamentos(): Promise<any> {
  return this.departamentos.obtenerListaDepartamentos().toPromise().then(
    respuesta => {
      console.log(respuesta);

      this.datosTabla = respuesta.departaments;
      this.ch.markForCheck();
    }
  );
}

  public cambiarStatus(departamento: any): void {

    this.messages.mensajeConfirmacionCustom(
			`¿Está seguro de ${departamento.activo ? 'inactivar' : 'activar'} el departamento?`,
			'question',
			`${departamento.activo ? 'Inactivar' : 'Activar'} departamento`
		).then(res => {
      if (!res.isConfirmed) return; 

      this.messages.mensajeEsperar();

      this.departamentos.cambiarStatusDepartamento(departamento.id_departaments).subscribe(
        respuesta => {
          this.obtenerListaDepartamentos().then(() => {
            this.messages.mensajeGenerico(respuesta.mensaje, 'success', respuesta.title);
          });
        }, error => {
          this.messages.mensajeGenerico('error', 'error');
        }
      );
    });
  }

public abrirModalRegistrarDepartamento(pkDepartamento: number): void {

  console.log('ID ENVIADO:', pkDepartamento);

  const data: any = {
    pkDepartamento: pkDepartamento
  };

  this.modal.abrirModalConComponente(RegistrarDepartamento,
    data,
    'md-modal'
  );
}

  ngOnDestroy(): void {
    clearInterval(this.intervalo);
  }
}
