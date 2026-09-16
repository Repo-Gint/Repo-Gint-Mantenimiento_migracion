import { TestBed } from '@angular/core/testing';

import { TipoMantenimiento } from './tipo-mantenimiento';

describe('TipoMantenimiento', () => {
  let service: TipoMantenimiento;

  beforeEach(() => {
    TestBed.configureTestingModule({});
    service = TestBed.inject(TipoMantenimiento);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });
});
