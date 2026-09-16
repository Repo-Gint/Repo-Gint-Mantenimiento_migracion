import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ConsultarCatmaquinas } from './consultar-catmaquinas';

describe('ConsultarCatmaquinas', () => {
  let component: ConsultarCatmaquinas;
  let fixture: ComponentFixture<ConsultarCatmaquinas>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [ConsultarCatmaquinas],
    }).compileComponents();

    fixture = TestBed.createComponent(ConsultarCatmaquinas);
    component = fixture.componentInstance;
    await fixture.whenStable();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
