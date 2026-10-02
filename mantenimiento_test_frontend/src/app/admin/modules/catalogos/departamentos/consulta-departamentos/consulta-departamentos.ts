import { CommonModule } from '@angular/common';
import { ChangeDetectorRef, Component, OnDestroy, OnInit } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { ModalService } from '../../../../services/modal/modal';
import { DepartamentosService } from '../../../../services/api/departamentos/departamentos';
import { MessagesService } from '../../../../services/messages/messages';
import { RegistrarDepartamento } from '../registrar-departamento/registrar-departamento';

@Component({
  selector: 'app-consulta-departamentos',
  imports: [CommonModule, FormsModule],
  standalone: true,
  templateUrl: './consulta-departamentos.html',
  styleUrl: './consulta-departamentos.css',
})
export class ConsultaDepartamentos implements OnInit, OnDestroy {
  protected datosTabla: any = [];
  protected textoBusqueda: string = '';
  
  // Objeto para recibir las métricas reales de la API
  protected metricas: any = {
    total_departamentos: 0,
    activos_nave: 0,
    ots_asignadas: 0,
    colaboradores_total: 0
  };

  // Paginación exacta configurada a 5 elementos por página
  protected paginaActual: number = 1;
  protected elementosPorPagina: number = 5;

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
    try {
      const respuesta: any = await this.departamentos.obtenerListaDepartamentos().toPromise();
      
      // Mapeamos los datos reales del backend asegurando las propiedades 'activo' y 'estado'
      this.datosTabla = (respuesta.departaments || []).map((dep: any) => ({
        ...dep,
        activo: Number(dep.Active) === 1,
        estado: Number(dep.Active) === 1 ? 'Activo' : 'Inactivo'
      }));

      // Asignamos las métricas reales enviadas desde el backend
      if (respuesta.metricas) {
        this.metricas = respuesta.metricas;
      }

      this.ch.markForCheck();
    } catch (error) {
      this.datosTabla = [];
    }
  }

  // --- FILTRO DE BÚSQUEDA REACTIVO ---
  get departamentosFiltrados() {
    if (!this.textoBusqueda || this.textoBusqueda.trim() === '') {
      return this.datosTabla;
    }
    const query = this.textoBusqueda.toLowerCase();
    return this.datosTabla.filter((dep: any) => 
      (dep.Departament_ES && dep.Departament_ES.toLowerCase().includes(query)) ||
      (dep.Departament_EN && dep.Departament_EN.toLowerCase().includes(query)) ||
      (dep.Acronym && dep.Acronym.toLowerCase().includes(query))
    );
  }

  // --- PAGINACIÓN CADA 5 ELEMENTOS ---
  get departamentosPaginados() {
    const inicio = (this.paginaActual - 1) * this.elementosPorPagina;
    const fin = inicio + this.elementosPorPagina;
    return this.departamentosFiltrados.slice(inicio, fin);
  }

  protected cambiarPagina(nuevaPagina: number): void {
    this.messages.mensajeEsperar();
    
    setTimeout(() => {
      this.paginaActual = nuevaPagina;
      this.messages.cerrarMensajes();
      this.ch.markForCheck();
    }, 400); // Pausa fluida con el modal de espera requerido
  }

  protected mathMin(a: number, b: number): number {
    return Math.min(a, b);
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
    const data: any = {
      pkDepartamento: pkDepartamento
    };

    this.modal.abrirModalConComponente(RegistrarDepartamento, data, 'md-modal');
  }

  ngOnDestroy(): void {
    clearInterval(this.intervalo);
  }
}