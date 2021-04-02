<?php

namespace App\Helpers;

use firebase\JWT\JWT;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class JwtAuth{

    public function signup(){

        // Buscar si existe el usuario con esas credenciales
        // Comprobar si son correctas 
        // Generar el token con los datos del usuario indentificado
        // Devolver los datos decodificados, en funcion de un parametro

        return 'metodo de la clase JWTAuth';

    }

}
