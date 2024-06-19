<?php

require('config_Db/conn.php');
if(isset($_GET['codeCofirm']) && !empty($_GET['codeCofirm'])){
    //LIMPAR O GET
    $cod = limparPost($_GET['codeCofirm']);

    //Consultar se usuario tem senha de cinfirmacao
    $sql = $pdo->prepare("SELECT * FROM oline_user WHERE confirm_code=? LIMIT 1");

    $sql->execute(array($cod));
    $usuario = $sql->fetch(PDO::FETCH_ASSOC);
    if($usuario){
        $status = "confirmado";
        //Actualizar status "novo' para confirmado
        $sql = $pdo->prepare("UPDATE oline_user SET status=? WHERE onfirm_code");
        if($sql->execute(array($status,$cod))){
            header('location: index.php?resutado=ok');
        }

    }else{
        echo "<h1>Codigo de confirmacao invaida</h1>";
    }




}


?>