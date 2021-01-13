import { Component } from '@angular/core';
// Add Router to be able to navigate from code (this.router.navigate(...)
import { ActivatedRoute, Router } from '@angular/router';
import { Resource } from 'ngx-jsonapi';
import { GenresService, Genre } from './../genres.service';

// Add Form control
import { FormControl, NgForm } from '@angular/forms';

@Component({
    selector: 'demo-genre',
    templateUrl: './genre.component.html'
})
export class GenreComponent {
    public genre: Genre;

    // Flags for form modes
    public isEditMode: boolean = false;
    public isValidFormSubmitted: boolean  = true;

    public constructor(
        protected genresService: GenresService,
        // init router
        private router: Router,
        private route: ActivatedRoute
    ) {
        // create empty genre before load one to avoid errors during loading
        this.genre = this.genresService.new();

        route.params.subscribe(({ id }) => {
          // Add processing id = 0 for add new records
          if ( id > 0 ) {
            genresService.get(id, { ttl: 100 }).subscribe(
                genre => {
                    this.genre = genre;
                    console.log('genre loaded for id', id);
                },
                error => console.error('Could not load genre.', error)
            );
          } else {
              console.log('New genre created');
              this.isEditMode = true;
          }
        });
    }

    public onEdit(  ) {
        this.isEditMode = true;
    }

    public onCancel(  ) {
        if ( this.genre.id ) {
            this.isEditMode = false;
        } else {
            this.router.navigate(['/genres']);
        }
    }

    public onFormSubmit(form: NgForm) {

        this.isValidFormSubmitted = false;
        if (form.valid) {
            this.isValidFormSubmitted = true;
        } else {
            return;
        }

        var oldid = this.genre.id
        console.log('genre old id', oldid);

        this.genre.attributes.gname = form.value.gname;
        console.log('genre data for save ', this.genre.toObject());
        this.genre.save().subscribe(success => {
            console.log('genre saved', this.genre.toObject());
            this.isEditMode = false;
            if ( oldid == '' ) { // if it was new record
            	this.router.navigate(['/genres', this.genre.id]);
            }
        });
    }
}
