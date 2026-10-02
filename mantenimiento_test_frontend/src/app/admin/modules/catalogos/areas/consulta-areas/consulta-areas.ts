import { CommonModule } from '@angular/common';
import { ChangeDetectorRef, Component, HostListener, OnInit, OnDestroy } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { AreasService } from '../../../../services/api/areas/areas';
import { ModalService } from '../../../../services/modal/modal';
import { RegistrarArea } from '../registrar-area/registrar-area';
import { MessagesService } from '../../../../services/messages/messages';

@Component({
    selector: 'app-consulta-areas',
    imports: [CommonModule, FormsModule],
    standalone: true,
    templateUrl: './consulta-areas.html',
    styleUrl: './consulta-areas.css',
})
export class ConsultaAreas implements OnInit, OnDestroy {
    protected datosTabla: any = [];
    protected datosFiltrados: any = [];
    protected datosPaginados: any = [];

    protected textoBusqueda: string = '';
    protected filtroEstado: string = 'todos';
    private intervalo: any;

    // Paginación
    public paginaActual: number = 1;
    public elementosPorPagina: number = 5;
    public totalPaginas: number = 1;
    public paginas: number[] = [];

    constructor(
        private modal: ModalService,
        private areas: AreasService,
        private messages: MessagesService,
        private ch: ChangeDetectorRef
    ) { }

    async ngOnInit(): Promise<any> {
        this.messages.mensajeEsperar();
        await this.obtenerListaAreas();
        this.repetitiveInstruction();
        this.messages.cerrarMensajes();
    }

    @HostListener('window:keydown', ['$event'])
    handleKeyboardEvent(event: KeyboardEvent) {
        if ((event.ctrlKey || event.metaKey) && event.key.toLowerCase() === 'k') {
            event.preventDefault();
            const inputSearch = document.getElementById('inputBusquedaGlobal') as HTMLInputElement;
            if (inputSearch) inputSearch.focus();
        }
    }

    public repetitiveInstruction(): void {
        this.intervalo = setInterval(() => {
            this.obtenerListaAreas(false);
        }, 10000);
    }

    public async obtenerListaAreas(mostrarLoader: boolean = true): Promise<any> {
        return this.areas.obtenerListaAreas().toPromise().then(
            respuesta => {
                this.datosTabla = respuesta.areas || [];
                this.aplicarFiltros();
                this.ch.markForCheck();
            },
            error => {
                if (mostrarLoader) this.messages.mensajeGenerico('Error al obtener información de Áreas', 'error');
            }
        );
    }

    public setFiltroEstado(nuevoEstado: string): void {
        this.filtroEstado = nuevoEstado;
        this.aplicarFiltros();
    }

    public aplicarFiltros(): void {
        let resultado = this.datosTabla;

        if (this.textoBusqueda.trim() !== '') {
            const query = this.textoBusqueda.toLowerCase();
            resultado = resultado.filter((item: any) =>
                item.area?.toLowerCase().includes(query) ||
                item.acronym?.toLowerCase().includes(query) ||
                item.color?.toLowerCase().includes(query)
            );
        }

        if (this.filtroEstado === 'activos') {
            resultado = resultado.filter((item: any) =>
                Number(item.active) === 1 || item.active === true || String(item.estado).toLowerCase() === 'activo'
            );
        } else if (this.filtroEstado === 'inactivos') {
            resultado = resultado.filter((item: any) =>
                Number(item.active) === 0 || item.active === false || String(item.estado).toLowerCase() === 'inactivo'
            );
        }

        this.datosFiltrados = resultado;
        this.paginaActual = 1;
        this.actualizarPaginacion();
    }

    public actualizarPaginacion(): void {
        this.totalPaginas = Math.ceil(this.datosFiltrados.length / this.elementosPorPagina) || 1;
        this.paginas = Array.from({ length: this.totalPaginas }, (_, i) => i + 1);

        const inicio = (this.paginaActual - 1) * this.elementosPorPagina;
        const fin = inicio + this.elementosPorPagina;
        this.datosPaginados = this.datosFiltrados.slice(inicio, fin);
    }

    public cambiarPagina(pagina: number): void {
        if (pagina >= 1 && pagina <= this.totalPaginas && pagina !== this.paginaActual) {
            this.messages.mensajeEsperar();
            this.paginaActual = pagina;
            this.actualizarPaginacion();

            setTimeout(() => {
                this.messages.cerrarMensajes();
                this.ch.markForCheck();
            }, 250);
        }
    }

    protected cambiarStatus(area: any): void {
        const estaActivo = Number(area.active) === 1 || area.active === true || String(area.estado).toLowerCase() === 'activo';
        const accion = estaActivo ? 'inactivar' : 'activar';
        const titulo = estaActivo ? 'Inactivar area' : 'Activar area';

        this.messages.mensajeConfirmacionCustom(`¿Está seguro de ${accion} el area?`, 'question', titulo
        ).then(res => {
            if (!res.isConfirmed) return;

            this.messages.mensajeEsperar();
            this.areas.cambiarStatusArea(area.id_area).subscribe(
                (respuesta) => {
                    area.active = estaActivo ? 0 : 1;
                    this.obtenerListaAreas(false).then(() => {
                        this.messages.mensajeGenerico(respuesta.mensaje, 'success', respuesta.title);
                    });
                },
                (error) => {
                    this.messages.mensajeGenerico(
                        'Error al cambiar el estado del área', 'error', 'Error'
                    );
                }
            );
        });
    }

    public abrirModalRegistrarArea(pkArea: number): void {
        const data: any = { pkArea: pkArea };

        this.modal.abrirModalConComponente(RegistrarArea, data, 'md-modal');
    }

    ngOnDestroy(): void {
        clearInterval(this.intervalo);
    }
}