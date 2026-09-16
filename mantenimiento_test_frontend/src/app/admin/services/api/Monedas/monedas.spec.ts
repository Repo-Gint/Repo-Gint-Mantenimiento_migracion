import { TestBed } from '@angular/core/testing';

import { Monedas } from './monedas';

describe('Monedas', () => {
  let service: Monedas;

  beforeEach(() => {
    TestBed.configureTestingModule({});
    service = TestBed.inject(Monedas);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });
});
