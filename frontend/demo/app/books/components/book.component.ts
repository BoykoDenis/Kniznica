import { Component } from '@angular/core';
// Add Router to be able to navigate from code (this.router.navigate(...)
import { ActivatedRoute, Router } from '@angular/router';
import { Resource } from 'ngx-jsonapi';
import { AuthorsService } from '../../authors/authors.service';
import { GenresService } from '../../genres/genres.service';
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
              this.book = this.booksService.new();
              console.log('New book created');
              this.isEditMode = true;
          }
        });
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
}
