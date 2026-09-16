import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ConsultaOrdenes } from './consulta-ordenes';

describe('ConsultaOrdenes', () => {
  let component: ConsultaOrdenes;
  let fixture: ComponentFixture<ConsultaOrdenes>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [ConsultaOrdenes],
    }).compileComponents();

    fixture = TestBed.createComponent(ConsultaOrdenes);
    component = fixture.componentInstance;
    await fixture.whenStable();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
