import { ComponentFixture, TestBed } from '@angular/core/testing';

import { AsignarOrden } from './asignar-orden';

describe('AsignarOrden', () => {
  let component: AsignarOrden;
  let fixture: ComponentFixture<AsignarOrden>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [AsignarOrden],
    }).compileComponents();

    fixture = TestBed.createComponent(AsignarOrden);
    component = fixture.componentInstance;
    await fixture.whenStable();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
