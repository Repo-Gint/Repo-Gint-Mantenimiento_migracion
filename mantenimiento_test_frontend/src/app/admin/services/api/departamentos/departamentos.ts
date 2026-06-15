import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { api } from '../../../../../environments/environments';

@Injectable({
  providedIn: 'root',
})
export class DepartamentosService {
  constructor (
    private http: HttpClient) {}

    public registrarDepartamento(departamento: any): Observable<any> {
            return this.http.post<any>(`${api}/departamentos/registrarDepartamento`, departamento);
    }

    public obtenerListaDepartamentos(): Observable<any> {
            return this.http.get<any>(`${api}/departamentos/obtenerListaDepartamentos`);
    }

    public obtenerDetalleDepartamento(pkDepartamento: number): Observable<any> {
            return this.http.get<any>(`${api}/departamentos/obtenerDetalleDepartamento/${pkDepartamento}`);
    }

	  public actualizarDepartamento(departamento: any): Observable<any> {
	    return this.http.put<any>(`${api}/departamentos/actualizarDepartamento`, departamento);
	  }

    public cambiarStatusDepartamento(pkDepartamento: number): Observable<any> {
		        return this.http.get<any>(`${api}/departamentos/cambiarStatusDepartamento/${pkDepartamento}`)
	}

}
