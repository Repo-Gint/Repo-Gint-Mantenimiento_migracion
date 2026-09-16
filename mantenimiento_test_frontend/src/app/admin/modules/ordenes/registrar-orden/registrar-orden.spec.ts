import { ComponentFixture, TestBed } from '@angular/core/testing';

import { RegistrarOrden } from './registrar-orden';

describe('RegistrarOrden', () => {
  let component: RegistrarOrden;
  let fixture: ComponentFixture<RegistrarOrden>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [RegistrarOrden],
    }).compileComponents();

    fixture = TestBed.createComponent(RegistrarOrden);
    component = fixture.componentInstance;
    await fixture.whenStable();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
