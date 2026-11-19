import { CommonModule } from '@angular/common';
import { ChangeDetectorRef, Component, OnInit, OnDestroy } from '@angular/core';
import { Router, RouterOutlet, NavigationEnd } from '@angular/router';
import { Navbar } from '../components/navbar/navbar';
import { Sidenav } from '../components/sidenav/sidenav';
import { SessionService } from '../services/session/session';
import { filter } from 'rxjs/operators';
import { Chart, registerables } from 'chart.js';
import { MessagesService } from '../services/messages/messages';
import { OrdenesService } from '../services/api/ordenes/ordenes';

Chart.register(...registerables);

@Component({
  selector: 'app-home',
  standalone: true,
  imports: [CommonModule, Navbar, Sidenav, RouterOutlet],
  templateUrl: './home.html',
  styleUrl: './home.css',
})
export class Home implements OnInit, OnDestroy {
  protected esRutaInicio: boolean = true;

  protected totalOrdenes:     number = 0;
  protected totalCorrectivos: number = 0;
  protected totalPreventivos: number = 0;
  protected totalUrgentes:    number = 0;

  protected ordenesPorMaquina:     any[] = [];
  protected listaOrdenesRecientes: any[] = [];

  private chartMaquinas: Chart | null = null;
  private chartResumen:  Chart | null = null;
  private intervalo:     any;

  constructor(
    private sessionService: SessionService,
    private messages: MessagesService,
    private ch: ChangeDetectorRef,
    private ordenesService: OrdenesService,
    private router: Router
  ) {
    this.verificarRutaActual(this.router.url);

    this.router.events
      .pipe(filter((event) => event instanceof NavigationEnd))
      .subscribe((event: any) => {
        this.verificarRutaActual(event.urlAfterRedirects || event.url);
      });
  }

  private verificarRutaActual(url: string): void {
    this.esRutaInicio = url === '/' || url === '/home' || url === '';
    if (this.esRutaInicio && this.totalOrdenes > 0) {
      setTimeout(() => this.renderizarGraficas(), 50);
    }
  }

  async ngOnInit(): Promise<any> {
    this.sessionService.iniciarContador();

    this.messages.mensajeEsperar();
    await this.cargarMetricasDashboard();
    this.repetitiveInstruction();
    this.messages.cerrarMensajes();
  }

  private repetitiveInstruction(): void {
    this.intervalo = setInterval(async () => {
      if (this.esRutaInicio) {
        await this.cargarMetricasDashboard(false);
      }
    }, 10000);
  }

  public async cargarMetricasDashboard(mostrarLoader: boolean = true): Promise<any> {
    const payload = {
      pkArea: 1,
      pkStatus: 1,
    };

    return this.ordenesService
      .ObtenerListaGeneralOrdenes(payload)
      .toPromise()
      .then(
        (respuesta) => {
          const ordenes = respuesta.ordenes || [];
          this.procesarDatosOrdenes(ordenes);
          this.ch.markForCheck();

          if (this.esRutaInicio) {
            setTimeout(() => this.renderizarGraficas(), 50);
          }
        },
        (error) => {
          if (mostrarLoader) {
            this.messages.mensajeGenerico(
              'Error al obtener la información del dashboard',
              'error'
            );
          }
        }
      );
  }

