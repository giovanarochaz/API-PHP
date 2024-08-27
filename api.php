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
                        lista_especifica_aluno($path_parts[3]);
                        break;
                    }
                } else{
                    lista_completa_alunos();
                    break;
                }
                

            //Logica tabela cursos
            } else  if($path_parte03 == 'cursos'){

                if(isset($path_parts[3])){
                    if(!is_numeric($path_parts[3])){
                        mensagem_simbolos();
                        break;
                    } else{
                        lista_especifica_curso($path_parts[3]);
                        break;
                    }
                } else{
                    lista_completa_cursos();
                    break;
                }

            } else{
                tabela_incorreta();
            }

            break;

        case 'POST':
            
            //Logica tabela alunos
            if($path_parte03 == 'alunos'){
                add_aluno();
                break;

            //Logica tabela cursos
            } else  if($path_parte03 == 'cursos'){
                add_curso();
                break;

            } else{
                tabela_incorreta();
            }

            break;

        case 'PUT':
            //Logica tabela alunos
            if($path_parte03 == 'alunos'){
                atualizar_aluno();
                break;

            //Logica tabela cursos
            } else  if($path_parte03 == 'cursos'){
                atualizar_curso();
                break;
                
            } else{
                tabela_incorreta();
            }
            break;

        case 'DELETE':
            //Logica tabela alunos
            if($path_parte03 == 'alunos'){
                delete_aluno();
                break;

            //Logica tabela cursos
            } else  if($path_parte03 == 'cursos'){
                delete_curso();
                break;
                
            } else{
                tabela_incorreta();
            }
            break;
        
        default:
            echo json_encode([
                'mensagem' => 'MÉTODO INCORRETO!'
            ]);
            break;
    }

    //GET 
    
    //Completo
    function lista_completa_alunos() {
        global $conexao;

        $resultado = $conexao->query("SELECT * FROM alunos");
        $dados = $resultado->fetch_all(MYSQLI_ASSOC);
        
        echo json_encode([
            'mensagem' => "ALUNOS COMPLETO!",
            'dados' => $dados
        ]);
    }

    function lista_completa_cursos() {
        global $conexao;

        $resultado = $conexao->query("SELECT * FROM cursos");
        $dados = $resultado->fetch_all(MYSQLI_ASSOC);
        
        echo json_encode([
            'mensagem' => "CURSO COMPLETO!",
            'dados' => $dados
        ]);
    }

    //Expecifico
    function lista_especifica_aluno($id) {
        global $conexao;

        $query = "SELECT * FROM alunos  WHERE id = ?";
        $stmt = $conexao->prepare($query);
        $stmt->bind_param("i", $id); 
        $stmt->execute();
        $dados = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        echo json_encode([
            'mensagem' => "aluno Específico!",
            'dados' => $dados
        ]);
    }

    function lista_especifica_curso( $id) {
        global $conexao;

        $query = "SELECT * FROM cursos WHERE id_curso = ?";
        $stmt = $conexao->prepare($query);
        $stmt->bind_param("i", $id); 
        $stmt->execute();
        $dados = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        echo json_encode([
            'mensagem' => "curso Específico!",
            'dados' => $dados
        ]);
    }

    //POST
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

    function add_aluno(){
        global $conexao;
        $nome = $_GET['nome'];
        $email = $_GET['email'];
        $id_curso = $_GET['id_curso'];
        $sql = "INSERT INTO alunos (nome, email, fk_cursos_id_curso) VALUE ('$nome','$email','$id_curso')";

        if($conexao->query($sql) === TRUE){
            mensagem_sucess();
        } else{
            mensagem_error();
        }
    }

    //PUT
    function atualizar_curso(){
        global $conexao;
        $id_curso = $_GET['id_curso'];
        $nome_curso = $_GET['nome_curso'];
        $sql = "UPDATE cursos set nome_curso = '$nome_curso' where id_curso = '$id_curso'";

        if($conexao->query($sql) === TRUE){
            mensagem_sucess();
        } else{
            mensagem_error();
        }
    }

    function atualizar_aluno(){
        global $conexao;
        $id = $_GET['id'];
        $nome = $_GET['nome'];
        $email = $_GET['email'];
        $id_curso = $_GET['id_curso'];
        $sql = "UPDATE alunos set nome = '$nome', email = '$email', fk_cursos_id_curso = '$id_curso' where id = '$id'";

        if($conexao->query($sql) === TRUE){
            mensagem_sucess();
        } else{
            mensagem_error();
        }
    }

    //DELETE

    function delete_curso() {
        global $conexao;
        $id = $_GET['id_curso'];
    
        // Iniciar uma transação
        $conexao->begin_transaction();
    
        try {
            $sql = "DELETE FROM alunos WHERE fk_cursos_id_curso = ?";
            $stmt = $conexao->prepare($sql);
            if ($stmt === false) {
                throw new Exception("Erro ao preparar a declaração de deletar alunos");
            }
    
            $stmt->bind_param("i", $id);
    
            if (!$stmt->execute()) {
                throw new Exception("Erro ao deletar alunos: " . $stmt->error);
            }
    
            $stmt->close();
    
            $sql = "DELETE FROM cursos WHERE id_curso = ?";
            $stmt = $conexao->prepare($sql);
            if ($stmt === false) {
                throw new Exception("Erro ao preparar a declaração de deletar curso");
            }
    
            $stmt->bind_param("i", $id);
    
            if (!$stmt->execute()) {
                throw new Exception("Erro ao deletar curso: " . $stmt->error);
            }
    
            $stmt->close();
    
            // Commit da transação
            $conexao->commit();
    
            mensagem_sucess();
        } catch (Exception $e) {
            // Rollback da transação em caso de erro
            $conexao->rollback();
            mensagem_error($e->getMessage());
        }
    }

    function delete_aluno() {
        global $conexao;
        $id = $_GET['id'];
    
        // Iniciar uma transação
        $conexao->begin_transaction();
    
        try {
            $sql = "DELETE FROM alunos WHERE id = ?";
            $stmt = $conexao->prepare($sql);
            if ($stmt === false) {
                throw new Exception("Erro ao preparar a declaração de deletar alunos");
            }
    
            $stmt->bind_param("i", $id);
    
            if (!$stmt->execute()) {
                throw new Exception("Erro ao deletar aluno: " . $stmt->error);
            }
    
            $stmt->close();
    
            // Commit da transação
            $conexao->commit();
    
            mensagem_sucess();
        } catch (Exception $e) {
            // Rollback da transação em caso de erro
            $conexao->rollback();
            mensagem_error($e->getMessage());
        }
    }
        
    //MENSAGENS
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

    function tabela_incorreta() {
        echo json_encode([
            'mensagem' => 'SUCESSO!'
        ]);
    }

?>