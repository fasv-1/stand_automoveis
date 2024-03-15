<?php
namespace App\Repositories;

use Illuminate\Database\Eloquent\Model; // permite tipar um elemento como sendo da class Model

abstract class AbstractRepository{

  public function __construct(Model $model)
  {

    $this->model = $model;
  }

  public function selectAtributosRegistrosRelacionados($atributos)
  {

    $this->model = $this->model->with($atributos);
    //a query está a ser montada

  }

  public function filtro($filtros)
  {

    $filtros = explode(';', $filtros);

    foreach ($filtros as $key => $condicao) {

      $c = explode(':', $condicao); // o metodo explode vai utilizar os ":" para saber onde separar a string passada como parametro no filtro e dividir num array
      $this->model = $this->model->where($c[0], $c[1], $c[2],);
    }
  }

  public function selectAtributos($atributos)
  {
    $this->model = $this->model->selectRaw($atributos);
  }

  public function getResultado()
  {
    return $this->model->get();
  }

  public function getResultadoPaginado($numeroDePaginas)
  {
    return $this->model->paginate($numeroDePaginas);
  }

}

?>