import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ConsultaMaquinas } from './consulta-maquinas';

describe('ConsultaMaquinas', () => {
  let component: ConsultaMaquinas;
  let fixture: ComponentFixture<ConsultaMaquinas>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [ConsultaMaquinas],
    }).compileComponents();

    fixture = TestBed.createComponent(ConsultaMaquinas);
    component = fixture.componentInstance;
    await fixture.whenStable();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
