import { Component, OnInit } from '@angular/core';
import { User } from 'src/app/models/user';
import { UserService } from '../../services/user.service'
import { Router, ActivatedRoute, Params } from "@angular/router"

@Component({
  selector: 'login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.css'],
  providers: [UserService]
})
export class LoginComponent implements OnInit {

  public title: string;
  public user: User;
  public status: string | undefined;
  public token;
  public identity;

  constructor(
    private _userService: UserService,
    private _router: Router,
    private _route: ActivatedRoute,
  ) { 
    this.title = "Login"
    this.user = new User(1,'','','ROLE_USER','','','','')
  }

  ngOnInit(): void {

    // Cierra sesion solo cuando recibe el parametro sure por url
    this.logout();

  }

  onSubmit(form:any) {

    this._userService.login(this.user,null).subscribe(

      response => {

        //TOKEN
        if (response.status != "error") {

          this.status = "success";
          this.token = response;

          alert('success');

          //OBJETO USUARIO

          this._userService.login(this.user,true).subscribe(

            response => {

                this.identity = response;

                // Almacenar datos en local Storage

                localStorage.setItem('token' , this.token);
                localStorage.setItem('identity' , JSON.stringify(this.identity));

                //Redireccionar a home

                this._router.navigate(['inicio']);

            },
            error => {
              console.log( <any>error )
              this.status = "error";
            }
          );
        }else{
          this.status = "error";
          alert('error');
        }
      },
      error => {
        console.log( <any>error )
        this.status = "error";
      }
    );
  }


  logout(){
    this._route.params.subscribe(params => {
      let logout = +params['sure'];

      if(logout == 1){
        localStorage.removeItem('identity');
        localStorage.removeItem('token');

        this.identity = null;
        this.token = null;

        //Redireccionar a home

        this._router.navigate(['inicio']);
      }

    });
  }
  

}
