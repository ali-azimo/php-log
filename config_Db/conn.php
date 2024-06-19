<?php
session_start();

//Conexao com Email
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
//Local e Online

//Local
$modo = 'local';

if($modo == 'local'){
    $servidor = 'localhost';
    $user = 'root';
    $senha = '';
    $banco = 'login';
}
if($modo=="producao"){
    $servidor ="";
    $user = "";
    $senha = "";
    $banco = "";
}
try{
    $pdo = new PDO("mysql:host=$servidor;dbname=$banco", $user,$senha);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //echo "Conectado com sucesso";
}catch(PDOException $erro){
    echo "Falha de conexcao com banco";
}

//Valodacao caso de invazao
function limparPost($dados){
    $dados = trim($dados);
    $dados = stripslashes($dados);
    $dados = htmlspecialchars($dados);
    return $dados;
}

//Validar autorizcao
function auth($tokenSession){
    global $pdo;

    //Verificar se tem autorizacao
$sql = $pdo->prepare("SELECT * FROM usuarios WHERE token=? limit 1");
$sql->execute(array($tokenSession));
$usuario = $sql->fetch(PDO::FETCH_ASSOC);
//caso nao exita levar ao login
if(!$usuario){
    return false;
}else{
    return $usuario;
}
}

