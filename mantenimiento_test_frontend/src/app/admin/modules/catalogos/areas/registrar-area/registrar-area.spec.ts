import { ComponentFixture, TestBed } from '@angular/core/testing';

import { RegistrarArea } from './registrar-area';

describe('RegistrarArea', () => {
  let component: RegistrarArea;
  let fixture: ComponentFixture<RegistrarArea>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [RegistrarArea],
    }).compileComponents();

    fixture = TestBed.createComponent(RegistrarArea);
    component = fixture.componentInstance;
    await fixture.whenStable();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
