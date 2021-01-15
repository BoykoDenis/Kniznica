import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { AuthComponent } from './components/auth.component';
import { AuthRoutingModule } from './auth-routing.module';
import { SharedModule } from '../shared/shared.module';

// Add Forms
import { FormsModule } from '@angular/forms';

@NgModule({
    imports: [CommonModule, SharedModule, AuthRoutingModule,
              // Add Forms
              FormsModule],
    declarations: [
        AuthComponent
    ]
})
export class AuthModule { }
