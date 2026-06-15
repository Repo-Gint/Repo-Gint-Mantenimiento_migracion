import { ComponentFixture, TestBed } from '@angular/core/testing';

import { RegistrarDepartamento } from './registrar-departamento';

describe('RegistrarDepartamento', () => {
  let component: RegistrarDepartamento;
  let fixture: ComponentFixture<RegistrarDepartamento>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [RegistrarDepartamento],
    }).compileComponents();

    fixture = TestBed.createComponent(RegistrarDepartamento);
    component = fixture.componentInstance;
    await fixture.whenStable();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
