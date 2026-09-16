import { ComponentFixture, TestBed } from '@angular/core/testing';

import { RegistrarCatmaquina } from './registrar-catmaquina';

describe('RegistrarCatmaquina', () => {
  let component: RegistrarCatmaquina;
  let fixture: ComponentFixture<RegistrarCatmaquina>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [RegistrarCatmaquina],
    }).compileComponents();

    fixture = TestBed.createComponent(RegistrarCatmaquina);
    component = fixture.componentInstance;
    await fixture.whenStable();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
