import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ConsultaMonedas } from './consulta-monedas';

describe('ConsultaMonedas', () => {
  let component: ConsultaMonedas;
  let fixture: ComponentFixture<ConsultaMonedas>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [ConsultaMonedas],
    }).compileComponents();

    fixture = TestBed.createComponent(ConsultaMonedas);
    component = fixture.componentInstance;
    await fixture.whenStable();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
