import { Component } from '@angular/core';
import { Resource, DocumentCollection } from 'ngx-jsonapi';
import { GenresService, Genre } from './../genres.service';
import { AuthorsService } from './../../authors/authors.service';
import { PhotosService } from '../../photos/photos.service';
import { ActivatedRoute } from '@angular/router';

@Component({
    selector: 'demo-genres',
    templateUrl: './genres.component.html'
})
export class GenresComponent {
    public genres: DocumentCollection<Genre>;

    public constructor(
        private route: ActivatedRoute,
        protected genresService: GenresService
    ) {
        route.queryParams.subscribe(({ page }) => {
            genresService
                .all({
                    sort: ['name'],
                    page: { number: page || 1, size: 5 },
                    ttl: 3600
                })
                .subscribe(
                    genres => {
                        this.genres = genres;
                        // console.info('success genres controll', this.genres);
                    },
                    (error): void => console.info('error genres controll', error)
                );
        });
    }

    public delete(genre: Resource) {
        if ( confirm( 'Are you sure to delete genre: ' + genre.attributes.gname ) )
            this.genresService.delete(genre.id);
    }
}
