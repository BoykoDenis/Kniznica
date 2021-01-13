import { Component } from '@angular/core';
import { Resource, DocumentCollection } from 'ngx-jsonapi';
import { UsersService, User } from './../users.service';
import { AuthorsService } from './../../authors/authors.service';
import { PhotosService } from '../../photos/photos.service';
import { ActivatedRoute } from '@angular/router';

@Component({
    selector: 'demo-users',
    templateUrl: './users.component.html'
})
export class UsersComponent {
    public users: DocumentCollection<User>;

    public constructor(
        private route: ActivatedRoute,
        protected usersService: UsersService
    ) {
        route.queryParams.subscribe(({ page }) => {
            usersService
                .all({
                    sort: ['name'],
                    page: { number: page || 1, size: 5 },
                    ttl: 3600
                })
                .subscribe(
                    users => {
                        this.users = users;
                        // console.info('success users controll', this.users);
                    },
                    (error): void => console.info('error users controll', error)
                );
        });
    }

    public delete(user: Resource) {
        if ( confirm( 'Are you sure to delete user: ' + user.attributes.name ) )
            this.usersService.delete(user.id);
        return false;
    }
}
