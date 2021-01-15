// merged version from
// - https://stackoverflow.com/questions/35602866/how-to-send-cookie-in-request-header-for-all-the-requests-in-angular2
// - http://pankajagarwal.in/interceptor-in-angular2-passing-http-header-th/
// - https://yizhiyue.me/2018/12/24/handling-http-requests-with-authorization-using-interceptor-in-angular

import { HttpInterceptor, HttpRequest, HttpHandler, HttpEvent } from '@angular/common/http';
import { Observable, throwError } from 'rxjs';
import { Injectable } from '@angular/core';
import { tap, map, catchError } from 'rxjs/operators';
import { Router } from '@angular/router';

import { AuthService } from './auth.service';

@Injectable()
export class AuthInterceptor implements HttpInterceptor {

    constructor(
        private authService: AuthService,
        private router: Router
    ) {}

    intercept(request: HttpRequest<any>, next: HttpHandler): Observable<HttpEvent<any>> {
        console.log("Intercepted ",request);
        let accessToken = this.authService.getToken();
        console.log("Access token in session storage:",accessToken);
        if ( accessToken ) {
            request = request.clone({
//                withCredentials: true,
                headers: request.headers.append('X-Auth-Token','token '+accessToken)
            });
        }
/*
        return next.handle(request).pipe(catchError(err => {
            console.log("Response error catched.");
            if (err.status === 401) {
                console.log("401 Response error catched.");
                this.authService.logout();
                this.router.navigate(['/auth']);
            }
            const error = err.error.message || err.statusText;
            return throwError(error);
        }));
*/

        return next.handle(request).pipe(
            tap(null, error => {
                console.log("Response tapped.");
                if (error.status === 401) {
                    this.authService.logout();
                    this.router.navigate(['/auth']);
                }
            })
        );

    }
}
