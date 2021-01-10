import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { BookComponent } from './components/book.component';
import { BooksComponent } from './components/books.component';
import { BooksRoutingModule } from './books-routing.module';
import { SharedModule } from '../shared/shared.module';

// Add Forms
import { FormsModule } from '@angular/forms';

@NgModule({
    imports: [CommonModule, SharedModule, BooksRoutingModule,
              // Add Forms
              FormsModule],
    declarations: [BookComponent, BooksComponent]
})
export class BooksModule {}
