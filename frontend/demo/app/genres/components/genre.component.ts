import { Component } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { Resource } from 'ngx-jsonapi';
import { GenresService, Genre } from './../genres.service';

@Component({
    selector: 'demo-genre',
    templateUrl: './genre.component.html'
})
export class GenreComponent {
    public genre: Genre;

    public constructor(
        protected genresService: GenresService,
        private route: ActivatedRoute
    ) {
        route.params.subscribe(({ id }) => {
            genresService.get(id, { include: ['author', 'photos'] }).subscribe(
                genre => {
                    this.genre = genre;
                    console.log('success genre', this.genre);
                },
                error => console.log('error genres controll', error)
            );
        });
    }
}
