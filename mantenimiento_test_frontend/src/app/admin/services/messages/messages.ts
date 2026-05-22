import { Injectable } from '@angular/core';
import Swal from 'sweetalert2';

@Injectable({
	providedIn: 'root',
})
export class MessagesService {
	private mensajeError500: string = 'Al parecer ocurrió un error interno, porfavor contactarse con el DTIC de GI LATAM ';

	public mensajeEsperar() {
		Swal.fire({
			allowOutsideClick: false,
			text: 'Espere un momento...',
			icon: 'info',
			title: '',
			confirmButtonText: 'Cool',
			keyboard: false,
			allowEscapeKey: false
		} as any);

		Swal.showLoading();

		document.body.style.paddingRight = '';
	}

	public mensajeGenerico(mensaje: string, tipo: string, title: string = '', html: any = null, width: any = null){
		mensaje = mensaje == 'error' ? this.mensajeError500 : mensaje;
		const data: any = {
			title,
			allowOutsideClick: false, 
			icon: tipo,
			html: mensaje,
			confirmButtonText: 'Continuar',
			buttonStyling: false, 
			customClass: {
				confirmButton: 'btn btn-sm btn-primary',
				popup: width != null ? 'popup-custom': ''
			},
			keyboard: false,
			allowEscapeKey: false
		};

		if (html) data['html'] = html;

		Swal.fire(data);

		document.body.style.paddingRight = '';
	}

	public mensajeConfirmacionCustom(mensaje: string, tipo: any, titulo: string = '', btnConfirmar = 'Continuar', btnCancelar = 'Cancelar', btnDenegado = 'Denegar', showDeny = false) {
		return Swal.fire({
			title: titulo,
			html: mensaje,
			icon: tipo,
			showDenyButton: showDeny,
			showCancelButton: true,
			confirmButtonText: btnConfirmar,
			cancelButtonText: btnCancelar,
			denyButtonText: btnDenegado,
			buttonsStyling: false,
			allowOutsideClick: false,
			customClass: {
				confirmButton: 'order-1 btn btn-sm btn-primary me-2',
				cancelButton: 'order-2 btn btn-sm btn-danger',
				denyButton: 'order-3'
			},
			allowEscapeKey: false
		});
	}

	public cerrarMensajes(){
		Swal.close();
		document.body.style.paddingRight = '';
	}
}