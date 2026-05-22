import { TestBed } from '@angular/core/testing';

import { FuncionesGenericasTs } from './funciones-genericas.ts';

describe('FuncionesGenericasTs', () => {
  let service: FuncionesGenericasTs;

  beforeEach(() => {
    TestBed.configureTestingModule({});
    service = TestBed.inject(FuncionesGenericasTs);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });
});
