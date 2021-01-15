import { Component, OnInit } from '@angular/core';
import { JsonapiCore } from 'ngx-jsonapi';
import { ActivatedRoute, Router } from '@angular/router';

// Add Forms
import { FormControl } from '@angular/forms';

// Add auth service
import { Auth, AuthService } from './auth/auth.service';

@Component({
    selector: 'demo-app',
    styleUrls: ['./app.component.scss'],
    templateUrl: './app.component.html'
})
export class AppComponent /* implements OnInit */ {
    public loading = '';
    public auth: Auth;
    public authMode = 0;

    public constructor(
        private jsonapiCore: JsonapiCore,
        protected authService: AuthService,
        // init router
        private router: Router
    ) {
        // init current auth object
        this.auth = this.authService.new();

        const curtm = new Date();
        this.authService.get( ''+curtm.getTime(), { ttl: 100 }).subscribe(
                auth => {
                    this.auth = auth;
                    console.log('init auth session');

                    if ( this.auth.attributes.privileges > 0 )
                      this.router.navigate(['/authors']);
//                    else
//                      this.router.navigate(['/auth']);
                },
                error => {
                    if ( error.errors[0].status != 401 )
                        console.error('Could not init auth form.', error)
                }
            );

        jsonapiCore.loadingsStart = (): void => {
            this.loading = 'LOADING...';
        };
        jsonapiCore.loadingsDone = (): void => {
            this.loading = '';
        };
        jsonapiCore.loadingsOffline = (error): void => {
            this.loading = 'No connection!!!';
        };
        jsonapiCore.loadingsError = (error): void => {
            if ( error.errors[0].status != 401 ) {
                let msg = '';
                if ( error.errors[0].status >= 400 && error.errors[0].status < 500 ) {
                    if ( error.errors[0]['title'] ) {
                        msg = ': ' + error.errors[0]['title'];
                    }
                }
                this.loading = 'Error found' + msg;
            }
        };
/*
        if ( this.auth.attributes.privileges > 0 )
            this.router.navigate(['/authors']);
        else
            this.router.navigate(['/auth']);
*/
    }

    public logout() {
        this.authService.logout();
        this.authService.delete(this.auth.id);
        this.router.navigate(['/auth']);
        return false;
    }
}
