<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller

//Verificar o token em jwt.io
{
    public function login(Request $request){
        
        $credendiais = $request->all(['email', 'password']); //dentro do request->all pode ser retornado dentro de um array apenas os parametros pertendidos 

        //autenticação (email e senha)
        $token = auth('api')->attempt($credendiais); //através do metodo auth e passando qual o metodo de autenticação('api') pode ser usado o metodo attempt para fazer uma tentativa de validação através das credenciais email e password 
        

        //retornar um Json Web Token
        if($token){ 
            return response()->json(['token' => $token]);
        }else{
            return response()->json(['erro' => 'Usuário ou password inválido'], 403);
        }

        //401 = unauthorized -> não autorizado
        //403 = forbidden ->proibido (login inválido)
        // dd($token);
        
    }

    public function logout(){
        auth('api')->logout();
        return response()->json(['msg' => 'Logged out']);
    }

    public function refresh(){
        //permite fazer um refresh no token existe para que este não expire
        $token = auth('api')->refresh(); //cliente encaminhe um jwt válido

        return response()->json(array('token' => $token));
    }

    public function me(){
        
        return response()->json(auth()->user()); // ao enviar os dados através do json, este não envia ,automaticamente, a password por segurança 
    }
}
