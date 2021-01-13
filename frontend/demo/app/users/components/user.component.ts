import { Component } from '@angular/core';
// Add Router to be able to navigate from code (this.router.navigate(...)
import { ActivatedRoute, Router } from '@angular/router';
import { Resource } from 'ngx-jsonapi';
import { UsersService, User } from './../users.service';

// Add Form control
import { FormControl, NgForm } from '@angular/forms';

@Component({
    selector: 'demo-user',
    templateUrl: './user.component.html'
})
export class UserComponent {
    public user: User;

    // Flags for form modes
    public isEditMode: boolean = false;
    public isValidFormSubmitted: boolean  = true;

    public constructor(
        protected usersService: UsersService,
        // init router
        private router: Router,
        private route: ActivatedRoute
    ) {
        // create empty empty before load one to avoid errors during loading
        this.user = this.usersService.new();

        route.params.subscribe(({ id }) => {
          // Add processing id = 0 for add new records
          if ( id > 0 ) {
            usersService.get(id, { ttl: 100 }).subscribe(
                user => {
                    user.attributes.upass = '*******';
                    this.user = user;
                    console.log('user loaded for id', id);
                },
                error => console.error('Could not load user.', error)
            );
          } else {
              console.log('New user created');
              this.isEditMode = true;
          }
        });
    }

    public onEdit(  ) {
        this.isEditMode = true;
    }

    public onCancel(  ) {
        if ( this.user.id ) {
            this.isEditMode = false;
        } else {
            this.router.navigate(['/users']);
        }
    }

    public onFormSubmit(form: NgForm) {

        this.isValidFormSubmitted = false;
        if (form.valid) {
            this.isValidFormSubmitted = true;
        } else {
            return;
        }

        var oldid = this.user.id
        console.log('user old id', oldid);

        this.user.attributes.uname = form.value.uname;
        this.user.attributes.uemail = form.value.uemail;
        this.user.attributes.upass = form.value.upass;
        this.user.attributes.privileges = form.value.privileges;
        console.log('user data for save ', this.user.toObject());
        this.user.save().subscribe(success => {
            console.log('user saved', this.user.toObject());
            this.isEditMode = false;
            if ( oldid == '' ) { // if it was new record
            	this.router.navigate(['/users', this.user.id]);
            }
        });
    }
}
