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

    public getAll(remotefilter) {
        // we add some remote filter
        remotefilter.date_published = {
            since: '1983-01-01',
            until: '2010-01-01'
        };

        let genres$ = this.genresService.all({
            remotefilter: remotefilter,
            page: { number: 1 },
            include: ['author', 'photos']
        });
        genres$.subscribe(
            genres => {
                this.genres = genres;

                console.log('success genres controller', this.genres);
            },
            error => console.info('error genres controller', error)
        );
        genres$.toPromise().then(success => console.log('genres loaded PROMISE'));
    }

    public delete(genre: Resource) {
        this.genresService.delete(genre.id);
    }
}
