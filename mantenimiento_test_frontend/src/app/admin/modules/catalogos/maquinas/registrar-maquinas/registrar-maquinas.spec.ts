import { ComponentFixture, TestBed } from '@angular/core/testing';

import { RegistrarMaquinas } from './registrar-maquinas';

describe('RegistrarMaquinas', () => {
  let component: RegistrarMaquinas;
  let fixture: ComponentFixture<RegistrarMaquinas>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [RegistrarMaquinas],
    }).compileComponents();

    fixture = TestBed.createComponent(RegistrarMaquinas);
    component = fixture.componentInstance;
    await fixture.whenStable();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
