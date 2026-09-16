import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { api } from '../../../../../environments/environments';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root',
})
export class PrioridadOrden {
  constructor (
    private http: HttpClient
  ) {}

    public registrarPrioridadOrden(prioridadOrden: any): Observable<any> {
          return this.http.post<any>(`${api}/prioridadOrden/registrarPrioridadOrden`, prioridadOrden);
    }
    
    public obtenerListaPrioridadOrden(): Observable<any> {
          return this.http.get<any>(`${api}/prioridadOrden/obtenerListaPrioridadOrden`);
    }
    
    public obtenerDetallePrioridadOrden(pkPrioridadOrden: number): Observable<any> {
          return this.http.get<any>(`${api}/prioridadOrden/obtenerDetallePrioridadOrden/${pkPrioridadOrden}`);
    }
    
    public actualizarPrioridadOrden(prioridadOrden: any): Observable<any> {
          return this.http.put<any>(`${api}/prioridadOrden/actualizarPrioridadOrden`, prioridadOrden);
    }
    
    public cambiarStatusPrioridadOrden(pkPrioridadOrden: number): Observable<any> {
          return this.http.get<any>(`${api}/prioridadOrden/cambiarStatusPrioridadOrden/${pkPrioridadOrden}`)
    }
}
