import { Component, OnInit } from '@angular/core';
import { User } from 'src/app/models/user';
import { UserService } from 'src/app/services/user.service';
import { global } from 'src/app/services/global';


@Component({
  selector: 'app-user-edit',
  templateUrl: './user-edit.component.html',
  styleUrls: ['./user-edit.component.css'],
  providers: [UserService]
})
export class UserEditComponent implements OnInit {

  public title: string;
  public user: User;
  public url;
  public identity;
  public token;
  public status;

  public richEditor: Object = {
    placeholderText: '',
    attribution: false,
    charCounterCount: true,
    toolbarButtons: ['bold', 'italic', 'underline'],
    toolbarButtonsXS: ['bold', 'italic', 'underline'],
    toolbarButtonsSM: ['bold', 'italic', 'underline'],
    toolbarButtonsMD: ['bold', 'italic', 'underline']
  };

  public avatarUploader = {
    multiple: false,
    formatsAllowed: ".jpg,.png,.gif,.jpeg",
    maxSize: "50",
    uploadAPI:  {
      url:global.url+"user/upload",
      method:"POST",
      headers: {
        "Authorization" : this._userService.getToken()
      }
    },
    theme: "attachPin",
    hideProgressBar: false,
    hideResetBtn: false,
    hideSelectBtn: false,
    fileNameIndex: true,
    attachPinText: 'Sube tu avatar de Usuario'
  };

  constructor(
    private _userService : UserService
  ) {

    this.title = "Ajustes de Usuario";
    this.url = global.url;
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

  avatarUpload(event){

    let data = JSON.parse(event.response);

    this.user.image = data.image ;
  }

}
