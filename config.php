<?php 

  include 'restrito.php';
  // Incluindo o arquivo que garante a restrição da página apenas a usuários logados
  include 'conexao.php';
  // Incluindo o arquivo de conexão ao banco de dados
  include 'permicao.php';
  // Incluindo o arquivo que verifica se um usuário pode marcar uma notificação como visualizada || excluí-la

?>

<?php 

  

?>


<!DOCTYPE html>

<!-- Uso de Bootstrap para o estilo das páginas -->

<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<meta charset="utf-8">
		<title>Configurações - Project Manager</title>
		<meta name="generator" content="Bootply" />
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		<link href="css/bootstrap.min.css" rel="stylesheet">
		<link href="css/styles.css" rel="stylesheet">
	</head>
	<body>
    <div id="top-nav" class="navbar navbar-inverse navbar-static-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
              <span class="icon-toggle"></span>
          </button>
          <a class="navbar-brand" href="index.php">INÍCIO</a>
        </div>
        <ul class="nav navbar-nav pull-right">
          <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
              <?php 

                // A partir daqui, tem-se a verificação do tipo de usuário (se é ou não administrador)

                $loginName = $_SESSION['login'];
                // Pega a Session

                $caminho = "";
                // Criando a variável aqui, pois será usada também para mostrar a foto em tamanho maior no corpo da página

                $isAdmin = "";
                $boolAdmin = false; // Se for admin, essa condição será 'true', senão, será 'false'
                // Esta variável será utilizada mais a frente, para aparecer menu lateral ou não

                if (strstr($loginName, "@")) {
                  // Verifica se o usuário logou com o email ou com o nome de usuário
                    $isAdmin = "select admin, login from Login where email = '$loginName';";
                } else {
                    $isAdmin = "select admin, login from Login where login = '$loginName';";
                }
                $resultadoIsAdmin = mysqli_query($connection, $isAdmin);

                if ($resultadoIsAdmin) {
                  $intAdmin = 0;
                  // Declara a variavel intAdmin para pegar o valor retornado

                  $administradores = mysqli_query($connection, $isAdmin);
                  while(($administrador = mysqli_fetch_array($administradores)) != null) {
                    $intAdmin = $administrador['admin'];
                    // Se for 1, o usuário é administrador
                    // Se for 0, o usuário é comum

                    $loginName = $administrador['login'];
                  }
                  
                  if ($intAdmin == 1) {
                    $boolAdmin = true;
                    // Essa variável servirá para mostrar o menu lateral, caso o usuário for administrador
                  } else {
                    $boolAdmin = false;
                  }

                  if (strstr($loginName, "@")) {
                    // Verifica se o usuário logou com o email ou com o nome de usuário e declara a string que vai ser a requisição ao banco de dados
                    $query1 = "select foto, login from Login where email = '$loginName';";
                  } else {
                    $query1 = "select foto, login from Login where login = '$loginName';";
                  }

                  $result1 = mysqli_query($connection, $query1);

                  if ($result1) {
                    $nomes = mysqli_query($connection, $query1);
                    while(($foto = mysqli_fetch_array($nomes)) != null) {
                        $caminho = "upload/$foto[foto]";
                        $loginName = $foto['login'];
                        echo "<img src=\"$caminho\" style=\"border-radius:50%; width: 35px; height: 25px;\"></img>";
                        // Exibição do pequeno ícone de foto no foto da página
                    }
                  }
                }
              ?>
              Olá, <?php echo $loginName; ?>
            <span class="caret"></span>

            </a>
            <ul class="dropdown-menu">
              <li><a href="config.php"><img src="img/settings.png"/>  Configurações</a></li>
              <li><a href="indexUsuario.php"><img src="img/projects.png"/>  Meus Projetos</a></li>    
              <li><a href="?logout=sim"><img src="img/logoff.png"/>  Logout</a></li>          
            </ul>
          </li>
        </ul>
        <ul class="nav navbar-nav">
          <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
              <?php

                // Pegar todas as notificações para este usuário que estão marcadas como não lidas (ou ativas)
                $nots = "select n.*, p.codPart 
                          from Notificacao n, Login l, Participante p
                          where n.ativo = 1
                          and n.codigoPart = p.codPart
                          and p.codLogin = l.codigo
                          and l.login = \"$loginName\";";
                $codigoParticipante = 0;
                // Definindo para poder pegar este código da requisição mais abaixo
                $notif = mysqli_query($connection, $nots);
                // Esta variável será usada mais abaixo
                $conta = mysqli_num_rows($notif);
                // Contando o número de notificações encontradas
                
                if ($conta == 0) {
                  echo "<span class=\"badge\">$conta</span>";
                  // Se for 0, exibirá o aviso com background cinza (padrão)
                } else {
                  echo "<span class=\"badge\" style=\"background-color: red;\">$conta</span>";
                  // Se for != 0, exibirá o aviso com background vermelho
                }
              ?>
            <span class="caret"></span>

            </a>
            <ul class="dropdown-menu">
              <li class="text-center"><a href="notificacoes.php" style="background-color: #ccc; color: #483D8B;">Visualizar todas as notificações</a></li>
              <?php
                $contador = 0;
                while ($row = mysqli_fetch_array($notif)) {
                  // A variável $notif foi criada na verificação de existência de notificações ativas
                  // Foi usada para pegar os dados das notificações
                  $contador++;
                  // Variável que será usada para contar as notificações não lidas para o usuário
                  ?>
                    <li align="center">
                      <?php
                        echo "<a href=\"?valueNotification=$row[codNot]\"><span class=\"badge\" style=\"background-color: red; margin-right: 10px;\">new</span>$row[msg] <span style=\"color: #ccc;\">[".date("d/m - H:i", strtotime("$row[dataNot]"))."]</span></a>";
                          $codigoParticipante = $row['codPart'];
                      ?>
                    </li>
                  <?php
                          
                }
              ?>        
            </ul>
          </li>
        </ul>
      </div>
    </div>
