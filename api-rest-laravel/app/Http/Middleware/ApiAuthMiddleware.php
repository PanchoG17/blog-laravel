<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        //Comprobar si el usuario esta logueado

        $token = $request->header('Authorization'); //Recoger token de la cabecera 
        $jwtAuth = new \JWT; //Instanciar JWT class
        $checkToken = $jwtAuth->checkToken($token);

        if ($checkToken) {

        return $next($request);

        }else{

            $data = array(
                'code' => 400,
                'status' => 'error',
                'message' => 'El usuario no está logueado'
            );
            return response()->json($data,$data['code']);

        }
    }
}
