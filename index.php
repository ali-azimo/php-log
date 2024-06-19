<?php
require('config_Db/conn.php');


if(isset($_POST['email']) && isset($_POST['senha']) && !empty($_POST['email']) && !empty($_POST['senha'])){
    //Receber dados do post e limpa
    $email = limparPost($_POST['email']);
    $senha = limparPost($_POST['senha']);
    $senha_cript = sha1($senha);


    //Verificar se usuaro existe
    $sql = $pdo->prepare("SELECT * FROM oline_user WHERE email=? AND senha=? LIMIT 1");
    $sql->execute(array($email,$senha_cript));
    $usuario = $sql->fetch(PDO::FETCH_ASSOC);

    if($usuario){
        //Existe usuario
        //Criar token (sequencia de numero e letra de usuario id)
        if($usuario['status']=="confirmado"){
            $token = sha1(uniqid().date('d-m-Y-H-i-s'));

            //Actualizar o token no banco
            $sql = $pdo->prepare("UPDATE oline_user SET token=? WHERE email=? AND senha=?");
            if($sql->execute(array($token, $email, $senha_cript))){
                //Armazenar na SESSAO
                $_SESSION['TOKEN'] = $token;
                header('location: restrita.php');
            }
        }else{
            $erro_login = "Confirme o seu cadastro no email";
        } 
        
    }else{
        $erro_login = "Usuario ou senha invalida";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"
  />
    <title>Aulas completa de PHP</title>
</head>
<body>
    <section class="form-continer">

        <form method="post">
            <h1>Login</h1>

        <?php if(isset($_GET['result']) && ($_GET['result']=="ok")){ ?>
            <div class="sucesso">
                Cadastrado com sucesso!
            </div>
        <?php }?>
            
    <?php if(isset($login->erro["erro_geral"])) { ?>
        <div class="erro-geral animate__animated animate__rubberBand">
            <?php echo $login->erro["erro_geral"]; ?>
        </div>
    <?php } ?> 

        <?php if(isset($erro_login)) { ?>
            <div style="text-align: center; font-size: 14px;" class="erro-geral animate__animated animate__rubberBand">
                <?php echo $erro_login; ?>
            </div>
        <?php } ?> 

            <div class="input-group">
                <img class="input-icon" src="images/email.png" alt="">
                <input type="email" name="email" placeholder="Digite o seu email" required>
            </div>

            <div class="input-group">
                <img  class="input-icon" src="images/senha.png" alt="">
                <input type="password" name="senha" placeholder="Digite sua senha" required>
             </div>

            <button class="btn-blue" type="submit">Fazer login</button>
            <a  class="ir" href="cadastrar.php">Ainda nao tenho cadastro</a>
        </form>
    </section>

    <script src="js/jquery-3.6.3.min.js"></script>
    <?php if(isset($_GET['result']) && ($_GET['result']=="ok")){ ?>
        <script>
            setTimeout(() =>{
                $('.sucesso').hide()
            }, 3000);
        </script>
    <?php }?>
</body>
</html>