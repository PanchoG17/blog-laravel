<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{

    public function prueba(Request $request){
        return "Metodo prueba UserController";
    }

    public function register(Request $request){

        // Recoger datos de Usuario

        $json = $request->input('json', null);

        $params = json_decode($json); // OBJETO
        $params_array = json_decode($json,true); // ARRAY

        // var_dump($params_array);


        if (!empty($params) && !empty($params_array)) {

            //Limpiar datos (Trim)

            $params_array = array_map('trim',$params_array);

            // Validar datos

            $validate = \Validator::make($params_array,[
                'name'      => 'required|alpha',
                'surname'   => 'required|alpha',
                'email'     => 'required|email|unique:users',   // Verifica dupiclados
                'password'  => 'required'
            ]);

                if ($validate->fails()) {

                //Validacion Error

                    $data = array(
                        'status' => 'error',
                        'code' => 400,
                        'message' => 'El usuario no se pudo registrar',
                        'errors' => $validate->errors()
                    );

                }else {

                //Validacion OK

                    // Cifrar contraseña

                    $pwd = hash('sha256', $params->password);


                    // Crear el usuario

                    $user = new User();

                    $user->name = $params_array['name'];
                    $user->surname = $params_array['surname'];
                    $user->email = $params_array['email'];
                    $user->password = $pwd;
                    $user->role = 'ROLE_USER';



                    //Crear el usuario

                    $user->save();

                    $data = array(
                        'status' => 'success',
                        'code' => 200,
                        'message' => 'El usuario se ha registrado correctamente',
                        'user' => $user
                    );
                }

        }else {

            $data = array(
                'status' => 'error',
                'code' => 400,
                'message' => 'Los datos ingresados no son correctos',
            );

        }

        return response()->json($data, $data['code']); //Devolver mensajes OK / ERROR en json

    } //// REGISTRO DE USUARIO

    public function login(Request $request){

        $jwtAuth = new \JWT;

        //Recibir datos por POST 

        $json = $request->input('json',null);
        $params = json_decode($json);
        $params_array = json_decode($json,true);

        //Validar los datos

        $validate = \Validator::make($params_array,[
            'email'     => 'required|email',
            'password'  => 'required'
        ]);

            if ($validate->fails()) {

            //Validacion Error

                $data = array(
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'Ingrese los datos Correctamente',
                    'errors' => $validate->errors()
                );

            }else {

            //Validacion OK

                //Cifrar la contraseña

                $pwd = hash('sha256', $params->password);

                //Devolver token o datos

                $signup = $jwtAuth->signup($params->email,$pwd); //Token

                if (isset($params->getToken)) {
                    $signup = $jwtAuth->signup($params->email,$pwd,true); //Datos
                }
            }

        return response()->json($signup,200);

    } //// LOGIN DE USUARIO

    public function update(Request $request){

        $token = $request->header('Authorization'); //Recoger token de la cabecera 
        $jwtAuth = new \JWT;

        $checkToken = $jwtAuth->checkToken($token);

        if ($checkToken) {
            echo '<h1>Login correcto</h1>';
        }else {
            echo '<h1>Login incorrecto</h1>';
        }

        die();

    } //// UPDATE DE USUARIO 

}


