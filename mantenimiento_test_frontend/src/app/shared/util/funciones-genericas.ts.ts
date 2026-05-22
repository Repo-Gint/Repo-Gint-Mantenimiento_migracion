import { fromEvent, merge, Observable, of, switchMap, take } from 'rxjs';
import Swal from 'sweetalert2';


export default class FGenerico {
    private success = new Audio('assets/sounds/success.mp3');
    private warning = new Audio('assets/sounds/warning.mp3');
    private info = new Audio('assets/sounds/info.mp3');

    public esperarConexion(): Observable<boolean> {
        if (navigator.onLine) return of(true);

        return merge(fromEvent(window, 'online')).pipe(
            take(1),
            switchMap(() => of(true))
        );
    }

    public fechaActual(): any {
        const hoy = new Date();
        return hoy.toISOString().split('T')[0];
    }

    public soloLetras(event: KeyboardEvent) {
        const pattern = /[a-zA-Zá-úÁ-Ú ]/;
        const inputChar = String.fromCharCode(event.charCode);

        if (!pattern.test(inputChar)) {
            event.preventDefault();
        }
    }

    public soloTexto(event: Event) {
        const pattern = /^[a-zA-Zá-úÁ-Ú0-9 :.,-_@#$%&+*[{}()?¿!¡\n]*$/;
        const inputElement = event.target as HTMLInputElement;
        const inputValue = inputElement.value;

        if (!pattern.test(inputValue)) {
            inputElement.value = inputValue.slice(0, -1);
        }
    }

    public soloNumeros(event: KeyboardEvent) {
        const pattern = /[0-9 .]/;
        const inputChar = String.fromCharCode(event.charCode);

        if (!pattern.test(inputChar)) {
            event.preventDefault();
        }
    }

    public soloLetrasMovil(event: Event): void {
        const input = event.target as HTMLInputElement;
        const valorFiltrado = input.value.replace(/[^a-zA-Zá-úÁ-Ú ]/g, '');
        if (input.value !== valorFiltrado) {
            input.value = valorFiltrado;
            this.dispararEventoInput(input);
        }
    }

    public soloTextoMovil(event: Event): void {
        const input = event.target as HTMLInputElement;
        const valorFiltrado = input.value.replace(/[^a-zA-Zá-úÁ-Ú0-9 :.,\-_@#$%&+*\[{}\]()?¿!¡\n]/g, '');
        if (input.value !== valorFiltrado) {
            input.value = valorFiltrado;
            this.dispararEventoInput(input);
        }
    }

    public soloNumerosMovil(event: Event): void {
        const input = event.target as HTMLInputElement;
        const valorFiltrado = input.value.replace(/[^0-9.]/g, '');
        if (input.value !== valorFiltrado) {
            input.value = valorFiltrado;
            this.dispararEventoInput(input);
        }
    }

    private dispararEventoInput(input: HTMLInputElement): void {
        const evento = new Event('input', { bubbles: true });
        input.dispatchEvent(evento);
    }

    public soloCoordenadas(event: KeyboardEvent) {
        const pattern = /[0-9 .,-]/;
        const inputChar = String.fromCharCode(event.charCode);

        if (!pattern.test(inputChar)) {
            event.preventDefault();
        }
    }

    public convertirAMayusculas(event: any): void {
        event.target.value = event.target.value.toUpperCase();
    }

    public is_empty(cadena: any) {
        return cadena == null || cadena == undefined || (isNaN(cadena) && cadena.trim() == '' || cadena.length == 0);
    }

    public is_number(value: any): boolean {
        return typeof value === 'number' && !isNaN(value);
    }

    public obtenerFormatoNumero(telefono: string): string {
        if (this.is_empty(telefono)) return 'Sin información';
        return telefono.replace(/[^\d]/g, '').replace(/(\d{3})(\d{3})(\d{4})/, '$1 $2 $3');
    }

    public obtenerSaludo(): string {
        const horaActual = new Date().getHours();

        if (horaActual >= 5 && horaActual < 12) {
            return 'buenos días';
        } else if (horaActual >= 12 && horaActual < 18) {
            return 'buenas tardes';
        } else {
            return 'buenas noches';
        }
    }

    public formatString(str: any): string {
        str = str.toString() ?? '';
        return str.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toLowerCase().replace(/[^\w\s]/gi, '').replace(/ñ/g, 'n');
    }

    public formatNumber(valor: number | string): string {
        if (valor === null || valor === undefined) return '';

        const num = Number(valor);
        return new Intl.NumberFormat('en-US').format(num);
    }

    public getCurrentDateFormatted(): string {
        const now = new Date();
        const day = now.getDate().toString().padStart(2, '0');
        const month = (now.getMonth() + 1).toString().padStart(2, '0');
        const year = now.getFullYear().toString();

        return `${day}-${month}-${year}`;
    }

    public adjustTextareaHeight(event: Event): void {
        const textarea = event.target as HTMLTextAreaElement;
        textarea.style.height = 'auto';
        textarea.style.height = (textarea.scrollHeight + 2) + 'px';
    }

    public esHabil = (fecha: Date): boolean => {
        const diaSemana = fecha.getDay();
        return diaSemana >= 1 && diaSemana <= 6;
    };

    public esFeriado = (fecha: Date): boolean => {
        const fechaComparacion = new Date(fecha);
        const year = fechaComparacion.toString().split(' ')[3];

        const feriados = [
            `${year}-01-01`, // Año Nuevo
            this.obtenerPrimerLunesFebrero(new Date().getFullYear()), // Día de la Constitución
            `${year}-03-18`, // Natalicio de Benito Juárez
            `${year}-05-01`, // Día del Trabajo
            `${year}-09-16`, // Día de la Independencia
            `${year}-12-25`, // Navidad
        ];

        fechaComparacion.setDate(fechaComparacion.getDate() - 1);
        return feriados.includes(fechaComparacion.toISOString().split('T')[0]);
    };

    private obtenerPrimerLunesFebrero(year: number): Date {
        let date = new Date(year, 1, 1);

        let dayOfWeek = date.getDay();
        let daysToAdd = (dayOfWeek === 0) ? 1 : (8 - dayOfWeek);

        date.setDate(date.getDate() + daysToAdd);
        return date;
    }

    public copiarPortapapeles(value: string): void {
        navigator.clipboard.writeText(value).then(() => {
            let Toast: any = Swal.mixin({
                toast: true,
                position: 'bottom-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                willOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                    toast.addEventListener('click', Swal.stopTimer);
                }
            });

            Toast.fire({
                icon: 'success',
                title: 'Se copió en el portapapeles'
            });

            document.body.style.paddingRight = '';
        });
    }

    public formatDate(dateString: string): string {
        if (this.is_empty(dateString) || dateString.includes('0000-00-00')) {
            return 'Sin información';
        }

        const hasTime = /T\d{2}:\d{2}|\s\d{2}:\d{2}/.test(dateString);

        let normalizedDate = dateString;

        if (!hasTime && /^\d{4}-\d{2}-\d{2}$/.test(dateString)) {
            normalizedDate = `${dateString}T00:00:00`;
        }

        const inputDate = new Date(normalizedDate);
        const currentDate = new Date();

        const hours = inputDate.getHours();
        const minutes = inputDate.getMinutes();

        inputDate.setHours(0, 0, 0, 0);
        currentDate.setHours(0, 0, 0, 0);

        const timeDiff = currentDate.getTime() - inputDate.getTime();
        const dayDiff = Math.floor(timeDiff / (1000 * 60 * 60 * 24));

        const formattedTime = hasTime
            ? ` ${this.formatTime(hours, minutes)}`
            : '';

        if (dayDiff === 0) return `Hoy${formattedTime}`;

        if (dayDiff === -1) return `Mañana${formattedTime}`;

        if (dayDiff === -2) return `Pasado mañana${formattedTime}`;

        if (dayDiff === 1) return `Ayer${formattedTime}`;

        if (dayDiff === 2) return `Antier${formattedTime}`;

        const formattedDate = this.formatDateToString(inputDate);
        return formattedTime
            ? `${formattedDate} | ${formattedTime}`
            : formattedDate;
    }

    private formatDateToString(date: Date): string {
        const day = date.getDate().toString().padStart(2, '0');
        const month = date.toLocaleString('default', { month: 'short' }).charAt(0).toUpperCase() + date.toLocaleString('default', { month: 'short' }).slice(1);
        const year = date.getFullYear();
        return `${day} ${month} ${year}`;
    }

    private formatTime(hours: number, minutes: number): string {
        const isPM = hours >= 12;
        const hour12 = hours % 12 || 12;
        const minuteStr = minutes.toString().padStart(2, '0');
        const period = isPM ? 'p. m.' : 'a. m.';
        return `${hour12}:${minuteStr} ${period}`;
    }

    public formatearNombre(nombre: any): string {
        if (typeof nombre !== 'string' || nombre.trim() === '') {
            return '';
        }

        const lowercaseWords = ['de', 'del', 'la', 'las', 'el', 'los', 'y'];

        return nombre
            .trim()
            .replace(/\s+/g, ' ')
            .toLowerCase()
            .split(' ')
            .map((word, index) => {
                if (lowercaseWords.includes(word)) {
                    return word;
                } else if (index === 0 && word.length === 2 && word.endsWith('.')) {
                    return word.toUpperCase();
                } else if (index === 0 && word.length === 1) {
                    return word.toUpperCase();
                } else {
                    return word.charAt(0).toUpperCase() + word.slice(1);
                }
            })
            .join(' ');
    }

    public textoValido(texto: any) {
        if (typeof texto !== "string") return true;
        if (texto.length <= 10) return true;

        const containsLetter = /[a-zA-Z]/.test(texto);
        return !containsLetter;
    }

    public eliminarEspaciosBlanco(texto: string) {
        return texto.replace(/\s+/g, '');
    }

    public getCurrentDate(): string {
        const currentDate = new Date();
        return currentDate.toISOString().split('T')[0];
    }

    public selectText(event: any): void {
        event.target.select();
    }

    public semaforoCaducidad(fecha: string | Date): string {
        if (this.is_empty(fecha)) return '';

        const targetDate = new Date(fecha);

        const cad8 = new Date(this.futureDate(8));
        const cad6 = new Date(this.futureDate(6));

        if (targetDate >= cad8) return 'rgb(125, 214, 42)';
        if (targetDate >= cad6) return 'yellow';
        return 'rgb(224, 73, 73)';
    }

    private futureDate(monts: number): string {
        const today = new Date();
        const futureDate = new Date(today.setMonth(today.getMonth() + monts));

        const year = futureDate.getFullYear();
        const month = String(futureDate.getMonth() + 1).padStart(2, '0');
        const day = String(futureDate.getDate()).padStart(2, '0');

        return `${year}-${month}-${day}`;
    }

    private agregarDiasAFecha(base: Date, dias: number): Date {
        const nuevaFecha = new Date(base);
        nuevaFecha.setDate(nuevaFecha.getDate() + dias);
        return nuevaFecha;
    }

    private esFechaMinimo4MesesAdelante(fecha: Date): boolean {
        const hoy = new Date();
        const cuatroMesesDespues = new Date(hoy.getFullYear(), hoy.getMonth() + 4, hoy.getDate());
        return fecha >= cuatroMesesDespues;
    }

    public convertirLoteUnileverAFecha(lote: string, vida_util: number): string | null {
        if (!/^\d{4}/.test(lote)) return null;
        if (typeof vida_util !== 'number' || vida_util < 1) return null;

        const currentYear = new Date().getFullYear();
        const decadeBase = Math.floor(currentYear / 10) * 10;

        const yearOffset = parseInt(lote.charAt(0), 10);
        const dayOfYear = parseInt(lote.slice(1), 10);
        if (dayOfYear < 1 || dayOfYear > 366) return null;

        let year = decadeBase + yearOffset;

        if (year < currentYear - 1) year += 10;

        const fechaProduccion = new Date(year, 0);
        fechaProduccion.setDate(dayOfYear);

        if (fechaProduccion.getFullYear() !== year) return null;

        const fechaFinal = this.agregarDiasAFecha(fechaProduccion, vida_util);
        if (!this.esFechaMinimo4MesesAdelante(fechaFinal)) {
            this.mensajeGenericoToast('La fecha calculada con el lote ingresado es menor a 4 meses de caducidad con respecto a la fecha actual', 'warning');
            return null;
        }

        return fechaFinal.toISOString().slice(0, 10);
    }

    public convertirLotePerfetiAFecha(lote: string, vida_util: number): string | null {
        if (typeof lote !== 'string' || lote.length < 6) return null;
        if (typeof vida_util !== 'number' || vida_util < 1) return null;

        const yearDigit = lote.charAt(1);
        const dayOfYearStr = lote.slice(3, 6);

        if (!/^\d$/.test(yearDigit) || !/^\d{3}$/.test(dayOfYearStr)) return null;

        const currentYear = new Date().getFullYear();
        const decadeBase = Math.floor(currentYear / 10) * 10;

        const yearOffset = parseInt(yearDigit, 10);
        const dayOfYear = parseInt(dayOfYearStr, 10);
        if (dayOfYear < 1 || dayOfYear > 366) return null;

        let year = decadeBase + yearOffset;
        if (year < currentYear - 1) year += 10;

        const fechaProduccion = new Date(year, 0);
        fechaProduccion.setDate(dayOfYear);

        if (fechaProduccion.getFullYear() !== year) return null;

        const fechaFinal = this.agregarDiasAFecha(fechaProduccion, vida_util);
        if (!this.esFechaMinimo4MesesAdelante(fechaFinal)) {
            this.mensajeGenericoToast('La fecha calculada con el lote ingresado es menor a 4 meses de caducidad con respecto a la fecha actual', 'warning');
            return null;
        }

        return fechaFinal.toISOString().slice(0, 10);
    }

    private mensajeGenericoToast(mensaje: string, tipo: string, tiempo: number = 3000, position: any = 'bottom-end') {
        switch (tipo) {
            case 'success':
                this.success.currentTime = 0;
                this.success.play();
                break;
            case 'warning':
            case 'error':
                this.warning.currentTime = 0;
                this.warning.play();
                break;
            default:
                this.info.currentTime = 0;
                this.info.play();
                break;
        }

        let Toast: any = Swal.mixin({
            toast: true,
            position,
            showConfirmButton: false,
            timer: tiempo,
            timerProgressBar: true,
            willOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
                toast.addEventListener('click', Swal.stopTimer);
            }
        });

        Toast.fire({
            icon: tipo,
            title: mensaje
        });

        document.body.style.paddingRight = '';
    }

    public esObjeto(valor: any): any {
        return typeof valor === "object" && valor !== null && !Array.isArray(valor);
    }

    public getTiempoEntreFechas(
        fechaInicio: string | Date | null,
        fechaFin: string | Date | null
    ): string {
        if (!fechaInicio && !fechaFin) return '0m';

        const inicio = fechaInicio ? new Date(fechaInicio) : null;
        const fin = fechaFin ? new Date(fechaFin) : new Date();

        if (!inicio) return '0m';

        const diffMs = fin.getTime() - inicio.getTime();

        if (diffMs <= 0) return '0m';

        const diffMinutes = Math.floor(diffMs / (1000 * 60));
        const diffHours = Math.floor(diffMinutes / 60);
        const diffDays = Math.floor(diffHours / 24);

        const hours = diffHours % 24;
        const minutes = diffMinutes % 60;

        const partes: string[] = [];

        if (diffDays > 0) partes.push(`${diffDays}d`);
        if (hours > 0) partes.push(`${hours}h`);
        if (minutes > 0 && diffDays === 0) partes.push(`${minutes}m`);

        return partes.length > 0 ? partes.join(' ') : '0m';
    }

    public toMinutes(hora: string): number {
        const [h, m] = hora.split(':').map(Number);
        return h * 60 + m;
    }

    public fromMinutes(minutos: number): string {
        const h = Math.floor(minutos / 60).toString().padStart(2, '0');
        const m = (minutos % 60).toString().padStart(2, '0');
        return `${h}:${m}`;
    }

    public permisosFormato(permisos: any = (localStorage.getItem('permisos_sigma') ?? [])): any {
        return JSON.parse(permisos);
    }

    public obtenerPermitidoModulo(moduloBuscado: string): boolean {
        const modulo = this.permisosFormato().find((m: any) => m.modulo == moduloBuscado);

        return modulo?.permitido ?? false;
    }

    public obtenerPermitidoSubModulo(moduloBuscado: string, subModuloBuscado: string): boolean {
        const modulo = this.permisosFormato().find((m: any) => m.modulo == moduloBuscado);
        const subModulo = modulo?.subModulos?.find((sm: any) => sm.subModulo == subModuloBuscado);

        return subModulo?.permitido ?? false;
    }

    public obtenerPermitidoAccionModulo(moduloBuscado: string, accionBuscada: string): boolean {
        const modulo = this.permisosFormato().find((m: any) => m.modulo == moduloBuscado);
        const accion = modulo?.acciones?.find((a: any) => a.accion == accionBuscada);

        return accion?.permitido ?? false;
    }

    public obtenerPermitidoAccionSubModulo(moduloBuscado: string, subModuloBuscado: string, accionBuscada: string): boolean {
        const modulo = this.permisosFormato().find((m: any) => m.modulo == moduloBuscado);
        const subModulo = modulo?.subModulos?.find((sm: any) => sm.subModulo == subModuloBuscado);
        let accion: any;

        if ((subModulo?.acciones ?? []).length > 0) {
            accion = subModulo?.acciones?.find((a: any) => a.accion == accionBuscada);
        } else if ((subModulo?.subModulos ?? []).length > 0) {
            accion = subModulo?.subModulos?.find((sm: any) => sm.subModulo == accionBuscada);
        }

        return accion?.permitido ?? false;
    }

    public obtenerPermitidoAccionSubSubModulo(moduloBuscado: string, subModuloBuscado: string, subSubModuloBuscado: string, accionBuscada: string): boolean {
        const modulo = this.permisosFormato().find((m: any) => m.modulo == moduloBuscado);
        const subModulo = modulo?.subModulos?.find((sm: any) => sm.subModulo == subModuloBuscado);
        const subSubModulo = subModulo?.subModulos?.find((sm: any) => sm.subModulo == subSubModuloBuscado);
        const accion = subSubModulo?.acciones?.find((a: any) => a.accion == accionBuscada);

        return accion?.permitido ?? false;
    }
}