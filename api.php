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
                        echo json_encode([
                            'mensagem' => 'LETRAS E SIMBOLOS NAO SAO PERMITIDOS!'
                        ]);
                        break;
                    } else{
                        echo json_encode([
                            'mensagem' => 'ALUNOS ESPECIFICO!',
                            'Id' => $path_parts[3]
                        ]);
                        break;
                    }
                } else{
                    echo json_encode([
                        'mensagem' => 'ALUNOS COMPLETO!'
                    ]);
                    break;
                }

                

            //Logica tabela cursos
            } else  if($path_parte03 == 'cursos'){

                if(isset($path_parts[3])){
                    if(!is_numeric($path_parts[3])){
                        echo json_encode([
                            'mensagem' => 'LETRAS E SIMBOLOS NAO SAO PERMITIDOS!'
                        ]);
                        break;
                    } else{
                        echo json_encode([
                            'mensagem' => 'CURSO ESPECIFICO!',
                            'Id' => $path_parts[3]
                        ]);
                        break;
                    }
                } else{
                    echo json_encode([
                        'mensagem' => 'CURSOS COMPLETO!'
                    ]);
                    break;
                }

            } else{
                echo json_encode([
                    'mensagem' => 'TABELA INCORRETA!'
                ]);
            }

            break;

        case 'POST':
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

?>