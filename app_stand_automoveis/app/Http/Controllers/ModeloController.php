<?php

namespace App\Http\Controllers;

use App\Models\Modelo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Repositories\ModeloRepository;

class ModeloController extends Controller
{

    public function __construct(Modelo $modelo)
    {
        $this->modelo = $modelo;
    }


    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //Verificar repositório ModeloRepository em App\Repositories\ModeloRepository 
        $modeloRepository = new ModeloRepository($this->modelo);

        if ($request->has('atributos_marca')) {
            $atributos_marca = 'marca:id,'.$request->atributos_marca;
            $modeloRepository->selectAtributosRegistrosRelacionados($atributos_marca); //contrução da query
        } else {
            $modeloRepository->selectAtributosRegistrosRelacionados("marca"); //contrução da query
        }

        //adiciona filtros de pesquisa
        if ($request->has('filtro')) {

            $modeloRepository->filtro($request->filtro);

        }

        if ($request->has('atributos')) {

            $modeloRepository->selectAtributos($request->atributos);

        }

        return response()->json($modeloRepository->getResultado(), 200);
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate($this->modelo->rules());

        $image = $request->file('imagem');
        $image_urn = $image->store('imagens/modelos', 'public');

        $modelo = $this->modelo->create([
            'marca_id' => $request->marca_id,
            'nome' => $request->nome,
            'imagem' => $image_urn,
            'numero_portas' => $request->numero_portas,
            'lugares' => $request->lugares,
            'air_bag' => $request->air_bag,
            'abs' => $request->abs,
        ]);

        return $modelo;
    }



    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $modelo = $this->modelo->with('marca')->find($id); //with adiciona os detalhes do relacionamento
        if ($modelo === null) {
            return response()->json(['error' => 'Recurso pesquisado não existe'], 404);
        }
        return $modelo;
    }



    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Modelo $modelo)
    {
        //
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $modelo = $this->modelo->find($id);

        if ($modelo === null) {
            return response()->json(['error' => 'Impossivel realizar o update, o id identificado não existe'], 404);
        }

        $regrasDinamicas = [];

        if ($request->method() === 'PATCH') {

            //percorrer todas as regras definidas no Model
            foreach ($modelo->rules() as $input => $regra) {

                //coletar apenas as regras aplicáveis aos parâmetros parciais da requesição
                if (array_key_exists($input, $request->all())) {
                    $regrasDinamicas[$input] = $regra;
                }
            }

            $request->validate($regrasDinamicas);
        } else {
            $request->validate($modelo->rules());
        }

        $modelo->fill($request->all());
        //remove o arquivo antigo caso um novo arquivo tenha sido enviado no request
        if ($request->file('imagem')) {
            Storage::disk('public')->delete($modelo->imagem);
            $image = $request->file('imagem');
            $image_urn = $image->store('imagens/modelos', 'public');
            $modelo->imagem = $image_urn;
        }

        $modelo->save();
    }

    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $modelo = $this->modelo->find($id);
        if ($modelo === null) {
            return response()->json(['error' => 'Impossivel apagar o registro, o id identificado não existe'], 404);
        }

        //remove o arquivo antigo caso um novo arquivo tenha sido enviado no request
        Storage::disk('public')->delete($modelo->imagem);

        $modelo->delete();

        return ['msg' => 'O modelo foi removido com sucesso!'];
    }
}
