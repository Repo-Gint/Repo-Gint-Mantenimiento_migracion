import { Component } from '@angular/core';
import { Navbar } from '../components/navbar/navbar';
import { Sidenav } from '../components/sidenav/sidenav';
import { SessionService } from '../services/session/session';
import { RouterOutlet } from '@angular/router';

@Component({
  selector: 'app-home',
  standalone: true,
  imports: [Navbar, Sidenav, RouterOutlet],
  templateUrl: './home.html',
  styleUrl: './home.css',
})
export class Home {

  constructor(
    private sessionService: SessionService
  ) {}

  ngOnInit(): void {
    this.sessionService.iniciarContador();
  }

}