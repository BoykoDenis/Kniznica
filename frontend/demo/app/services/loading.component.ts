import { Component } from '@angular/core';

@Component({
  selector: 'app-loading',
  template: `
<div class="container">
    <div class="row" style="margin-top:150px">
        <div class="col-lg-1"></div>
        <div class="col-lg-10">
            <h4 style="text-align:center;">
                <img src="http://s.ytimg.com/yt/img/loader-vflff1Mjj.gif" />
                ...loading...
                <img src="http://s.ytimg.com/yt/img/loader-vflff1Mjj.gif" />
            </h4>
        </div>
        <div class="col-lg-1"></div>
    </div>
</div>  `,
})
export class LoadingComponent {
}