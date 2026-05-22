import { Injectable } from '@angular/core';
import { BsModalRef, BsModalService } from 'ngx-bootstrap/modal';

@Injectable({
    providedIn: 'root',
})
export class ModalService {
 modalStack: BsModalRef[] = [];

	constructor(
		private modalService: BsModalService
	) { }

	public abrirModalConComponente(component: any, dataModal: any = null, typeModal: string = '') {
		if (this.modalStack.length > 0) {
			setTimeout(() => {
				const modals = document.querySelectorAll('.modal.show');
				if (modals.length > 0) {
					const lastModal = modals[modals.length - 1];
					lastModal.classList.add('hidden-modal');
				}
			}, 0);
		}

		const modalConfig = {
			ignoreBackdropClick: true,
			keyboard: false,
			animated: true,
			initialState: dataModal,
			class: 'modal-xl modal-dialog-centered ' + typeModal,
			style: {
				'background-color': 'transparent',
				'overflow-y': 'auto'
			}
		};

		if (typeModal == 'lg-modal') {
			modalConfig.class = 'modal-lg modal-dialog-centered';
		}

		if (typeModal == 'md-modal') {
			modalConfig.class = 'modal-md modal-dialog-centered';
		}

		const modalRef = this.modalService.show(component, modalConfig);
		this.modalStack.push(modalRef);
	}

	public cerrarModal() {
		if (this.modalStack.length > 0) {
			const modalRef = this.modalStack.pop();
			modalRef?.hide();

			if (this.modalStack.length > 0) {
				setTimeout(() => {
					const modals = document.querySelectorAll('.modal.hidden-modal');
					if (modals.length > 0) {
						const lastHidden = modals[modals.length - 1];
						lastHidden.classList.remove('hidden-modal');
					}
				}, 0);
			}
		}
	}

	public cerrarTodosLosModales(): void {
		this.modalService.hide();
	}
}