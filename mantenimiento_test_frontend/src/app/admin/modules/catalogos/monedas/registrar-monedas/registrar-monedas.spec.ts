import { ComponentFixture, TestBed } from '@angular/core/testing';

import { RegistrarMonedas } from './registrar-monedas';

describe('RegistrarMonedas', () => {
  let component: RegistrarMonedas;
  let fixture: ComponentFixture<RegistrarMonedas>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [RegistrarMonedas],
    }).compileComponents();

    fixture = TestBed.createComponent(RegistrarMonedas);
    component = fixture.componentInstance;
    await fixture.whenStable();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
