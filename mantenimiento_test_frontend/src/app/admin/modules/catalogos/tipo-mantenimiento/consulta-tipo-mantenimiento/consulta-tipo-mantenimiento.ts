import { CommonModule } from '@angular/common';
import { ChangeDetectorRef, Component, HostListener, OnInit, OnDestroy } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { ModalService } from '../../../../services/modal/modal';
import { MessagesService } from '../../../../services/messages/messages';
import { RegistrarTipoMantenimiento } from '../registrar-tipo-mantenimiento/registrar-tipo-mantenimiento';
import { TipoMantenimientoService } from '../../../../services/api/tipoMantenimiento/tipo-mantenimiento';

@Component({
    selector: 'app-consulta-tipo-mantenimiento',
    imports: [CommonModule, FormsModule],
    standalone: true,
    templateUrl: './consulta-tipo-mantenimiento.html',
    styleUrl: './consulta-tipo-mantenimiento.css',
})
export class ConsultaTipoMantenimiento implements OnInit, OnDestroy {
    protected datosTabla: any = [];
    protected datosFiltrados: any = [];
    protected datosPaginados: any = [];
    protected kpis: any = { totalCategorias: 0, porcentajeNorma: 100, totalColores: 0, coloresList: '' };
    protected textoBusqueda: string = '';
    protected filtroEstado: string = 'todos';
    protected ultimaSincronizacion: string = 'hace unos momentos';
    private intervalo: any;

    // Propiedades de Paginación
    public paginaActual: number = 1;
    public elementosPorPagina: number = 5;
    public totalPaginas: number = 1;
    public paginas: number[] = [];

    constructor(
        private modal: ModalService,
        private ch: ChangeDetectorRef,
        private messages: MessagesService,
        private tipoMantenimientos: TipoMantenimientoService
    ) { }

    async ngOnInit(): Promise<any> {
        this.messages.mensajeEsperar();
        await this.obtenerListatipoMantenimientos();
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

    private repetitiveInstruction(): void {
        this.intervalo = setInterval(() => {
            this.obtenerListatipoMantenimientos(false);
        }, 10000);
    }

    public async obtenerListatipoMantenimientos(mostrarLoader: boolean = true): Promise<any> {
        return this.tipoMantenimientos.obtenerListatipoMantenimientos().toPromise().then(
            respuesta => {
                this.datosTabla = respuesta.tipoMantenimientos || [];
                this.kpis = respuesta.kpis || this.kpis;
                this.aplicarFiltros();
                this.ultimaSincronizacion = 'hace un momento';
                this.ch.markForCheck();
            },
            error => {
                if (mostrarLoader) this.messages.mensajeGenerico('Error al obtener información', 'error');
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
                item.type_maintenances?.toLowerCase().includes(query) ||
                item.acronym?.toLowerCase().includes(query) ||
                item.color?.toLowerCase().includes(query)
            );
        }

        if (this.filtroEstado === 'activos') {
            resultado = resultado.filter((item: any) => item.active === 1);
        } else if (this.filtroEstado === 'inactivos') {
            resultado = resultado.filter((item: any) => item.active === 0);
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
        if (pagina >= 1 && pagina <= this.totalPaginas) {
            this.paginaActual = pagina;
            this.actualizarPaginacion();
        }
    }

    public cambiarStatus(tipoMantenimiento: any): void {
        const isActive = tipoMantenimiento.active === 1;
        this.messages.mensajeConfirmacionCustom(
            `¿Está seguro de ${isActive ? 'inactivar' : 'activar'} el tipo de mantenimiento?`,
            'question',
            `${isActive ? 'Inactivar' : 'Activar'} tipo de mantenimiento`
        ).then(res => {
            if (!res.isConfirmed) return;

            this.messages.mensajeEsperar();

            this.tipoMantenimientos.cambiarStatustipoMantenimiento(tipoMantenimiento.id_type_maintenances).subscribe(
                respuesta => {
                    this.obtenerListatipoMantenimientos(false).then(() => {
                        this.messages.mensajeGenerico(respuesta.mensaje, 'success', respuesta.title);
                    });
                }, error => {
                    this.messages.mensajeGenerico('Ocurrió un error', 'error');
                }
            );
        });
    }

    public abrirModalRegistrotipoMantenimiento(pktipoMantenimiento: number): void {
        const data: any = { pktipoMantenimiento };
        this.modal.abrirModalConComponente(RegistrarTipoMantenimiento, data, 'md-modal');
    }

    ngOnDestroy(): void {
        clearInterval(this.intervalo);
    }
}