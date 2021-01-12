import { Injectable } from '@angular/core';
import { Autoregister, Service, Resource, DocumentCollection, DocumentResource } from 'ngx-jsonapi';

export class Login extends Resource {
    public attributes = {
        loginname: '',
        password: '',
        privileges: ''
    };
}

@Injectable()
export class LoginService extends Service<Login> {
    public resource = Login;
    public type = 'login';
}
