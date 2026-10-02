import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { api } from '../../../../../environments/environments';

@Injectable({
  providedIn: 'root',
})
export class OrdenesService {
  constructor (
    private http: HttpClient
  ) {}

  public registrarOrden(formData: FormData): Observable<any> {
    return this.http.post<any>(`${api}/ordenes/registrarOrden`, formData);
  }

  public obtenerRecursosRegistroOrden(): Observable<any> {
    return this.http.get<any>(`${api}/ordenes/obtenerRecursosRegistroOrden`);
  }

  public obtenerStatusOrdenes(): Observable<any> {
    return this.http.get<any>(`${api}/ordenes/obtenerStatusOrdenes`);
  }

  public ObtenerListaGeneralOrdenes(data: any): Observable<any> {
    return this.http.post<any>(`${api}/ordenes/ObtenerListaGeneralOrdenes`, data); 
  }

  public obtenerDetalleOrden($pkOrden: number): Observable<any> {
    return this.http.get<any>(`${api}/ordenes/obtenerDetalleOrden/${$pkOrden}`);
  }

  public actualizarOrden(formData: FormData): Observable<any> {
    return this.http.post<any>(`${api}/ordenes/actualizarOrden`, formData);
  }

  public eliminarEvidenciaOrden(id_eviden_order: number): Observable<any> {
    return this.http.delete<any>(`${api}/ordenes/eliminarEvidenciaOrden/${id_eviden_order}`);
  }

  public cancelarOrden(id: number): Observable<any> {
    return this.http.post<any>(`${api}/ordenes/cancelarOrden/${id}`, {});
  }

  public obtenerUsuariosAsignacion($pkOrden: number): Observable<any> {
    return this.http.get<any>(`${api}/ordenes/obtenerUsuariosAsignacion/${$pkOrden}`);
  }

  public asignarOrden(data: any): Observable<any> {
    return this.http.post<any>(`${api}/ordenes/asignarOrden`, data);
  }

  public obtenerMaquinasPorAreaYCategoria(idArea: number, idCatMachines: number): Observable<any> {
    return this.http.get<any>(`${api}/ordenes/obtenerMaquinasPorAreaYCategoria/${idArea}/${idCatMachines}`);
  }

  public obtenerMensajesOrden(id_order: number): Observable<any> {
    return this.http.get<any>(`${api}/ordenes/obtenerMensajesOrden/${id_order}`);
  }

  public enviarMensajeOrden(data: any): Observable<any> {
    return this.http.post<any>(`${api}/ordenes/enviarMensajeOrden`, data);
  }

  public cambiarStatusYSolucionarOrden(data: any): Observable<any> {
    return this.http.post<any>(`${api}/ordenes/cambiarStatusYSolucionarOrden`, data);
  }

  public obtenerListaMonedas(): Observable<any> {
    return this.http.get<any>(`${api}/monedas/obtenerListaMonedas`);
  }
}