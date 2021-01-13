import { Photo } from './../../../../src/tests/factories/photos.service';
import { BooksService } from './../../books/books.service';
import { Component } from '@angular/core';
import { DocumentCollection } from 'ngx-jsonapi';
import { AuthorsService, Author } from './../authors.service';
import { ActivatedRoute } from '@angular/router';
import { Resource } from 'ngx-jsonapi';

@Component({
    selector: 'demo-authors',
    templateUrl: './authors.component.html'
})
export class AuthorsComponent {
    public authors: DocumentCollection<Author>;

    public constructor(private route: ActivatedRoute, private authorsService: AuthorsService, booksService: BooksService) {
        route.queryParams.subscribe(({ page }) => {
            authorsService
                .all({
                    sort: ['name'],
                    page: { number: page || 1, size: 5 },
                    ttl: 3600
                })
                .subscribe(
                    authors => {
                        this.authors = authors;
                    },
                    error => console.error('Could not load authors :(', error)
                );
        });
    }

    public delete(author: Resource) {
        if ( confirm( 'Are you sure to delete author: ' + author.attributes.name ) )
            this.authorsService.delete(author.id);
        return false;
    }

}
