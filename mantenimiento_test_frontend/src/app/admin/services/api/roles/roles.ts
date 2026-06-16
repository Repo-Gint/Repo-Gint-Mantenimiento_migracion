import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { api } from '../../../../../environments/environments';

@Injectable({
  providedIn: 'root',
})
export class RolesService {
  constructor (
    private http: HttpClient
  ) {}

  public registrarRol(rol: any): Observable<any> {
    return this.http.post<any>(`${api}/roles/registrarRol`, rol);
  }

  public obtenerListaRoles(): Observable<any> {
    return this.http.get<any>(`${api}/roles/obtenerListaRoles`);
  }
}
