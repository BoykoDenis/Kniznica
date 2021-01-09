import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { GenreComponent } from './components/genre.component';
import { GenresComponent } from './components/genres.component';
import { GenresRoutingModule } from './genres-routing.module';
import { SharedModule } from '../shared/shared.module';

// Add Forms
import { FormsModule } from '@angular/forms';

@NgModule({
    imports: [CommonModule, SharedModule, GenresRoutingModule,
              // Add Forms
              FormsModule],
    declarations: [GenreComponent, GenresComponent]
})
export class GenresModule {}
