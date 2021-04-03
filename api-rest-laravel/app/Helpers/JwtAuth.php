<?php

namespace App\Helpers;

use Firebase\JWT\JWT;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class JwtAuth{

    public $key;
    public function __construct(){
        $this->key = 'clave_secreta_17011996';
    }

    public function signup($email, $password, $getToken = null){

        //// Buscar si existe el usuario con esas credenciales

        $user = User::where([
            'email'     =>   $email,
            'password'  =>   $password

        ]) -> first();

        //// Comprobar si son correctas 

        $signup = false;
        if(is_object($user)){
            $signup = true;
        }

        // Generar el token con los datos del usuario indentificado

        if($signup){

            $token = array(

                'sub'     =>   $user->id,
                'email'   =>   $user->email,
                'name'    =>   $user->name,
                'surname' =>   $user->surname,
                'iat'     =>   time(),
                'exp'     =>   time() + (7*24*60*60)

            );


            $jwt = JWT::encode($token, $this->key, 'HS256');        // Generar Token Cifrado
            $decoded = JWT::decode($jwt, $this->key, ['HS256']);    // Descrifrar Token


            // Devolver los datos decodificados, en funcion de un parametro ($getToken)

            if (is_null($getToken)) {
                $data = $jwt;
            }else {
                $data = $decoded;
            }

        }else{

            $data = array(

                'status'  => 'error',
                'message' => 'login incorrecto'

            );
        }

        return $data;

    }

}
