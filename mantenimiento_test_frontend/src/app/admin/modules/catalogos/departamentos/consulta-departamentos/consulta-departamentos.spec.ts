import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ConsultaDepartamentos } from './consulta-departamentos';

describe('ConsultaDepartamentos', () => {
  let component: ConsultaDepartamentos;
  let fixture: ComponentFixture<ConsultaDepartamentos>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [ConsultaDepartamentos],
    }).compileComponents();

    fixture = TestBed.createComponent(ConsultaDepartamentos);
    component = fixture.componentInstance;
    await fixture.whenStable();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
