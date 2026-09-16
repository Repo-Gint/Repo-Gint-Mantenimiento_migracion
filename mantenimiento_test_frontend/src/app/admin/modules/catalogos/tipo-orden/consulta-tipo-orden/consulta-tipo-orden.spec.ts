import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ConsultaTipoOrden } from './consulta-tipo-orden';

describe('ConsultaTipoOrden', () => {
  let component: ConsultaTipoOrden;
  let fixture: ComponentFixture<ConsultaTipoOrden>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [ConsultaTipoOrden],
    }).compileComponents();

    fixture = TestBed.createComponent(ConsultaTipoOrden);
    component = fixture.componentInstance;
    await fixture.whenStable();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
