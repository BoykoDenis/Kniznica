import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { AuthorComponent } from './components/author.component';
import { AuthorsComponent } from './components/authors.component';
import { AuthorsRoutingModule } from './authors-routing.module';
import { SharedModule } from '../shared/shared.module';

// Add Forms
import { FormsModule } from '@angular/forms';

@NgModule({
    imports: [CommonModule, SharedModule, AuthorsRoutingModule,
              // Add Forms
              FormsModule],
    declarations: [AuthorComponent, AuthorsComponent]
})
export class AuthorsModule {}
