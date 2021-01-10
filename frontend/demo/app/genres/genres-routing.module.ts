import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { GenresComponent } from './components/genres.component';
import { GenreComponent } from './components/genre.component';

export const routes: Routes = [
    {
        path: '',
        component: GenresComponent
    },
    {
        path: ':id',
        component: GenreComponent
    }
];

@NgModule({
    imports: [ RouterModule.forChild(routes) ],
    exports: [ RouterModule ]
})
export class GenresRoutingModule {}
