<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    use HasFactory;
    protected $fillable=['nome', 'imagem'];

    public function rules(){
        return [
            'nome' => 'required|unique:marcas,nome,'.$this->id.'|min:3', //verifica dentro da tabela marcas se é unico
            'imagem' => 'required|file|mimes:png' //mimes-> define as extensões de ficheiro aceites
        ];
        /*
        Caracteristicas dos parametros de validação unique
            1º  nome da tabela a pesquisar o nome
            2º  nome da coluna que será pesquisada (normalmente é ignorado pois é costume ser o nome de referencia para as validações, neste caso "nome")
            3º  id do registro que será desconsiderado na pesquisa
        */
    }
    public function feedback(){
        return [
            'required' => 'O campo :attribute é obrigatório',
            'nome.unique' => 'O nome da marca já existe',
            'imagem.mimes' => 'Apenas são aceites ficheiros do tipo PNG'
        ];
    }

    public function modelos(){
        //UMA marca pertene a VÁRIOS modelos
        return $this->hasMany('App\Models\Modelo');
    }
}
