<?php

    header('Content-Type: application/json');
    include 'conexao.php';

    $metodo = $_SERVER['REQUEST_METHOD'];
    $url = $_SERVER['REQUEST_URI'];

    $path = parse_url($url, PHP_URL_PATH);
    $path = trim($path, '/');

    $path_parts = explode('/',$path);

    $path_parte01 = isset($path_parts[0]) ? $path_parts[0] : '';
    $path_parte02 = isset($path_parts[1]) ? $path_parts[1] : '';
    $path_parte03 = isset($path_parts[2]) ? $path_parts[2] : '';
    $path_parte04 = isset($path_parts[3]) ? $path_parts[3] : '';

    $resposta = [
        'metodo' => $metodo,
        'parte 1' => $path_parte01,
        'parte 2' => $path_parte02,
        'parte 3' => $path_parte03,
        'parte 4' => $path_parte04,
    ];

    switch ($metodo){

        case 'GET':
            
            //Lógica tabela Alunos
            if($path_parte03 == 'alunos'){

                if(isset($path_parts[3])){
                    if(!is_numeric($path_parts[3])){
                        mensagem_simbolos();
                        break;
                    } else{
                        lista_especifica('alunos',  (int)$path_parts[3]);
                        break;
                    }
                } else{
                    lista_completa('alunos');
                    break;
                }
                

            //Logica tabela cursos
            } else  if($path_parte03 == 'cursos'){

                if(isset($path_parts[3])){
                    if(!is_numeric($path_parts[3])){
                        mensagem_simbolos();
                        break;
                    } else{
                        lista_especifica('cursos',  (int)$path_parts[3]);
                        break;
                    }
                } else{
                    lista_completa('cursos');
                    break;
                }

            } else{
                echo json_encode([
                    'mensagem' => 'TABELA INCORRETA!'
                ]);
            }

            break;

        case 'POST':
            
            if($path_parte03 == 'alunos'){
                if(isset($path_parts[3])){
                    break;
                } else{
                    break;
                }
                

            //Logica tabela cursos
            } else  if($path_parte03 == 'cursos'){
                add_curso();
                break;

            } else{
                echo json_encode([
                    'mensagem' => 'TABELA INCORRETA!'
                ]);
            }

            break;

        case 'PUT':
            break;

        case 'DELETE':
            break;
        
        default:
            echo json_encode([
                'mensagem' => 'MÉTODO INCORRETO!'
            ]);
            break;
    }

    //echo json_encode($resposta);


    function lista_completa($tabela) {
        global $conexao;

        $resultado = $conexao->query("SELECT * FROM {$tabela}");
        $dados = $resultado->fetch_all(MYSQLI_ASSOC);
        
        echo json_encode([
            'mensagem' => "{$tabela} COMPLETO!",
            'dados' => $dados
        ]);
    }

    function lista_especifica($tabela, $id) {
        global $conexao;


        $coluna_id = $tabela === 'cursos' ? 'id_curso' : 'id';
        $query = "SELECT * FROM `{$tabela}` WHERE `{$coluna_id}` = ?";
        $stmt = $conexao->prepare($query);
        $stmt->bind_param("i", $id); 
        $stmt->execute();
        $dados = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        echo json_encode([
            'mensagem' => "{$tabela} Específico!",
            'dados' => $dados
        ]);
    }

    function add_curso(){
        global $conexao;
        $nome_curso = $_GET['nome_curso'];
        $sql = "INSERT INTO cursos (nome_curso) VALUE ('$nome_curso')";

        if($conexao->query($sql) === TRUE){
            mensagem_sucess();
        } else{
            mensagem_error();
        }
    }
    
    function mensagem_simbolos() {
        echo json_encode([
            'mensagem' => 'LETRAS E SÍMBOLOS NÃO SÃO PERMITIDOS!'
        ]);
    }

    function mensagem_error() {
        echo json_encode([
            'mensagem' => 'ERROR!'
        ]);
    }

    function mensagem_sucess() {
        echo json_encode([
            'mensagem' => 'SUCESSO!'
        ]);
    }

?>