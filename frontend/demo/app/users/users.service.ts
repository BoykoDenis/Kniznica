import { Injectable } from '@angular/core';
import { Autoregister, Service, Resource, DocumentCollection, DocumentResource } from 'ngx-jsonapi';

export class User extends Resource {
    public attributes = {
        uname: '',
        uemail: '',
        upass: '',
        privileges: 0
    };
}

@Injectable()
export class UsersService extends Service<User> {
    public resource = User;
    public type = 'users';
}
