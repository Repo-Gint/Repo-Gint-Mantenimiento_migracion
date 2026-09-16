import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { api } from '../../../../../environments/environments';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root',
})
export class TipoMantenimientoService {
  constructor(
    private http: HttpClient
  ) { }

  public registrarTipoMantenimiento(tipoMantenimiento: any): Observable<any> {
    return this.http.post<any>(`${api}/tipoMantenimiento/registrarTipoMantenimiento`, tipoMantenimiento);
  }

  public obtenerListatipoMantenimientos(): Observable<any> {
    return this.http.get<any>(`${api}/tipoMantenimiento/obtenerListatipoMantenimientos`);
  }

  public obtenerDetalletipoMantenimiento(pktipoMantenimiento: number): Observable<any> {
    return this.http.get<any>(`${api}/tipoMantenimiento/obtenerDetalletipoMantenimiento/${pktipoMantenimiento}`);
  }

  public actualizartipoMantenimiento(tipoMantenimiento: any): Observable<any> {
    return this.http.put<any>(`${api}/tipoMantenimiento/actualizartipoMantenimiento`, tipoMantenimiento);
  }

  public cambiarStatustipoMantenimiento(pktipoMantenimiento: number): Observable<any> {
    return this.http.get<any>(`${api}/tipoMantenimiento/cambiarStatustipoMantenimiento/${pktipoMantenimiento}`)
  }
}
