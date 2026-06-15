import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ConsultarUsuarios } from './consultar-usuarios';

describe('ConsultarUsuarios', () => {
  let component: ConsultarUsuarios;
  let fixture: ComponentFixture<ConsultarUsuarios>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [ConsultarUsuarios],
    }).compileComponents();

    fixture = TestBed.createComponent(ConsultarUsuarios);
    component = fixture.componentInstance;
    await fixture.whenStable();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
