<?php

    //4 parametros basicos para conectar ao banco de dados
    $host = 'localhost';
    $usuario = 'root';
    $senha = '';
    $banco = 'etecmcm';

    $conexao = new mysqli($host,$usuario,$senha,$banco);

    //Tratativa de erros para conexao com banco
    if($conexao -> connect_error){
        die('Falha de conexão' . $conexao -> connect_error);
    } 

?>