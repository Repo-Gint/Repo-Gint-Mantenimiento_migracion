import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { api } from '../../../../../environments/environments';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root',
})
export class TipoOrdenService {
  constructor(
    private http: HttpClient
  ) { }

  public registrarTipoOrden(tipoOrden: any): Observable<any> {
    return this.http.post<any>(`${api}/tipoOrdenes/registrarTipoOrden`, tipoOrden);
  }

  public obtenerListaTipoOrden(): Observable<any> {
    return this.http.get<any>(`${api}/tipoOrdenes/obtenerListaTipoOrden`);
  }

  public obtenerDetalleTipoOrden(pktipoOrden: number): Observable<any> {
    return this.http.get<any>(`${api}/tipoOrdenes/obtenerDetalleTipoOrden/${pktipoOrden}`);
  }

  public actualizarTipoOrden(tipoOrden: any): Observable<any> {
    return this.http.put<any>(`${api}/tipoOrdenes/actualizarTipoOrden`, tipoOrden);
  }

  public cambiarStatusTipoOrden(pktipoOrden: number): Observable<any> {
    return this.http.get<any>(`${api}/tipoOrdenes/cambiarStatusTipoOrden/${pktipoOrden}`)
  }
}
