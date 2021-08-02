import { Component , OnInit , DoCheck } from '@angular/core';
import { UserService } from './services/user.service';
import { global } from './services/global';

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css'],
  providers: [UserService]
})
export class AppComponent implements OnInit, DoCheck{

  public identity;
  public token;
  public url;

  constructor(
    public _userService: UserService
  ){

    this.url = global.url;
    this.loadUser();

  }

  loadUser(){
    this.identity = this._userService.getIdentity();
    this.token = this._userService.getToken();
  }

  ngDoCheck(){
    this.loadUser();
  }

  ngOnInit(){

  }
}
