import { Component } from '@angular/core';
// Add Router to be able to navigate from code (this.router.navigate(...)
import { ActivatedRoute, Router } from '@angular/router';
import { Resource } from 'ngx-jsonapi';
import { Login, LoginService } from './../login.service';
import { Output, EventEmitter } from '@angular/core';

// Add Form control
import { FormControl, NgForm } from '@angular/forms';

@Component({
    selector: 'demo-login',
    templateUrl: './login.component.html'
})
export class LoginComponent {
    public login: Login;

    public isValidFormSubmitted: boolean  = true;

    public constructor(
        protected loginService: LoginService,
        // init router
        private router: Router,
        private route: ActivatedRoute
    ) {
        const curtm = new Date();
        route.params.subscribe(({ id }) => {
          loginService.get( ''+curtm.getTime(), { ttl: 100 }).subscribe(
                login => {
                    login.id = '0';
                    this.login = login;
                    console.log('init login session');
                },
                error => console.error('Could not init login.', error)
            );
        });
    }

    public onFormSubmit(form: NgForm) {

        this.isValidFormSubmitted = false;
        if (form.valid) {
            this.isValidFormSubmitted = true;
        } else {
            return;
        }

        this.login.attributes.loginname = form.value.loginname;
        this.login.attributes.password = form.value.password;
        console.log('login data for save ', this.login.toObject());
        this.login.save().subscribe(success => {
            console.log('session initialized', this.login.toObject());
            this.router.navigate(['/authors']);
        });
    }

}
