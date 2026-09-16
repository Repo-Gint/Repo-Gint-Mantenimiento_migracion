import { TestBed } from '@angular/core/testing';

import { Catmaquinas } from './catmaquinas';

describe('Catmaquinas', () => {
  let service: Catmaquinas;

  beforeEach(() => {
    TestBed.configureTestingModule({});
    service = TestBed.inject(Catmaquinas);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });
});
