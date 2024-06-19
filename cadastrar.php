<?php

use PHPMailer\PHPMailer\PHPMailer;

require('config_Db/conn.php');

//VERIFICAR SE EXISTE UMA POSSTAGEM NOS INPUT
if(isset($_POST['nome']) && isset($_POST['email']) && isset($_POST['senha']) && isset($_POST['repete_senha'])){
    //VERIFICAR SE TODAS AS POSTAGEN FORAM PREENCHIDAS
    if(empty($_POST['nome']) or empty($_POST['email']) or empty($_POST['senha']) or empty($_POST['repete_senha']) or empty($_POST['termos'])){
        echo "Todos campos sao obrigarios";
    }else{
        //Receber do post e limpar
        $nome = limparPost($_POST['nome']);
        $email = limparPost($_POST['email']);
        $senha = limparPost($_POST['senha']);
        //Senha criptogrfada
        $senha_Cript = sha1($senha);
        $repete_senha = limparPost($_POST['repete_senha']);
        $checkbox = limparPost($_POST['termos']);

        //Valodar caracter de nomes para que seja nome valido
        if(!preg_match("/^[a-zA-Z- ']*$/",$nome)){
            $erro_nome = "Apenas letras e epacos em branco";
        }
        //Verificar se o email e valido
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $erro_email = "Email invalido";
        }
        //Validacao de senha + de 6 disgitos
        if(strlen($senha) < 6 ){
            $erro_senha = "A senha deve ter 6 digitos ou mais";
        }
        //verificar se repete senha e igual
        if($senha !== $repete_senha){
            $erro_RepSenha = "Senha diferente";
        }
        //Verificar se checkbox e igual
        if($checkbox !== "ok"){
            $erroCheckBox = "Desativado";
        }
        //Inserir no Banco caso nao haja erros
        if(!isset($erro_geral) && !isset($erro_nome) && !isset($erro_email) && !isset($erro_senha) && !isset($erro_RepSenha) && !isset($erroCheckBox)){

            //Verificar se usuario esta cadastrado
            $sql = $pdo ->prepare("SELECT * FROM oline_user WHERE email=? LIMIT 1");
            $sql->execute(array($email));
            $usuario = $sql->fetch();
            //caso nao exista usuario com email cadastrado - cadastrar
            if(!$usuario){
                $recuperarSenha = "";
                $token = "";
                $confir_code= uniqid();
                $status = "novo";
                $dataCadastro = date('d-m-Y');
                $sql = $pdo->prepare("INSERT INTO oline_user VALUES (null, ?,?,?,?,?,?,?,?)");
                if($sql->execute(array($nome, $email, $senha_Cript, $recuperarSenha, $token,$confir_code, $status, $dataCadastro))){
                    //local
                    if($modo == "local"){
                        //caso estej tudo ok redicionar
                    header('location: index.php?result=ok');
                    }
                    //modo producao
                    if($modo == "producao"){
                        //Enviar email to the user
                        $mail = new PHPMailer(true);
                        try{
                            $mail->setFrom('webdesign.aejl@gmail.com', 'Sistema da AM');
                            $mail->addAddress($email, $nome);

                            //Content(Construcao do emai;)
                            $mail->isHTML(true);
                            $mail->Subject = "Confirme o cadastro";
                            $mail->Body = "<h1>Confirme o seu email abaixo:
                            </h1>
                            <br><br>
                            <a href='linkdositema.com/confirm.php?codeCofirm".$confir_code."'>Confirma email</a>";
                            $mail->send();
                            header('location: obrigado.php');
                        }catch(Exception){
                            echo"Houve problema ao enviar a confirmacao";
                        }
                    }
                }
            }else{
                //Caaso exista apresentar erro
                $erro_geral = "Usuario Cadastrado";
            }
        }

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
    <title>Aulas completa de PHP OBJ</title>
</head>
<body>
    <section class="form-continer">

        <form method="post">

            <h1>Cadastrar</h1>

            <?php if(isset($usuario->erro["erro_geral"])) { ?>
        <div class="erro-geral animate__animated animate__rubberBand">
            <?php echo $usuario->erro["erro_geral"]; ?>
        </div>
    <?php } ?>

    <?php if(isset($erro_geral)) { ?>
        <div class="erro-geral animate__animated animate__rubberBand">
            <?php echo $erro_geral; ?>
        </div>
    <?php } ?> 
           

    <?php if(isset($login->erro["erro_geral"])) { ?>
        <div class="erro-geral animate__animated animate__rubberBand">
            <?php echo $login->erro["erro_geral"]; ?>
        </div>
    <?php } ?>

     <div class="input-group">
        <img class="input-icon" src="images/nome.png" alt="">

        <input
        <?php if(isset($erro_geral) or isset($erro_nome))
        { echo "class = 'erro-input'";} ?> 
        type="text" name="nome" placeholder="Digite o seu nome" required
        
        <?php 
        //Caso o dados esteja corecto permanecer
        if(isset($_POST['nome'])) echo "value = '".$_POST['nome']."'";?>>

        <?php if(isset($erro_nome)){ ?>
            <div class="erro"><?php echo $erro_nome; ?></div>
        <?php } ?>
    </div>
             
    <div class="input-group">
        <img class="input-icon" src="images/email.png" alt="">
        <input <?php if(isset($erro_email) or isset($erro_geral)){ echo "class = 'erro-input'";} ?> type="email" name="email" placeholder="Digite o seu email" required <?php if(isset($_POST['email'])) echo "value = '".$_POST['email']."'";?>>

        <?php if(isset($erro_email)){ ?>
            <div class="erro"><?php echo $erro_email; ?></div>
        <?php } ?>
    </div>

    <div class="input-group">
        <img  class="input-icon" src="images/senha.png" alt="">
        <input <?php if(isset($erro_senha) or isset($erro_geral)){ echo "class = 'erro-input'";} ?> type="password" name="senha" placeholder="Digite a sua senha" required <?php if(isset($_POST['senha'])) echo "value = '".$_POST['senha']."'";?>>

        <?php if(isset($erro_senha)){ ?>
            <div class="erro"><?php echo $erro_senha; ?></div>
        <?php } ?>
    </div>

    <div class="input-group">
        <img  class="input-icon" src="images/senha-desb.png" alt="">
        <input <?php if(isset($erro_RepSenha) or isset($erro_geral)){ echo "class = 'erro-input'";} ?> type="password" name="repete_senha" placeholder="Redigite a senha" required <?php if(isset($_POST['repete_senha'])) echo "value = '".$_POST['repete_senha']."'";?>>
                
        <?php if(isset($erro_RepSenha)){ ?>
            <div class="erro"><?php echo $erro_RepSenha; ?></div>
        <?php } ?>
    </div>
             
        <div <?php if(isset($erroCheckBox) or isset($erro_geral) && $erro_geral== "Todos os campos sao obrigatorios!"){ echo "class = 'erro-input input-group'";} else{ echo "class = 'input-group'";} ?>>
                
        <input type="checkbox" id="termos" name="termos" value="ok" required>
        <label for="termos">Ao se cadastrar voce concorda com a nossa <a href="#" class="link">Politica de Privacidade</a> e os <a href="#" class="link">Termos de uso</a></label>
    </div>

            <button class="btn-blue" type="submit">Fazer login</button>
            <a href="index.php" class="ir">Ja tenho uma conta</a>
        </form>
    </section>
</body>
</html>