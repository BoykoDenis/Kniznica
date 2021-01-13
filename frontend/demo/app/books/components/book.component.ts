import { Component } from '@angular/core';
// Add Router to be able to navigate from code (this.router.navigate(...)
import { ActivatedRoute, Router } from '@angular/router';
import { Resource, DocumentCollection } from 'ngx-jsonapi';
import { AuthorsService } from '../../authors/authors.service';
import { Genre, GenresService } from '../../genres/genres.service';
import { BooksService, Book } from './../books.service';
import { PhotosService } from '../../photos/photos.service';

// Add Form control
import { FormControl, NgForm } from '@angular/forms';

@Component({
    selector: 'demo-book',
    templateUrl: './book.component.html'
})
export class BookComponent {
    public book: Book;
    public genres: DocumentCollection<Genre>;

    // Flags for form modes
    public isEditMode: boolean = false;
    public isValidFormSubmitted: boolean  = true;

    public constructor(
        protected authorsService: AuthorsService,
        protected booksService: BooksService,
        protected photosService: PhotosService,
        protected genresService: GenresService,
        // init router
        private router: Router,
        private route: ActivatedRoute
    ) {
        // create empty book before load one to avoid errors during loading
        this.book = this.booksService.new();

        route.params.subscribe(({ id }) => {
          // Add processing id = 0 for add new records
          if ( id > 0 ) {
            booksService.get(id, { include: ['authors', 'genres'], ttl: 100 }).subscribe(
                book => {
                    this.book = book;
                    console.log('success book', this.book);
                },
                error => console.log('error books controll', error)
            );
          } else {
              console.log('New book created');
              this.isEditMode = true;
          }
        });

        this.getAllGenres();
    }

    public turnEditMode( mode: boolean ) {
        this.isEditMode = mode;
    }

    public onEdit(  ) {
        this.isEditMode = true;
    }

    public onCancel(  ) {
        if ( this.book.id ) {
            this.isEditMode = false;
        } else {
            this.router.navigate(['/books']);
        }
    }

    public onFormSubmit(form: NgForm) {

        this.isValidFormSubmitted = false;
        if (form.valid) {
            this.isValidFormSubmitted = true;
        } else {
            return;
        }

        var oldid = this.book.id
        console.log('book old id', oldid);

        this.book.attributes.title = form.value.title;
        this.book.attributes.date_published = form.value.date_published;
        this.book.attributes.isbn = form.value.isbn;
        console.log('book data for save without any include', this.book.toObject());
        this.book.save().subscribe(success => {
            console.log('book saved', this.book.toObject());
            this.isEditMode = false;
            if ( oldid == '' ) { // if it was new record
            	this.router.navigate(['/books', this.book.id]);
            }
        });
    }

    public addGenre() {

        let newgenreid = prompt('Link to genre (id):', '');
        if ( !newgenreid || parseInt(newgenreid) < 1 )
        {
            return;
        }
        newgenreid = ''+parseInt(newgenreid)
        this.genresService.get(newgenreid).subscribe(
                genre => {
                    console.log('success genre', genre);
                    this.book.addRelationship(genre);

                    this.book.save( { include: ['genres'] } ).subscribe(success => {
                        console.log('book saved', this.book.toObject());
                    });
                },
                error => alert('Cannot find genre with id:'+newgenreid)
            );
    }

    public addThisGenre( genre ) {

        let newgenreid = ''+genre.id
        this.genresService.get(newgenreid).subscribe(
                genre => {
                    console.log('success genre', genre);
                    this.book.addRelationship(genre);

                    this.book.save( { include: ['genres'] } ).subscribe(success => {
                    console.log('book saved', this.book.toObject());
                    });
                },
                error => alert('Cannot find genre with id:'+newgenreid)
            );
        return false;
    }

    public removeGenre( genre: Resource ) {
        if ( confirm( 'Are you sure to unlink genre ['+ genre.attributes.gname +'] from book' ) ) {
            this.book.removeRelationship('genres', genre.id);
            this.book.save( { include: ['genres'] } );
            console.log('removeRelationship save with genres include', this.book.toObject());
        }
    }

    public addAuthor() {

        let newauthorid = prompt('Link to author (id):', '');
        if ( !newauthorid || parseInt(newauthorid) < 1 )
        {
            return;
        }
        newauthorid = ''+parseInt(newauthorid)
        this.authorsService.get(newauthorid).subscribe(
                author => {
                    console.log('success author', author);
                    this.book.addRelationship(author);

                    this.book.save( { include: ['authors'] } ).subscribe(success => {
                        console.log('book saved', this.book.toObject());
                    });
                },
                error => alert('Cannot find author with id:'+newauthorid)
            );
    }

    public removeAuthor( author: Resource ) {
        if ( confirm( 'Are you sure to unlink author ['+ author.attributes.name +'] from book' ) ) {
            this.book.removeRelationship('authors', author.id);
            this.book.save( { include: ['authors'] } );
            console.log('removeRelationship save with authors include', this.book.toObject());
        }
    }


    public getAllGenres() {

        let genres$ = this.genresService.all({});
        genres$.subscribe(
            genres => {
                this.genres = genres;

                console.log('success genres controller', this.genres);
            },
            error => console.info('error genres controller', error)
        );
        genres$.toPromise().then(success => console.log('genres loaded PROMISE'));
    }



}
