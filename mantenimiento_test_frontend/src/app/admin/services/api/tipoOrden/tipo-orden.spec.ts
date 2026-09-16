import { TestBed } from '@angular/core/testing';

import { TipoOrden } from './tipo-orden';

describe('TipoOrden', () => {
  let service: TipoOrden;

  beforeEach(() => {
    TestBed.configureTestingModule({});
    service = TestBed.inject(TipoOrden);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });
});