<div class="container" id="tudo">

    <?php 

      if (isset($_GET["logout"])) {
        session_destroy();
        header("Location:login.php");
      }
      // Pegando o momento em que o usuário vai fazer logoff, ao clicar em "LOGOUT"

      function marcarLida($codNotif) {
        // Função para marcar as notificações como lidas
        global $connection, $loginName;
        // Pegando o código do participante a partir da variável já declarada acima

        if (permitir($loginName, $codNotif)) {
          // Usando a função "permitir", do arquivo importando "permicao.php para a validação"
          // Neste caso, aqui está sendo permitida a marcação como lida

          $req = "update Notificacao set ativo = 0 where codNot = $codNotif;";
          // Faz o update da notificação, para que ela seja marcada como lida, ou seja, "ativo" terá valor 0
          $res = mysqli_query($connection, $req);
          if ($res) {
            header('Location:indexUsuario.php');
            // Redireciona para a página dos projetos
          } else {
            ?>
              <div class="alert alert-danger">
                <small><strong>Um erro ocorreu...</strong></small>
              </div>
            <?php
          }
        } else {
          ?>
            <div class="alert alert-danger">
              <small><strong>Você não tem permissão para acessar esta notificação...</strong></small>
            </div>
          <?php
        }
      }

      if (isset($_GET["valueNotification"])) {
        $not = $_GET["valueNotification"];
        marcarLida($not);
      }


      // Ajustando os tamanhos de tela para cada tipo de usuário.
      // Explicação: o usuário adminstrador tem o menu lateral para realizar cadastros, o comum, não.
      if ($boolAdmin) {
      ?>
        <div class="col-sm-3">
      
          <h3> MENU </h3>
          <hr>
          
          <ul class="nav nav-stacked">
            <li><a  href="novo.php"><i class="glyphicon glyphicon-plus"></i> NOVO PROJETO</a></li>
            <li><a href="novoParticipante.php"><i class="glyphicon glyphicon-plus"></i> NOVO PARTICIPANTE</a></li>
            <li><a href="objetivo.php"><i class="glyphicon glyphicon-plus"></i> NOVO OBJETIVO</a></li>
            <li><a href="novoAdmin.php"><i class="glyphicon glyphicon-plus"></i> NOVO USUÁRIO ADMIN</a></li>
          </ul>
          
          <!-- Exibição do menu lateral para usuários administradores -->
          <hr>
          
      </div>
        <div class="row">
        <div class="col-sm-8">

        <?php
      } else {
        ?>
          <div class="row">
          <div class="col-sm-12">
        <?php
      }
    ?>
      
	   <div class="row">
          <?php
          if ($boolAdmin) {
            ?>
              <div class="col-md-12">
            <?php
          } else {
            ?>
            <div style="margin: 0 auto; max-width: 740px;">
            <?php
          }

          ?>
              <div style="margin: 0px auto;" class="panel panel-primary">
                  <div class="panel-heading"><h4><strong>CONFIGURAÇÕES</strong></h4></div>

                  <div class="panel-body">
                      <div class="col-md-12">
                          <?php
                            // Função que será ativada quando o usuário submeter o formulário de alteração de senha
                            function alterarSenha() {
                              global $connection;
                              // Pegando a variável global, declarada na inclusão do arquivo conexao.php
                              // Assim, tornando possível a conexão ao banco de dados
                              $senhaNova = $_POST['novaSenha'];
                              $confirmeNovaSenha = $_POST['reNovaSenha'];
                              $loginName = $_SESSION['login'];

                              if (strcmp($senhaNova, $confirmeNovaSenha) != 0) {
                                ?>
                                  <div class="alert alert-danger">
                                    <small><strong>Senhas não conferem...</strong></small>
                                  </div>
                                <?php
                              } else {
                                if (strlen($senhaNova) < 6) {
                                  // Validação da senha
                                  ?>
                                    <div class="alert alert-danger">
                                      <small><strong>Senha fraca (deve ter entre 6 e 14 caracteres)</strong></small>
                                    </div>
                                  <?php
                                } else {
                                  $alterarSenha = "";

                                  if (strstr($loginName, "@")) {
                                    // Verifica se o usuário logou com o email ou com o nome de usuário
                                    $alterarSenha = "update Login set senha = \"$senhaNova\" where email = \"$loginName\";";
                                  } else {
                                    $alterarSenha = "update Login set senha = \"$senhaNova\" where login = \"$loginName\";";
                                  }

                                  $resultado = mysqli_query($connection,$alterarSenha);

                                  if ($resultado) { // Verifica se o banco de dados recebeu a requisição
                                      ?>
                                        <div class="alert alert-success">
                                          <strong>Senha alterada com sucesso! <a href="index.php">Home Page</a></strong>
                                        </div>
                                      <?php
                                  } else {
                                      ?>
                                        <div class="alert alert-danger">
                                          <small><strong>Ocorreu o seguinte erro: <?php echo mysqli_error($connection); ?></strong></small>
                                        </div>
                                      <?php
                                  }
                                }
                              }
                            }

                            function alterImg() {
                              global $connection;
                              // Pegando a variável global, declarada na inclusão do arquivo conexao.php
                              // Assim, tornando possível a conexão ao banco de dados
                              if (isset($_FILES['arquivo2'])) {
                                // Pegando o arquivo da foto de perfil cadastrada pelo usuário
                                $nome_arquivo = $_FILES['arquivo2']['name'];
                                $loginName = $_SESSION['login'];
                                
                                $extensao = strtolower(substr($nome_arquivo, -5));
                              // Pegando a extensão da imagem que foi enviada, por exemplo ".jpg"
                              // Pegando as 5 últimas letras e nas as 4 porque podem existir arquivos com extensão ".jpeg"
                              // E se algum arquivo for "foto.jpg", por exemplo, o "o" que vier antes não influenciará no acesso à imagem,
                              // mas apenas adicionará um caractere a mais no nome.

                                $novo_nome = md5(time()).$extensao; // md5() para criptografia do nome da imagem

                                $diretorio = "upload/";

                                $alterar = "";

                                if (strstr($loginName, "@")) {
                                  // Verifica se o usuário logou com o email ou com o nome de usuário
                                  $alterar = "update Login set foto = \"$novo_nome\" where email = \"$loginName\";";
                                } else {
                                  $alterar = "update Login set foto = \"$novo_nome\" where login = \"$loginName\";";
                                }

                                $resultado = mysqli_query($connection,$alterar);

                                if ($resultado) { // Verifica se o banco de dados recebeu a requisição
                                    move_uploaded_file($_FILES['arquivo2']['tmp_name'], $diretorio.$novo_nome);
                                    header("Refresh:0; url=config.php");
                                    // Antes de fazer isso, verificar as permissões da pasta 'upload'
                                } else {
                                    
                                    ?>
                                        <script type="text/javascript">
                                            alert("Ocorreu o seguinte erro: " + "<?php echo mysqli_error($connection) ?>");
                                        </script>
                                    <?php
                                }
                              }
                            }

                            if (isset($_POST['submeterImg'])) {
                              alterImg();
                            }
                            // A função alterarImg() será chamada quando existir o botão de 'submeterImg'

                            if (isset($_POST['submeterSenha'])) {
                              alterarSenha();
                            }
                            // A função alterarSenha() será chamada quando existir o botão de 'submeterSenha'

                          ?>

                          <div class="pull-left" align="center">
                            <h3 style="color: #999999; font-weight: bolder;">ÍCONE DE PERFIL:</h3>
                            <?php
                              echo "<img src=\"$caminho\" style=\"border-radius:18px 0px 18px 0px; max-width: 250px;-webkit-box-shadow: 4px 6px 5px 0px rgba(0,0,0,0.75); -moz-box-shadow: 4px 6px 5px 0px rgba(0,0,0,0.75); box-shadow: 4px 6px 5px 0px rgba(0,0,0,0.5);\"></img>";
                                // Exibindo a imagem de perfil do usuário em tamanho maior, para melhor visualização
                            ?>
                          </div>
                          <br><br>
                          <!-- Formulário de alterar imagem -->
                          <form class="pull-right" enctype="multipart/form-data" name="alterarImagem" action="config.php" method="POST">
                            <small>Alterar ícone de perfil: </small><input type="file" name="arquivo2" required>

                            <br><input type="submit" class="btn btn-success" name="submeterImg" value="SUBMETER">
                          </form> 

                          <div class="col-md-12">
                            <!-- Formulário de alterar senha -->
                            <form name="alterarImagem" action="config.php" method="POST">
                              <br><br>

                              <small><strong>Alterar senha:<br>
                              <div class="alert alert-warning">
                                <strong><small>(Sua senha deve conter mais que 6 caracteres, e menos que 14)</small></strong>
                              </div>
                              <input class="form-control" maxlength="14" type="password" placeholder="Digite a nova senha..." name="novaSenha" required><br>
                              <input class="form-control" maxlength="14" type="password" placeholder="Confirme a nova senha..." name="reNovaSenha" required>

                              <br><input type="submit" class="btn btn-success" name="submeterSenha" value="SUBMETER">
                            </form>
                          </div>
                      </div>
                  </div>
              </div>
            </div>                    
              
      </div>
     
    </div>
  </div>
    
</div>

<!-- "Style aplicado para manter o rodapé no fim da página" -->
<footer class="text-center" id="footer"><strong>Project Manager &copy </strong></footer>
		<script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js"></script>
		<script src="js/bootstrap.min.js"></script>
	</body>
</html>