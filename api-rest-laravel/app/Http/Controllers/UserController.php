<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\User;

class UserController extends Controller
{

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

    } //// Registro de Usuario

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

    } //// Login de Usuario

    public function update(Request $request){

        //Comprobar si el usuario esta logueado

        $token = $request->header('Authorization'); //Recoger token de la cabecera 
        $jwtAuth = new \JWT; //Instanciar JWT class
        $checkToken = $jwtAuth->checkToken($token);

        // Recoger datos por post

        $json = request()->input('json', null);
        $params_array = json_decode($json,true);

        if ($checkToken && !empty($params_array)) {

            // Actualizar Usuario.

                $user = $jwtAuth->checkToken($token,true);  //datos del usuario identificado

                // Validar los datos

                $validate = \Validator::make($params_array,[

                    'name'      => 'required|alpha',
                    'surname'   => 'required|alpha',
                    'email'     => 'required|email|unique:users'.$user->sub

                ]);

                // Quitar campos que no se actualizan 

                unset($params_array['id']);
                unset($params_array['role']);
                unset($params_array['password']);
                unset($params_array['created_at']);
                unset($params_array['remember_token']);

                // Actualizar usuario en la DB 

                $user_update = User::where('id', $user->sub)->update($params_array);


                // Devolver array con los resultados

                $data = array(
                    'code' => 200,
                    'status' => 'success',
                    'message' => 'El usuario se ha actualizado correctamente',
                    'user' => $user,
                    'changes' => $params_array
                );

        }else {

            $data = array(
                'code' => 400,
                'status' => 'error',
                'message' => 'El usuario no está logueado'
            );
        }

        return response()->json($data,$data['code']);

    } //// Update de Usuario

    public function upload(Request $request){


        //recoger datos de la peticion 
        $image = $request->file('file0');

        //validar imagen

        $validate = \Validator::make($request->all(), [
            'file0' => 'required|image|mimes:jpg,png,jpeg'
        ]);



        //guardar imagen
        if(!$image || $validate->fails()){

            $data = array(
                'code' => 400,
                'status' => 'Error',
                'message' => $validate->errors()
            );

        }else {
            $image_name = time()."-".$image->getClientOriginalName();
            \Storage::disk('user-img')->put($image_name, \File::get($image));
        
            //devolver el resultado

            $data = array(
                'code' => 200,
                'status' => 'success',
                'image' => $image_name
            );
        }

        return response()->json($data,$data['code']);

    } //// Uploader de IMGs

    public function getImage($filename){

        $isset = \Storage::disk('user-img')->exists($filename);

        if ($isset) {
            $file = \Storage::disk('user-img')->get($filename);
            return new Response($file, 200);
        }else{

            $data = array(
                'code' => 400,
                'status' => 'error',
                'message' => 'La imagen no existe'
            );

            return response()->json($data,$data['code']);

        }



    } //// Obtener Avatar

    public function getUser($id){

        $user = User::find($id);

        if(is_object($user)){

            $data = array(
                'status' => 'success',
                'code' => 200,
                'user' => $user
            );

        }else{
            $data = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'El usuario no existe.'
            );
        }

        return response()->json($data,$data['code']);

    } //// Obtener Info de Usuario

}


