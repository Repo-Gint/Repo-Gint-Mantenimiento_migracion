import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ConsultaTipoMantenimiento } from './consulta-tipo-mantenimiento';

describe('ConsultaTipoMantenimiento', () => {
  let component: ConsultaTipoMantenimiento;
  let fixture: ComponentFixture<ConsultaTipoMantenimiento>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [ConsultaTipoMantenimiento],
    }).compileComponents();

    fixture = TestBed.createComponent(ConsultaTipoMantenimiento);
    component = fixture.componentInstance;
    await fixture.whenStable();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
