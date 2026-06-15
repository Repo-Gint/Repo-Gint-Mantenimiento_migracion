import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { api } from '../../../../../environments/environments';

@Injectable({
  providedIn: 'root',
})
export class AreasService {
  constructor (
    private http: HttpClient
  ) {}

  public registrarArea(area: any): Observable<any> {
		return this.http.post<any>(`${api}/areas/registrarArea`, area);
	}

  public obtenerListaAreas(): Observable<any> {
		return this.http.get<any>(`${api}/areas/obtenerListaAreas`);
	}

	public obtenerDetalleArea(pkArea: number): Observable<any> {
		return this.http.get<any>(`${api}/areas/obtenerDetalleArea/${pkArea}`);
	}

	public actualizarArea(area: any): Observable<any> {
		return this.http.put<any>(`${api}/areas/actualizarArea`, area);
	}  

	public cambiarStatusArea(pkArea: number): Observable<any> {
		return this.http.get<any>(`${api}/areas/cambiarStatusArea/${pkArea}`)
	}

}
