<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Post;

use app\Helpers\JwtAuth;

class PostController extends Controller
{

    public function __construct(){
        $this->middleware('api.auth', ['except' => [

            'index',
            'show',
            'getImage',
            'getPostByCategory',
            'getPostByUser'
        
            ]]);  // Agregar Middleware de Autenticacion
    }

    private function getIdentity($request){

            // recoger usuario identificado

            $jwt = new \JWT;
            $token = $request->header('authorization');
            $user = $jwt->checkToken($token,true);

            return $user;

    }

    public function index(){
        $posts = Post::all()->load('category');

        return response()->json([
            'code' => '200',
            'status' => 'success',
            'posts' => $posts
        ],200);
    } //// Listar Posts

    public function show($id){

        $post = Post::findOrFail($id)->load('category');

        if (is_object($post)) {

            $data = [
            'code' => '200',
            'status' => 'success',
            'post' => $post
            ];

        }else{

            $data = [
                'code' => '404',
                'status' => 'error',
                'message' => 'El post no existe o fue eliminado'
                ];
            }

        return response()->json($data,$data['code']);

    } //// Mostrar un Post

    public function store(Request $request){

        // recoger datos por post

        $json = $request->input('json',null);
        $params = json_decode($json);
        $params_array = json_decode($json,true);



        if (!empty($params_array)) {

            $user = $this->getIdentity($request);

            // validar datos de usuario 
    
            $validate = \Validator::make($params_array, [
    
                'title' => 'required',
                'content' => 'required',
                'category_id' => 'required',
                'image' => 'required'
    
            ]);

            if ($validate->fails()) {
                
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'mesagge' => $validate->errors()
                ];

            }else {

                // Guardar el post

                $post = new Post();

                $post->user_id = $user->sub;
                $post->category_id = $params->category_id;
                $post->title = $params->title;
                $post->content = $params->content;
                $post->image = $params->image;

                $post->save();


                // devolver respuesta

                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'mesagge' => 'El post se guardó correctamente',
                    'post' => $post
                ];

            }

        }else {

            $data = [
                'code' => 400,
                'status' => 'error',
                'mesagge' => 'datos incorrectos'
            ];

        }

        return response()->json($data,$data['code']);



    } //// Guardar un Post

    public function update($id,Request $request){

        // recoger datos por post

        $json = $request->input('json',null);
        $params_array = json_decode($json, true);

        if (!empty($params_array)) {

            $validate = \Validator::make($params_array, [

                'title' => 'required',
                'content' => 'required',
                'category_id' => 'required'

            ]);

            if ($validate->fails()) {

                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'mesagge' => $validate->errors()
                ];

            }else {

                $user = $this->getIdentity($request);

                // eliminar lo que no queremos actualizar

                unset($params_array['id']);
                unset($params_array['user_id']);
                unset($params_array['created_at']);

                // actualizar el registro

                $post = Post::find($id);

                if (is_object($post)) {

                    if ($user->sub == $post->user_id) {

                        $post->title = $params_array['title'];
                        $post->content = $params_array['content'];
                        $post->category_id = $params_array['category_id'];
        
                        $post->update();
        
                        // devolver mensaje
        
                        $data = [
                            'code' => 200,
                            'status' => 'success',
                            'mesagge' => 'El post se actualizo correctamente',
                            'post' => $post,
                            'changes' => $params_array
                        ];
                    }else {
                        $data = [
                            'code' => 400,
                            'status' => 'error',
                            'message' => 'No tienes permiso para modificar este post'
                        ];
                    }
                }else {
                    $data = [
                        'code' => 400,
                        'status' => 'error',
                        'message' => 'El post no existe o fue eliminado'
                    ];
                }
            }
        }else {
            
            $data = [
                'code' => 400,
                'status' => 'error',
                'mesagge' => 'datos incorrectos'
            ];

        }

        return response()->json($data,$data['code']);


    } //// Actualizar un Post

    public function destroy($id, Request $request){

        $user = $this->getIdentity($request);

        // obtener post por id
        $post = Post::find($id);

        if (is_object($post)) {


            if ($user->sub == $post->user_id) {

                // borrar post
                $post->delete();

                // devolver mensaje

                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'message' => 'El post se elimino correctamente',
                    'post' => $post
                ];

                }else {
                    $data = [
                        'code' => 400,
                        'status' => 'error',
                        'message' => 'No tienes permiso para borrar este post'
                    ];
                }

        }else {
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'El post no existe o fue eliminado'
            ];
        }


        return response()->json($data,$data['code']);


    } //// Eliminar un post

    public function upload(Request $request){

        // recoger la imagen de la peticion
        $image = $request->file('file0');

        // validar la imagen
        $validate = \Validator::make($request->all(),[
            'file0' => 'required|image|mimes:jpg,jpeg,png'
        ]);

        if (!$image | $validate->fails()) {
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'Error al subir la imagen'
            ];
        }else {

            // guardar la imagen

            $image_name = time().$image->getClientOriginalName();
            \Storage::disk('post-img')->put($image_name, \File::get($image));

            $data = [
                'code' => 200,
                'status' => 'succes',
                'message' => 'Imagen subida correctamente',
                'image' => $image_name
            ];

        }

        return response()->json($data,$data['code']);

    } //// Subir una imagen

    public function getImage($filename){

        // comprobar si existe

        $isset = \Storage::disk('post-img')->exists($filename);

        if ($isset) {

            // conseguir la imagen
            $file = \Storage::disk('post-img')->get($filename);

            // devolver la imagen
            return new Response($file,200);

        }else{
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'La imagen no existe o fue eliminada'
            ];

            return response()->json($data,$data['code']);
        }


    } //// Obtener imagen de un post

    public function getPostByCategory($id){

        $posts = Post::where('category_id',$id)->get();

            $data = [
                'code' => 200,
                'status' => 'success',
                'posts' => $posts
            ];

        return response()->json($data,$data['code']);

    } //// Obtener posts por categoria

    public function getPostByUser($id){

        $posts = Post::where('user_id',$id)->get();

        $data = [
            'code' => 200,
            'status' => 'success',
            'posts' => $posts
        ];

        return response()->json($data,$data['code']);

    } //// Obtener posts por usuario



}
