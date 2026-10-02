import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ChatOrders } from './chat-orders';

describe('ChatOrders', () => {
  let component: ChatOrders;
  let fixture: ComponentFixture<ChatOrders>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [ChatOrders],
    }).compileComponents();

    fixture = TestBed.createComponent(ChatOrders);
    component = fixture.componentInstance;
    await fixture.whenStable();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
