import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { api } from '../../../../../environments/environments';
import { Observable } from 'rxjs';

@Injectable({
 providedIn: 'root',
})
export class Monedaservice {
	constructor (
    private http: HttpClient
 ) {}

	public registrarMoneda(moneda: any): Observable<any> {
				return this.http.post<any>(`${api}/monedas/registrarMoneda`, moneda);
	}
	
	public obtenerListaMonedas(): Observable<any> {
				return this.http.get<any>(`${api}/monedas/obtenerListaMonedas`);
	}
	
	public obtenerDetalleMoneda(pkMoneda: number): Observable<any> {
				return this.http.get<any>(`${api}/monedas/obtenerDetalleMoneda/${pkMoneda}`);
	}
	
	public actualizarMoneda(moneda: any): Observable<any> {
				return this.http.put<any>(`${api}/monedas/actualizarMoneda`, moneda);
	}
	
	public cambiarStatusMoneda(pkMoneda: number): Observable<any> {
				return this.http.get<any>(`${api}/monedas/cambiarStatusMoneda/${pkMoneda}`)
	}
}
