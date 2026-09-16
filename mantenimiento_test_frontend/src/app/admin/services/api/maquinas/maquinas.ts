import { HttpClient, HttpParams } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { api } from '../../../../../environments/environments';

@Injectable({
  providedIn: 'root',
})
export class MaquinasService {
  constructor (
    private http: HttpClient
  ) {}

  public registrarMaquina(maquina: any): Observable<any> {
    return this.http.post<any>(`${api}/maquinas/registrarMaquina`, maquina);
  }

  public obtenerListaMaquinas(): Observable<any> {
    return this.http.get<any>(`${api}/maquinas/obtenerListaMaquinas`);
  }

  public obtenerMaquinasPorAreaYCategoria(id_area: number, id_cat_machines: number): Observable<any> {
  const params = new HttpParams()
    .set('id_area', id_area)
    .set('id_cat_machines', id_cat_machines);

  return this.http.get<any>(`${api}/maquinas/obtenerMaquinasPorAreaYCategoria`, { params });
}

  public obtenerDetalleMaquina(pkMaquina: number): Observable<any> {
    return this.http.get<any>(`${api}/maquinas/obtenerDetalleMaquina/${pkMaquina}`);
  }

    public obtenerRecursosRegistroMaquina(): Observable<any> {
    return this.http.get<any>(`${api}/maquinas/obtenerRecursosRegistroMaquina`);
  }

  public actualizarMaquina(maquina: any): Observable<any> {
    return this.http.put<any>(`${api}/maquinas/actualizarMaquina`, maquina);
  }

  public cambiarStatusMaquina(pkMaquina: number): Observable<any> {
    return this.http.get<any>(`${api}/maquinas/cambiarStatusMaquina/${pkMaquina}`)
  }
}
