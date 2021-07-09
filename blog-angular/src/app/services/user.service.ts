import { Injectable} from "@angular/core";
import { HttpClient, HttpHeaders } from "@angular/common/http";
import { Observable } from "rxjs";
import { global } from "./global"

@Injectable()
export class UserService {
    public url:string;
    public identity;
    public token;

    constructor(
        public _http: HttpClient
    ){
        this.url = global.url;
    }

    register(user:any): Observable<any>{

        let json = JSON.stringify(user);
        let params = 'json='+json;
        let headers = new HttpHeaders().set('Content-Type', 'application/x-www-form-urlencoded');

        return this._http.post(this.url+'register',params, {headers:headers});
    };

    login(user:any ,getToken:null|any): Observable<any>{

        if(getToken != null){
            user.getToken = 'true';
        }

        let json = JSON.stringify(user);
        let params = 'json='+json;
        let headers = new HttpHeaders().set('Content-Type', 'application/x-www-form-urlencoded');

        return this._http.post(this.url+'login' , params , {headers:headers});
    }

    getIdentity(){

        let identity = JSON.parse(localStorage.getItem('identity')!)

        if (identity && identity != 'undefined') {

            this.identity = identity;
            return this.identity;

        }else{
            this.identity = null;
        }

    }

    getToken(){
        let token = localStorage.getItem('token')

        if (token && token != 'undefined') {
            this.token = token;
        }else{
            this.token = null;
        }
    }


}