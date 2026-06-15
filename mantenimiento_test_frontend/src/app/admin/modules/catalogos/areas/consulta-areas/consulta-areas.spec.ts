import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ConsultaAreas } from './consulta-areas';

describe('ConsultaAreas', () => {
  let component: ConsultaAreas;
  let fixture: ComponentFixture<ConsultaAreas>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [ConsultaAreas],
    }).compileComponents();

    fixture = TestBed.createComponent(ConsultaAreas);
    component = fixture.componentInstance;
    await fixture.whenStable();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
