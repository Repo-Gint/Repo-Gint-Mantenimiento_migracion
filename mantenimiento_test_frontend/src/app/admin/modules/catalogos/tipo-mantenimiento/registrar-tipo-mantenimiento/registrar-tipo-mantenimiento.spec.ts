import { ComponentFixture, TestBed } from '@angular/core/testing';

import { RegistrarTipoMantenimiento } from './registrar-tipo-mantenimiento';

describe('RegistrarTipoMantenimiento', () => {
  let component: RegistrarTipoMantenimiento;
  let fixture: ComponentFixture<RegistrarTipoMantenimiento>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [RegistrarTipoMantenimiento],
    }).compileComponents();

    fixture = TestBed.createComponent(RegistrarTipoMantenimiento);
    component = fixture.componentInstance;
    await fixture.whenStable();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
