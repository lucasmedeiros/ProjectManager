<?php 
  include 'conexao.php';
  // Incluindo o arquivo de conexão ao banco de dados
?>

<!DOCTYPE html>

<!-- Nas páginas de login e cadastro não foi utilizado Bootstrap para o estilo das páginas -->

<html>
  <head>
    <meta charset="UTF-8">
    <title>Login - Project Manager</title>

    <link rel="stylesheet" href="css/style.css">

  </head>

  <body>
    <?php 
      @session_start();

      if (isset($_SESSION['login'])) {
        header("Location:index.php");
      }
    ?>
    <div id="login">
      <h1 id="header">PROJECT MANAGER &copy</h1>
      <form method="post" name="formPagina" action="login.php">

        <input type="text" placeholder="Nome de Login ou e-mail" name="login" required/><br />
        <input type="password" placeholder="Senha" name="senha" required /><br />
        <a class="esquerda" href="cadastro.php">Cadastrar-se</a>
        <input class="direita" type="reset" value="LIMPAR">
        <input type="submit" name="submit" value="LOGIN"></input>

      </form>
    </div>

    <?php

      if (isset($_POST['submit'])) {

        $nome = $_POST['login'];
        $senha = $_POST['senha'];

        $query = "";

        if (strstr($nome, "@")) {
            $query = "select * from Login where email = \"$nome\" and senha = \"$senha\";";
        } else {
            $query = "select * from Login where login = \"$nome\" and senha = \"$senha\";";
        }

        $result = mysqli_query($connection,$query);

        if ($result) {
            $count =  mysqli_num_rows($result);
            if($count == 1) {
                session_start();
                $_SESSION['login'] = $nome;
                $_SESSION['senha'] = $senha;
                header("Location:index.php");
            } else {
                echo "<div style=\"background-color: rgba(255, 255, 255, 0.5);border-radius: 20px; max-width: 300px; margin: 0 auto; margin-top: 10px; color: red;\"><p align=\"center\"><strong>Usuário e/ou senha incorreto(s)!</strong></p></div>";
            }
        } else {
            ?>
                <script type="text/javascript">
                    alert("Ocorreu o seguinte erro: " + "<?php echo mysqli_error($connection) ?>");
                </script>
            <?php
        }
      }

    ?>

  </body>
</html>