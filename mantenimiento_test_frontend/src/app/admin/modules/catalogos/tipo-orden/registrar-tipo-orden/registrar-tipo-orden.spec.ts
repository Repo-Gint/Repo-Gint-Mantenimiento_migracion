import { ComponentFixture, TestBed } from '@angular/core/testing';

import { RegistrarTipoOrden } from './registrar-tipo-orden';

describe('RegistrarTipoOrden', () => {
  let component: RegistrarTipoOrden;
  let fixture: ComponentFixture<RegistrarTipoOrden>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [RegistrarTipoOrden],
    }).compileComponents();

    fixture = TestBed.createComponent(RegistrarTipoOrden);
    component = fixture.componentInstance;
    await fixture.whenStable();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
