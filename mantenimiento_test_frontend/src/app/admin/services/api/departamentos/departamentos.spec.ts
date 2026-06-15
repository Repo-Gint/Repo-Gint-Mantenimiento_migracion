import { TestBed } from '@angular/core/testing';

import { Departamentos } from './departamentos';

describe('Departamentos', () => {
  let service: Departamentos;

  beforeEach(() => {
    TestBed.configureTestingModule({});
    service = TestBed.inject(Departamentos);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });
});
