import { HttpInterceptorFn } from '@angular/common/http';
import { inject } from '@angular/core';
import { Router } from '@angular/router';
import { catchError } from 'rxjs/operators';
import { MessagesService } from '../../app/admin/services/messages/messages';

export const authInterceptor: HttpInterceptorFn = (req, next) => {

	const router = inject(Router);
	const messages = inject(MessagesService);

	const token = localStorage.getItem('token_orden_gitlatam');

	let cloned = req;

	if (token) {
		cloned = req.clone({
			setHeaders: {
				Authorization: `Bearer ${token}`
			}
		});
	}

	return next(cloned).pipe(
		catchError((error: any) => {
			messages.cerrarMensajes();

			const mensaje = error?.error?.message || 'Ocurrió un error';
			if (error.status === 401) {

				localStorage.removeItem('token_orden_gitlatam');

				messages.mensajeGenerico(
					mensaje, 'warning', 'Sesión expirada'
				);

				router.navigate(['/login']);
			}
			else {
				messages.mensajeGenerico( mensaje, 'error');
			}

			throw error;
		})
	);
};