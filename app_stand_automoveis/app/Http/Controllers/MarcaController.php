<?php

namespace App\Http\Controllers;

use App\Models\Marca;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Repositories\MarcaRepository;

class MarcaController extends Controller
{
    public function __construct(Marca $marca)
    { // Injeção do Model (é injetado no construtor do controlador a instancia do objecto para que se torne global, tornando assim os metodos mais uniformes) ->>> é facultativo
        $this->marca = $marca;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //forma mais inxuta de filtrar as pesquisas através de endpoints

        $marcaRepository = new MarcaRepository($this->marca);

        if ($request->has('atributos_modelos')) {
            $atributos_modelos = 'modelos:'.$request->atributos_modelos;
            $marcaRepository->selectAtributosRegistrosRelacionados($atributos_modelos); //contrução da query
        } else {
            $marcaRepository->selectAtributosRegistrosRelacionados("modelos"); //contrução da query
        }

        //adiciona filtros de pesquisa
        if ($request->has('filtro')) {

            $marcaRepository->filtro($request->filtro);

        }

        if ($request->has('atributos')) {

            $marcaRepository->selectAtributos($request->atributos);

        }

        return response()->json($marcaRepository->getResultadoPaginado(3), 200);



        //----------------------------------------------------------------------



        // $marcas = array();


        // // verifica se existe a variavel a ser passada no endpoint
        // if ($request->has('atributos_modelos')) {
        //     $atributos_modelos = $request->atributos_modelos;
        //     $marcas = $this->marca->with("modelos:marca_id,$atributos_modelos"); //contrução da query
        // } else {
        //     $marcas = $this->marca->with("modelos"); //contrução da query
        // }

        // //adiciona filtros de pesquisa
        // if ($request->has('filtro')) {

        //     $filtros = explode(';', $request->filtro);

        //     foreach ($filtros as $key => $condicao) {

        //         $c = explode(':', $condicao); // o metodo explode vai utilizar os ":" para saber onde separar a string passada como parametro no filtro e dividir num array
        //         $marcas = $marcas->where($c[0], $c[1], $c[2],);
        //     }
        // }

        // if ($request->has('atributos')) {

        //     //passagem de atributos no endpoint para pesquisa
        //     $atributos = $request->atributos;
        //     $marcas = $marcas->selectRaw($atributos)->get(); //o metodo selectRaw() recebe os atributos todos como se fosse uma string e gere-os separadamente

        // } else {
        //     $marcas = $marcas->get();
        // }

        // return response()->json($marcas, 200);

        // $marcas = Marca::all(); //estatico 
        // return $this->marca->with('modelos')->get();
        //all() -> cria um objecto de consulta + get() = collection (neste caso com o metodo wuth, não ia receber o valor adicional da relação, pois não ia ser possivel alterar a consulta)
        //get() -> modifica a consulta e entrega a collection 

        //Nos casos dos endpoints que devolvem os dados corretamente o código do response já é atribuido corretamente!200
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
        // $marca = Marca::create($request->all());  //estático
        // $regras = [
        //     'nome' => 'required|unique:marcas', //verifica dentro da tabela marcas se é unico
        //     'imagem' => 'required'
        // ];
        // $feedback = [
        //     'required' => 'O campo :attribute é obrigatório',
        //     'nome.unique' => 'O nome da marca já existe'
        // ];
        // $request->validate($regras, $feedback); // o client necessita de estipular no header do request o KEY - Accept ; VALUE - aplication/json para que quando houver um erro, o validate não redirecione a aplicação para a página anterior 

        $request->validate($this->marca->rules(), $this->marca->feedback());

        $image = $request->file('imagem');
        $image_urn = $image->store('imagens', 'public'); //recebe 2 parametros, (o diretorio onde vai ser armazenado, o disco onde vai ser armazernado) ver config/filesystems.php (neste caso vai ser armazenado em storage/app/public/image)
        //recebe o diretorio o url com o nome da imagem dado pelo laravel

        $marca = $this->marca->create([
            'nome' => $request->nome,
            'imagem' => $image_urn // PARA QUE QUALQUER FICHEIRO SEJA ACESSIVEL ATRAVÉS DO ACESSO PUBLICO, BASTA NO TERMINAL ESCREVER: php artisan storage:link 
        ]);

        return $marca;
        //Nos casos dos endpoints que devolvem os dados corretamente o código do response já é atribuido corretamente!201
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    { // como já existe um bind entre o parametro enviado e o Model, o laravel procura na BD um registro com esse id //sugestão de tipo
        $marca = $this->marca->with('modelos')->find($id); //with adiciona os detalhes do relacionamento
        if ($marca === null) { // === operador identico, do mesmo tipo e ter o mesmo valor
            return response()->json(['error' => 'Recurso pesquisado não existe'], 404); //utilizando o helper response() com o metodo json(), é possivél como segundo parametro alterar o estado da responde! Em vez de 200 como mensagem recebida com sucesso, envia um 404 (ver https://developer.mozilla.org/pt-BR/docs/Web/HTTP/Status )
        }
        return $marca;
        //Nos casos dos endpoints que devolvem os dados corretamente o código do response já é atribuido corretamente!200
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Marca $marca) //sugestão de tipo
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // print_r($request->all());
        // echo '<hr>';
        // print_r($marca->getAttributes());
        // a diferença entre o PUT e o PATCH é simplesmente a nivel semantico, o PUT é utilizado para atualizar todo os registro, enquando o PATCH é apenas para uma parte

        $marca = $this->marca->find($id);

        if ($marca === null) {
            return response()->json(['error' => 'Impossivel realizar o update, o id identificado não existe'], 404);
        }

        $regrasDinamicas = [];

        if ($request->method() === 'PATCH') { //update com possibilidade de lidar com o metodo PUT e PATCH

            //percorrer todas as regras definidas no Model
            foreach ($marca->rules() as $input => $regra) {

                //coletar apenas as regras aplicáveis aos parâmetros parciais da requesição
                if (array_key_exists($input, $request->all())) { // array_key_exists(chave a ser pesquisada, array onde pesquisar essa chave)
                    $regrasDinamicas[$input] = $regra;
                }
            }

            $request->validate($regrasDinamicas, $marca->feedback());
        } else {
            $request->validate($marca->rules(), $marca->feedback());
        }
        // dd($request->method()); //retorna o verbo http utilizado 

        $marca->fill($request->all());
        
        //remove o arquivo antigo caso um novo arquivo tenha sido enviado no request
        if ($request->file('imagem')) {
            Storage::disk('public')->delete($marca->imagem);
            $image = $request->file('imagem');
            $image_urn = $image->store('imagens', 'public');
            $marca->imagem = $image_urn;
        }


        //Como o laravel não reconhece o verbo PUT e PATCH é necessário defenir no Postman por baixo dos valore e através do metodo POST o key "_method" e o valor "put" ou "patch"


        // dd($marca);

        

        $marca->save(); //passando um id como parametro, o metodo save() já consegue interpretar que se trata de uma atualização 


        // dd($marca->getAttributes(''));

        // $marca->update([
        //     'nome' => $request->nome,
        //     'imagem' => 
        // ]);
        // return $marca;
        //Nos casos dos endpoints que devolvem os dados corretamente o código do response já é atribuido corretamente!200
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $marca = $this->marca->find($id);
        if ($marca === null) {
            return response()->json(['error' => 'Impossivel apagar o registro, o id identificado não existe'], 404);
        }

        //remove o arquivo antigo caso um novo arquivo tenha sido enviado no request
        Storage::disk('public')->delete($marca->imagem);

        $marca->delete();

        return ['msg' => 'A marca foi removida com sucesso!'];
    }
}
