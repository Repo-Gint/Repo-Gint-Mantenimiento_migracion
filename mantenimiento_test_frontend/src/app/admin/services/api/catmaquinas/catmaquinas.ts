import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { api } from '../../../../../environments/environments';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root',
})
export class CatmaquinasService {
  constructor (
    private http: HttpClient
  ) {}

    public registrarCatalogoMaquina(maquinaCatalogo: any): Observable<any> {
      return this.http.post<any>(`${api}/catalogosmaquinas/registrarCatalogoMaquina`, maquinaCatalogo);
    }
  
    public obtenerListaCatalogoMaquina(): Observable<any> {
      return this.http.get<any>(`${api}/catalogosmaquinas/obtenerListaCatalogoMaquina`);
    }
  
    public obtenerDetalleCatalogoMaquina(pkCatalogoMaquina: number): Observable<any> {
      return this.http.get<any>(`${api}/catalogosmaquinas/obtenerDetalleCatalogoMaquina/${pkCatalogoMaquina}`);
    }
  
    public actualizarCatalogoMaquina(maquinaCatalogo: any): Observable<any> {
      return this.http.put<any>(`${api}/catalogosmaquinas/actualizarCatalogoMaquina`, maquinaCatalogo);
    }

      public cambiarStatusCatalogoMaquina(pkCatalogoMaquina: number): Observable<any> {
    return this.http.get<any>(`${api}/catalogosmaquinas/cambiarStatusCatalogoMaquina/${pkCatalogoMaquina}`)
  }
  
}
