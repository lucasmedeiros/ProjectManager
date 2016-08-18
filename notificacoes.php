<?php 

  include 'restrito.php';
  // Incluindo o arquivo que garante a restrição da página apenas a usuários logados
  include 'conexao.php';
  // Incluindo o arquivo de conexão ao banco de dados
  include 'permicao.php';
  // Incluindo o arquivo que verifica se um usuário pode marcar uma notificação como visualizada || excluí-la

?>

<!DOCTYPE html>

<!-- Uso de Bootstrap para o estilo das páginas -->

<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<meta charset="utf-8">
		<title>Notificações - Project Manager</title>
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

                $loginName = $_SESSION['login'];
                $codigo = 0;
                // Esta variável vai pegar o código do usuário, registrado no banco de dados
                // Esta ação servirá mais pra baixo, na listagem dos objetivos exclusivos deste usuário
                // Se quiser ver, descer logo para a parte dos objetivos

                if (strstr($loginName, "@")) {
                    $query1 = "select foto, login, codigo from Login where email = '$loginName';";
                } else {
                    $query1 = "select foto, login, codigo from Login where login = '$loginName';";
                }

                $result1 = mysqli_query($connection, $query1);

                if ($result1) {
                  $nomes = mysqli_query($connection, $query1);
                  while(($foto = mysqli_fetch_array($nomes)) != null) {
                      $caminho = "upload/$foto[foto]";
                      $loginName = $foto['login'];
                      $codigo = $foto['codigo'];
                      echo "<img src=\"$caminho\" style=\"border-radius:50%; width: 35px; height: 25px;\"></img>";
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
              <!-- Passando o valor  -->             
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
        // Pegando o login do participante a partir da variável já declarada acima, além da conexão importada do arquivo conexao.php

        if (permitir($loginName, $codNotif)) {
          // Usando a função "permitir", do arquivo importando "permicao.php para a validação"

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

      function removerNot($codNotif) {
        // Função para remover notificações
        global $connection, $loginName;
        // Pegando o código do participante a partir da variável já declarada acima

        if (permitir($loginName, $codNotif)) {
          $req = "delete from Notificacao where codNot = $codNotif";
          $res = mysqli_query($connection, $req);
          if ($res) {
            header('Location:notificacoes.php');
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

      if (isset($_GET["removeNotification"])) {
        $not = $_GET["removeNotification"];
        removerNot($not);
      }
      // Pegando o valor da notificação que foi clicada
    ?>
  
  <div class="row">
    <div class="col-sm-12">
      
	   <div class="row">
         	<div class="col-md-12">

              <div style="width: 550px;" class="panel panel-danger pull-left">
                <div class="panel-heading"><h4><strong>NOTIFICAÇÕES NÃO LIDAS</strong></h4></div>
                <!-- Exibindo as notificações não lidas ainda -->

                <div class="panel-body">
                  <?php
                    $notif = mysqli_query($connection, $nots);
                    // A variável "$nots" já está criada, pois foi usada para pegar as notif. não lidas anteriormente

                    $conta = mysqli_num_rows($notif); // Recriando para contar o número de ocorrências
                    if ($conta == 0) {
                      echo "<p style=\"color: gray;\">Não há notificações não lidas!</p>";
                    } else {
                      while ($row = mysqli_fetch_array($notif)) {
                        // A variável $notif foi criada na verificação de existência de notificações ativas
                        // Foi usada para pegar os dados das notificações
                        echo "<a href=\"?valueNotification=$row[codNot]\">$row[msg]</a><br/><span style=\"color: #ccc;\">[".date("d/m - H:i", strtotime("$row[dataNot]"))."]</span><br/><br/>";
                      }
                    }

                  ?>
                </div>
              </div>
              <div style="width: 550px;" class="panel panel-success pull-right">
                <div class="panel-heading"><h4><strong>TODAS AS NOTIFICAÇÕES</strong></h4></div>
                <!-- Exibindo as notificações não lidas ainda -->

                <div class="panel-body">
                  <?php
                    $notsLidas = "select n.* 
                                  from Notificacao n, Login l, Participante p
                                  where n.codigoPart = p.codPart
                                  and p.codLogin = l.codigo
                                  and l.login = \"$loginName\"
                                  group by dataNot;";

                    $notif = mysqli_query($connection, $notsLidas);
                    // Requisição SQL
                    $conta = mysqli_num_rows($notif); // Recriando para contar o número de ocorrências
                    if ($conta == 0) {
                      echo "<p style=\"color: gray;\">Não há notificações cadastradas para você!</p>";
                    } else {
                      while ($row = mysqli_fetch_array($notif)) {
                        // A variável $notif foi recriada para a verificação de existência de notificações não ativas, neste caso
                        // Foi usada para pegar os dados das notificações
                        echo "<a href=\"?valueNotification=$row[codNot]\">$row[msg]</a>  <a class=\"pull-right\" href=\"?removeNotification=$row[codNot]\"><img src=\"img/apagar.png\" alt=\"remove\"/></a><br/><span style=\"color: #ccc;\">[".date("d/m - H:i", strtotime("$row[dataNot]"))."]</span><br/><br/>";
                      }
                    }

                  ?>
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