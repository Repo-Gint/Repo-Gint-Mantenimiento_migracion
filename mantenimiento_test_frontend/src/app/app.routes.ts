import { Routes } from '@angular/router';
import { Login } from './auth/login/login';
import { AuthGuard } from './guards/admin-auth-guard';
import { Home } from './admin/home/home';

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
		children: []
	}
];