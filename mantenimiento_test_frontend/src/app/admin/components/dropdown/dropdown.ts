import { CommonModule } from '@angular/common';
import { Component, ElementRef, EventEmitter, Input, OnChanges, OnInit, Output, ViewChild } from '@angular/core';

@Component({
  selector: 'app-dropdown',
  imports: [CommonModule],
  standalone: true,
  templateUrl: './dropdown.html',
  styleUrl: './dropdown.css',
})
export class DropdownComponent implements OnInit, OnChanges {
  @ViewChild('dropdownButton') dropdownButton!: ElementRef;
  @ViewChild('searchInput') searchInput!: ElementRef;

  @Input() options: any[] = [];
  @Input() font: string = '';
  @Input() type: string = 'many';
  @Input() search: boolean = true;
  @Input() disabled: boolean = false;
  @Input() order: boolean = false;

  @Output() selectionChange: EventEmitter<any> = new EventEmitter<any>();

  selectedCount = 0;
  filteredOptions: any[] = [];
  searchText: string = '';

  constructor(private elementRef: ElementRef) { }

  ngOnInit(): void {
    this.options = this.order ? this.ordenarOpcionesAscendentemente(this.options) : this.options;
    this.filteredOptions = [...this.options];
    this.updateSelectedCount();
  }

  ngOnChanges(): void {
    this.options.forEach(option => {
      const existingOption = this.filteredOptions.find(item => item.value === option.value);
      if (existingOption) {
        option.checked = existingOption.checked;
      }
    });
    this.options = this.order ? this.ordenarOpcionesAscendentemente(this.options) : this.options;
    this.filteredOptions = [...this.options];
    this.updateSelectedCount();
  }

  protected onDropdownShown() {
    if (this.search && this.isDesktopDevice()) this.searchInput.nativeElement?.focus();
  }

  private isDesktopDevice(): boolean {
    const userAgent = navigator.userAgent || navigator.vendor || (window as any).opera;
    const isMobile = /android|iPad|iPhone|iPod|mobile|tablet/i.test(userAgent.toLowerCase());
    const isWideScreen = window.innerWidth >= 1024;

    return !isMobile && isWideScreen;
  }

  protected closeDropdown(event: MouseEvent) {
    const dropdownMenu = this.elementRef.nativeElement.querySelector('.dropdown-menu');
    if (dropdownMenu && !dropdownMenu.contains(event.target as Node)) {
      this.filteredOptions = [...this.options];
    }
  }

  protected selectAllChanged(event: any) {
    const checked = event.target.checked;
    this.options.forEach(option => {
      if (!option.disabled) option.checked = checked;
    });
    this.filteredOptions = [...this.options];
    this.dropdownButton.nativeElement.click();
    this.updateSelectedCount();
  }

  protected optionChanged(option: any) {
    if (this.type == 'simple') {
      this.options.forEach(op => (op.checked = (op.value != option.value ? false : op.checked)));
      this.dropdownButton.nativeElement.click();
    }
    option.checked = !option.checked;
    this.updateSelectedCount();
  }

  // Método auxiliar para eliminar elemento desde el chip del botón
  protected removeOption(option: any) {
    option.checked = false;
    this.updateSelectedCount();
  }

  protected filterOptions(event: Event) {
    this.searchText = (event.target as HTMLInputElement).value;

    const normalizedSearchText = this.searchText
      .normalize('NFD')
      .replace(/[\u0300-\u036f]/g, '');

    this.filteredOptions = this.searchText
      ? this.options.filter(option =>
        option.label
          ?.normalize('NFD')
          .replace(/[\u0300-\u036f]/g, '')
          .toLowerCase()
          .includes(normalizedSearchText.toLowerCase())
      )
      : [...this.options];

    this.filteredOptions = this.ordenarOpcionesAscendentemente(this.filteredOptions);

    if (this.filteredOptions.length == 1 && this.filteredOptions[0].disabled == false) {
      const option = this.filteredOptions[0];
      option.checked = true;

      if (this.type == 'simple') {
        this.options.forEach(op => (op.checked = (op.value != option.value ? false : op.checked)));
        this.dropdownButton.nativeElement.click();
      }

      this.searchInput.nativeElement.value = '';
      this.filteredOptions = [...this.options];
      this.updateSelectedCount();
    }
  }

  protected allOptionsSelected(): boolean {
    return this.options
      .filter(option => !option.disabled && !option.checked)
      .every(option => option.checked);
  }

  protected updateSelectedCount() {
    this.selectedCount = this.getSelectedOptions().length;
    this.updateSelection();
  }

  protected getSelectedOptions(): any[] {
    return this.options.filter(option => option.checked);
  }

  protected updateSelection() {
    const data = {
      selectedOptions: this.getSelectedOptions(),
      from: this.font
    };
    this.selectionChange.emit(data);
  }

  private ordenarOpcionesAscendentemente(opciones: any[]): any[] {
    return [...opciones].sort((a, b) => {
      const labelA = a.label?.toString().toLowerCase() ?? '';
      const labelB = b.label?.toString().toLowerCase() ?? '';

      if (!labelA && labelB) return 1;
      if (labelA && !labelB) return -1;

      return labelA.localeCompare(labelB, 'es', { numeric: true, sensitivity: 'base' });
    });
  }
}