  private procesarDatosOrdenes(ordenes: any[]): void {
    this.totalOrdenes = ordenes.length;
    this.totalCorrectivos = 0;
    this.totalPreventivos = 0;
    this.totalUrgentes = 0;

    this.listaOrdenesRecientes = ordenes.slice(0, 5);

    const maquinasMap: {
      [key: string]: { maquina: string; area: string; total: number };
    } = {};

    ordenes.forEach((orden: any) => {
      const tipoMantenimiento = (orden.type_maintenances || '').toLowerCase();
      if (tipoMantenimiento.includes('correctivo')) {
        this.totalCorrectivos++;
      } else if (tipoMantenimiento.includes('preventivo')) {
        this.totalPreventivos++;
      }

      const prioridad = (orden.priority || '').toLowerCase();
      if (prioridad.includes('urgente')) {
        this.totalUrgentes++;
      }

      const nombreMaquina = orden.machines || 'Sin Máquina';
      const nombreArea = orden.area || 'Nave General';
      const llave = `${nombreArea}-${nombreMaquina}`;

      if (!maquinasMap[llave]) {
        maquinasMap[llave] = {
          maquina: nombreMaquina,
          area: nombreArea,
          total: 0,
        };
      }
      maquinasMap[llave].total++;
    });

    this.ordenesPorMaquina = Object.values(maquinasMap);
  }

  private renderizarGraficas(): void {
    this.generarGraficaBarrasMaquinas();
    this.generarGraficaBarrasResumen();
  }

  private generarGraficaBarrasMaquinas(): void {
    const canvas = document.getElementById('graficaBarrasMaquinas') as HTMLCanvasElement;
    if (!canvas) return;

    if (this.chartMaquinas) {
      this.chartMaquinas.destroy();
    }

    const etiquetas = this.ordenesPorMaquina.map(
      (item) => `${item.maquina} (${item.area})`
    );
    const valores = this.ordenesPorMaquina.map((item) => item.total);

    const fondosPastel = [
      'rgba(255, 99, 132, 0.2)',
      'rgba(255, 159, 64, 0.2)',
      'rgba(255, 205, 86, 0.2)',
      'rgba(75, 192, 192, 0.2)',
      'rgba(54, 162, 235, 0.2)',
      'rgba(153, 102, 255, 0.2)',
      'rgba(201, 203, 207, 0.2)',
    ];

    const bordesSolidos = [
      'rgb(255, 99, 132)',
      'rgb(255, 159, 64)',
      'rgb(255, 205, 86)',
      'rgb(75, 192, 192)',
      'rgb(54, 162, 235)',
      'rgb(153, 102, 255)',
      'rgb(201, 203, 207)',
    ];

    this.chartMaquinas = new Chart(canvas, {
      type: 'bar',
      data: {
        labels: etiquetas,
        datasets: [
          {
            label: 'Órdenes por Máquina',
            data: valores,
            backgroundColor: etiquetas.map((_, index) => fondosPastel[index % fondosPastel.length]),
            borderColor: etiquetas.map((_, index) => bordesSolidos[index % bordesSolidos.length]),
            borderWidth: 1,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              stepSize: 1,
            },
          },
        },
      },
    });
  }

  private generarGraficaBarrasResumen(): void {
    const canvas = document.getElementById('graficaBarrasResumen') as HTMLCanvasElement;
    if (!canvas) return;

    if (this.chartResumen) {
      this.chartResumen.destroy();
    }

    this.chartResumen = new Chart(canvas, {
      type: 'bar',
      data: {
        labels: ['Total Ordenes', 'Correctivos', 'Preventivos', 'Urgentes'],
        datasets: [
          {
            label: 'Resumen Operativo',
            data: [
              this.totalOrdenes,
              this.totalCorrectivos,
              this.totalPreventivos,
              this.totalUrgentes,
            ],
            backgroundColor: [
              'rgba(54, 162, 235, 0.2)',
              'rgba(255, 159, 64, 0.2)',
              'rgba(75, 192, 192, 0.2)',
              'rgba(255, 99, 132, 0.2)',
            ],
            borderColor: [
              'rgb(54, 162, 235)',
              'rgb(255, 159, 64)',
              'rgb(75, 192, 192)',
              'rgb(255, 99, 132)',
            ],
            borderWidth: 1,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              stepSize: 1,
            },
          },
        },
      },
    });
  }

  ngOnDestroy(): void {
    clearInterval(this.intervalo);
    if (this.chartMaquinas) this.chartMaquinas.destroy();
    if (this.chartResumen) this.chartResumen.destroy();
  }
}