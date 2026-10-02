import { Routes } from '@angular/router';
import { Login } from './auth/login/login';
import { AuthGuard } from './guards/admin-auth-guard';
import { Home } from './admin/home/home';
import { ConsultarUsuarios } from './admin/modules/usuarios/consultar-usuarios/consultar-usuarios';
import { ConsultaAreas } from './admin/modules/catalogos/areas/consulta-areas/consulta-areas';
import { ConsultaDepartamentos } from './admin/modules/catalogos/departamentos/consulta-departamentos/consulta-departamentos';
import { ConsultaMaquinas } from './admin/modules/catalogos/maquinas/consulta-maquinas/consulta-maquinas';
import { ConsultaMonedas } from './admin/modules/catalogos/monedas/consulta-monedas/consulta-monedas';
import { ConsultaTipoOrden } from './admin/modules/catalogos/tipo-orden/consulta-tipo-orden/consulta-tipo-orden';
import { ConsultaTipoMantenimiento } from './admin/modules/catalogos/tipo-mantenimiento/consulta-tipo-mantenimiento/consulta-tipo-mantenimiento';
import { ConsultarCatmaquinas } from './admin/modules/catalogos/catmaquinas/consultar-catmaquinas/consultar-catmaquinas';
import { ConsultaOrdenes } from './admin/modules/ordenes/consulta-ordenes/consulta-ordenes';
import { ChatOrdenesComponent } from './admin/modules/ordenes/chat-orders/chat-orders';

export const routes: Routes = [
	{
		path: 'login',
		component: Login
	},
	{
		path: '',
		component: Home,
		canActivate: [AuthGuard],
		canActivateChild: [AuthGuard],
		children: [
			{
			path: 'consulta-usuarios',
			component: ConsultarUsuarios
			},

			{
				path: 'consulta-areas',
				component: 	ConsultaAreas
			},

			{
				path: 'consulta-departamentos',
				component: ConsultaDepartamentos
			},

			{
				path: 'consulta-maquinas',
				component: ConsultaMaquinas
			}, 

			{
				path: 'consulta-catmaquinas',
				component: ConsultarCatmaquinas
			},

			{
				path: 'consulta-monedas', 
				component: ConsultaMonedas
			},

			{
				path: 'consulta-tipo-orden',
				component: ConsultaTipoOrden
			},

			{
				path: 'consulta-tipo-mantenimiento',
				component: ConsultaTipoMantenimiento
			},

			{
				path: 'consulta-ordenes',
				component: ConsultaOrdenes
			},

			{
    			path: 'chat-ordenes/:pkOrden',
    			component: ChatOrdenesComponent
			}
		]
	}
];