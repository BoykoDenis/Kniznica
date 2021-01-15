import { Component, OnInit, Inject } from '@angular/core';
// Add Router to be able to navigate from code (this.router.navigate(...)
import { ActivatedRoute, Router } from '@angular/router';
import { Resource } from 'ngx-jsonapi';
import { Auth, AuthService } from './../auth.service';
import { Output, EventEmitter } from '@angular/core';

// Add Form control
import { FormControl, NgForm } from '@angular/forms';

import { AppComponent } from './../../app.component';

@Component({
    selector: 'demo-auth',
    templateUrl: './auth.component.html'
})
export class AuthComponent {
    public auth: Auth;

    public isValidFormSubmitted: boolean  = true;

    public constructor(
        protected authService: AuthService,
        // call app
        @Inject(AppComponent) protected app: AppComponent,
        // init router
        private router: Router,
        private route: ActivatedRoute
    ) {
        // Reset current auth mode
        this.app.auth.attributes.privileges = 0;
        // create empty author before load one to avoid errors during loading
        this.auth = this.authService.new();
        this.auth.id = '0'; // !!! important because post should be to /auth/0
/*
        // Use current timestamp to avoid caching initial call
        const curtm = new Date();
        route.params.subscribe(({ id }) => {
          authService.get( ''+curtm.getTime(), { ttl: 100 }).subscribe(
                auth => {
                    auth.id = '0';
                    this.auth = auth;
                    console.log('init login session');
                    console.log(auth);
                },
                error => {
                    if ( error.errors[0].status != 401 )
                        console.error('Could not init auth form.', error)
                }
            );
        });
*/
    }

    public onFormSubmit(form: NgForm) {

        this.isValidFormSubmitted = false;
        if (form.valid) {
            this.isValidFormSubmitted = true;
        } else {
            return;
        }

        this.auth.attributes.uemail = form.value.uemail;
        this.auth.attributes.upass = this.authService.MD5(form.value.upass);
        console.log('auth data for save ', this.auth.toObject());
        this.auth.save().subscribe(success => {
            console.log('session initialized', this.auth.toObject());
            console.log(success);
            this.app.auth.id = success['data']['id'];
            this.app.auth.attributes.uname = success['data']['attributes']['uname'];
            this.app.auth.attributes.privileges = success['data']['attributes']['privileges'];
            this.app.auth.attributes.token = success['data']['attributes']['token'];
            this.authService.login( this.app.auth.attributes.token );
//            this.app.auth.attributes.privileges = 10;
            this.router.navigate(['/authors']);
        });
    }

}
