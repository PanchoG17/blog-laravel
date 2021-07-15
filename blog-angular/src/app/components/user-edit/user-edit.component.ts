import { Component, OnInit } from '@angular/core';
import { User } from 'src/app/models/user';
import { UserService } from 'src/app/services/user.service';


@Component({
  selector: 'app-user-edit',
  templateUrl: './user-edit.component.html',
  styleUrls: ['./user-edit.component.css'],
  providers: [UserService]
})
export class UserEditComponent implements OnInit {

  public title: string;
  public user: User;
  public identity;
  public token;
  public status;
  public options: Object = {
    placeholderText: '',
    attribution: false,
    toolbarButtons: ['bold', 'italic', 'underline']
  }

  constructor(
    private _userService : UserService
  ) {

    this.title = "Ajustes de Usuario";
    this.token = this._userService.getToken();
    this.identity = this._userService.getIdentity();
    this.user = new User(1,'','','ROLE_USER','','','','')

    // Rellenar objeto de usuario
    this.user = this.identity;
  }

  ngOnInit(): void {
  }

  onSubmit(form:any){

    this._userService.update(this.token , this.user).subscribe(
      response => {
        if (response.status == 'success') {
          localStorage.removeItem('identity');
          localStorage.setItem('identity',JSON.stringify(this.identity));

          this.status = 'success';
        }else{
          this.status = 'error';
        }
      },
      error => {
        this.status = 'error'
        console.log(error)
      }
    );

  }

}
