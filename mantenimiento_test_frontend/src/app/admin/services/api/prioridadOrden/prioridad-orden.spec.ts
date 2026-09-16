import { TestBed } from '@angular/core/testing';

import { PrioridadOrden } from './prioridad-orden';

describe('PrioridadOrden', () => {
  let service: PrioridadOrden;

  beforeEach(() => {
    TestBed.configureTestingModule({});
    service = TestBed.inject(PrioridadOrden);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });
});
