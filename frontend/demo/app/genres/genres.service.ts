import { Injectable } from '@angular/core';
import { Autoregister, Service, Resource, DocumentCollection, DocumentResource } from 'ngx-jsonapi';
import { Book } from '../books/books.service';

export class Genre extends Resource {
    public attributes = {
        gname: ''
    };

    public relationships = {
        books: new DocumentCollection<Book>(),
    };
}

@Injectable()
export class GenresService extends Service<Genre> {
    public resource = Genre;
    public type = 'genres';
}
