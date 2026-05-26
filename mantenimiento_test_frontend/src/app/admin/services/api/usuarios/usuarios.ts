import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { api } from '../../../../../environments/environments';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root',
})
export class Usuarios {
  constructor (
    private http: HttpClient
  ){}

  	public registrarUsuario(usuario: any): Observable<any> {
		return this.http.post<any>(`${api}/usuarios/registrarUsuario`, usuario);
	}

  public obtenerRecursosRegistroUsuario(): Observable<any> {
    return this.http.get<any>(`${api}/usuarios/obtenerRecursosRegistroUsuario`);
  }

  public cerrarSesion(): Observable<any> {
		return this.http.post<any>(`${api}/usuarios/cerrarSesion`, {});
	}
}
