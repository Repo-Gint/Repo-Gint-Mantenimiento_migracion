import { Routes } from '@angular/router';
import { Login } from './auth/login/login';
import { AuthGuard } from './guards/admin-auth-guard';
import { Home } from './admin/home/home';
import { ConsultarUsuarios } from './admin/modules/usuarios/consultar-usuarios/consultar-usuarios';
import { ConsultaAreas } from './admin/modules/catalogos/areas/consulta-areas/consulta-areas';
import { ConsultaDepartamentos } from './admin/modules/catalogos/departamentos/consulta-departamentos/consulta-departamentos';

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
			}
		]
	}
];