import { TestBed } from '@angular/core/testing';

import { Maquinas } from './maquinas';

describe('Maquinas', () => {
  let service: Maquinas;

  beforeEach(() => {
    TestBed.configureTestingModule({});
    service = TestBed.inject(Maquinas);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });
});
