<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Category;

class CategoryController extends Controller
{

    public function __construct(){
        $this->middleware('api.auth', ['except' => ['index','show']]);  // Agregar Middleware de Autenticacion
    }

    public function index(){

        $categories = Category::all();

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'categories' => $categories
        ]);

    } // Listar Categorias

    public function show($id){
        $category = Category::find($id);

        if (is_object($category)) {

            $data = [
                'code' => 200,
                'status' => 'success',
                'name' => $category
            ];

        }else{
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'La categoria no existe'
            ];
        }

        return response()->json($data, $data['code']);



    } // Mostrar una Categoria

    public function store(Request $request){

        // recoger datos por post

        $json = $request->input('json' , null);
        $params_array = json_decode($json, true);

        if (!empty($params_array)) {


            // Validar los datos

            $validate = \Validator::make($params_array,[
                'name' => 'required|unique:categories'
            ]);


            if ($validate->fails()) {

                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'message' => $validate->errors()
                ];

            }else{

                // Guardar la categoria 

                $category = new Category();
                $category->name = $params_array['name'];
                $category->save();

                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'category' => $category 
                ];
            }

        }else{
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'Category is empty' 
            ];
        }

        // devolver resultado

        return response()->json($data,$data['code']);

    } // Guardar una Categoria

    public function update($id, Request $request){

        // Recoger datos por post

        $json = $request->input('json',null);
        $params_array = json_decode($json,true);

        if (!empty($params_array)) {

            // Validar los datos

            $validate = \Validator::make($params_array, [
                'name' => 'required|unique:categories'
            ]);

            if ($validate->fails()) {
                $data = [
                    'code' => 404,
                    'status' => 'error',
                    'message' => $validate->errors()
                ];
            }else {

                // Quitar los innecesarios

                unset($params_array['id']);
                unset($params_array['created_at']);

                // Actualizar el registro(categoria)

                $category = Category::find($id);

                if (is_object($category)) {

                    $category->name = $params_array['name'];
                    $category->update();

                    $data = [
                        'code' => 200,
                        'status' => 'success',
                        'message' => 'Category Updated',
                        'info' => $params_array
                    ];

                }else {
                    $data = [
                        'code' => 404,
                        'status' => 'error',
                        'message' => 'La categoria no existe.',
                    ];
                }
            }

        }else {
            $data = [
                'code' => 400,
                'status' => 'error',
            ];
        }

        // Devolver respuesta

        return response()->json($data,$data['code']);

    } // Actualizar una Categoria

}
