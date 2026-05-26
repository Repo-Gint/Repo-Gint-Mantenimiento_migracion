import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { api } from '../../../../environments/environments';

@Injectable({
  providedIn: 'root',
})
export class LoginService {
  constructor(
    private http: HttpClient
  ){ }

  public login(credenciales: any): Observable<any> {
    return this.http.post<any>(api + "/usuarios/login", credenciales)
  }
}