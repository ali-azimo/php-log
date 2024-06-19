<?php
require('config_Db/conn.php');

//Verificar Auteticacao
$user = auth($_SESSION['TOKEN']);
if($user){
    echo "<h1>Seja bem vindo   ".$user['nome']."</h1>";
    echo "<a href='logout.php'>Sair</a>";
}else{
    //Rediionar
    header('location: index.php');
}

/*
//Verificar se tem autorizacao
$sql = $pdo->prepare("SELECT * FROM usuarios WHERE token=? limit 1");
$sql->execute(array($_SESSION['TOKEN']));
$usuario = $sql->fetch(PDO::FETCH_ASSOC);
//caso nao exita levar ao login
if(!$usuario){
    header('location: index.php');
}else{
    echo "<h1>Seja bem vindo   ".$usuario['nome']."</h1>";
    echo "<a href='logout.php'>Sair</a>";
}
    
*/