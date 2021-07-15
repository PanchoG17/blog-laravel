import { Component, OnInit } from '@angular/core';
import { User } from '../../models/user';
import { UserService } from 'src/app/services/user.service';

@Component({
  selector: 'registro',
  templateUrl: './registro.component.html',
  styleUrls: ['./registro.component.css'],
  providers: [UserService]
})
export class RegistroComponent implements OnInit {

  public title: string;
  public user: User;
  public status: string | undefined;
  public mesage;

  constructor(
    private _userService: UserService
  ) { 
    this.title = "Registrate";
    this.user = new User(1,'','','ROLE_USER','','','','')
  }

  ngOnInit(): void {
  }

  onSubmit(form:any){
    this._userService.register(this.user).subscribe(

      response => {
        
        if (response.status == "success") {
          this.status = response.status;
          this.mesage = response.mesage;

          form.reset();

        }else{
          console.log(response)
          this.status = "error"
        }

      },

      error => {
        console.log(<any>error);
        this.status = "error"
      }


    );
  };

}
