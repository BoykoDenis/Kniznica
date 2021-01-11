// merged version from
// - https://stackoverflow.com/questions/35602866/how-to-send-cookie-in-request-header-for-all-the-requests-in-angular2
// - http://pankajagarwal.in/interceptor-in-angular2-passing-http-header-th/

import { HttpInterceptor, HttpRequest, HttpHandler, HttpEvent } from '@angular/common/http';
import { Observable } from 'rxjs';
import { Injectable } from '@angular/core';

@Injectable()
export class AuthInterceptor implements HttpInterceptor {
    constructor() {}

    intercept(request: HttpRequest<any>, next: HttpHandler): Observable<HttpEvent<any>> {
        console.log("Intercepted ",request);
        const newReq = request.clone({
            withCredentials: true,
            headers: request.headers.append('Authorization','123456')
        });
        return next.handle(newReq);
    }
}
