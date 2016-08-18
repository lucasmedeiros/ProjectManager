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

<html lang="en">
  <head>

    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta charset="utf-8"/>

    <title>Novo Participante - Project Manager</title>

    <meta name="generator" content="Bootply" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">

    <link href="calendar/jquery-ui.css" rel="stylesheet">

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

                if (strstr($loginName, "@")) {
                    $query1 = "select foto, login, admin from Login where email = '$loginName';";
                } else {
                    $query1 = "select foto, login, admin from Login where login = '$loginName';";
                }

                $result1 = mysqli_query($connection, $query1);

                if ($result1) {
                  while(($dados = mysqli_fetch_array($result1)) != null) {
                      if ($dados['admin'] != 1) {
                        header('Location:index.php');
                        // Se não for admin, será redirecionado para a página inicial
                      } else {
                        $caminho = "upload/$dados[foto]";
                        $loginName = $dados['login'];
                        echo "<img src=\"$caminho\" style=\"border-radius:50%; width: 35px; height: 25px;\"></img>";
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
    <div class="container">

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
              // Neste caso, aqui está sendo permitida a exclusão

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
          // Pegando o valor da notificação que foi clicada
        ?>
      <div class="row">
        <div class="col-sm-3">
          <h3> MENU </h3>
          <hr/>

          <ul class="nav nav-stacked">
            <li><a href="novo.php"><i class="glyphicon glyphicon-plus"></i> NOVO PROJETO</a></li>
            <li><a href="novoParticipante.php"><i class="glyphicon glyphicon-plus"></i> NOVO PARTICIPANTE</a></li>
            <li><a href="objetivo.php"><i class="glyphicon glyphicon-plus"></i> NOVO OBJETIVO</a></li>
            <li><a href="novoAdmin.php"><i class="glyphicon glyphicon-plus"></i> NOVO USUÁRIO ADMIN</a></li>
          </ul>

          <hr>

        </div>
        <div class="col-sm-9">  

          <div class="row">
            <div class="col-md-12">

              <div class="panel panel-primary">
                  <div class="panel-heading"><h4><strong>CADASTRAR NOVO PARTICIPANTE</strong></h4></div>
                  <div class="panel-body">
                    <?php
                      function submitParticipante() {
                        global $connection;
                        // Pegando a variável global, declarada na inclusão do arquivo conexao.php
                        // Assim, tornando possível a conexão ao banco de dados

                        $login = $_POST['nome'];
                        $func = $_POST['func'];
                        $proj = $_POST['proj'];

                        $getCod = "select pt.codPart 
                                    from Participante pt, Login l, Projeto p
                                    where pt.codLogin = l.codigo
                                    and p.codProj = $proj
                                    and pt.projeto = p.codProj
                                    and l.codigo = $login;";

                        $pegar = mysqli_query($connection, $getCod);
                        $conta = mysqli_num_rows($pegar);

                        if ($conta == 0) {
                          $query = "insert into Participante (codLogin, projeto, coordenador) values ($login, $proj, $func)";
                          $result = mysqli_query($connection,$query);

                          if ($result) {
                            $getCod = "select pt.codPart 
                                        from Participante pt, Login l
                                        where pt.codLogin = l.codigo
                                        and l.codigo = $login;";

                            $codigoPart = 0;

                            $pegar = mysqli_query($connection, $getCod);

                            while ($row = mysqli_fetch_array($pegar)) {
                              $codigoPart = $row['codPart'];
                            }

                            date_default_timezone_set('America/Sao_Paulo');
                            // Passando o fuso horário brasileiro para as datas

                            $dataNot = date("Y/m/d H:i:s");
                            // Pegando a data de cadastro da notificação

                            $segundaSubmissao = "insert into Notificacao (codigoPart, ativo, msg, dataNot) values ($codigoPart, 1, 'Você está cadastrado em um novo projeto: <strong>#$proj!</strong>', '$dataNot');";

                            $inserirNotificacao = mysqli_query($connection, $segundaSubmissao);
                            if ($inserirNotificacao) {
                              header('Location:index.php');
                            }
                          } else {
                              ?>
                                <div class="alert alert-danger" role="alert">
                                  <strong>Erro: <?php echo mysqli_error($connection) ?></strong>
                                </div>
                              <?php
                          }
                        } else {
                          ?>
                            <div class="alert alert-danger" role="alert">
                              <strong>Erro: Usuário já está cadastrado neste projeto</strong>
                            </div>
                          <?php
                        }
                      }

                      if (isset($_POST['submit'])) {
                        submitParticipante();
                      } else {
                        ?>
                          <div class="alert alert-info">
                            <strong>Todos os campos são obrigatórios</strong>.
                          </div>
                        <?php

                      }
                    ?>
                    <form accept-charset="UTF-8" name="formPagina" class="form-horizontal" role="form" method="post" action="novoParticipante.php">
                      <div class="form-group">
                        <label class="control-label col-sm-2" for="nome">Nome:</label>
                        <div class="col-sm-10">
                          <select required class="form-control" name="nome" id="nome">
                            <option value="">---</option>
                            <?php
                              $query = "select * from Login;";

                              $logins = mysqli_query($connection, $query);
                              while(($login = mysqli_fetch_array($logins)) != null) {
                                  echo "<option value='$login[codigo]'>$login[nomeCompleto]</option>";
                              }
                            ?>
                          </select>
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-sm-2" for="func">Função:</label>
                        <div class="col-sm-10">
                          <select required class="form-control" name="func" id="func">
                            <option value="">---</option>
                            <option value="1">Orientador</option>
                            <option value="2">Bolsista</option>
                            <option value="3">Voluntário</option>
                          </select>
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-sm-2" for="proj">Projeto:</label>
                        <div class="col-sm-10">
                          <select required class="form-control" name="proj" id="proj">
                            <option value="">---</option>
                            <?php
                              $query = "select * from getProjetos;";

                              $projs = mysqli_query($connection, $query);
                              while(($proj = mysqli_fetch_array($projs)) != null) {
                                  echo "<option value='$proj[codProj]'>$proj[nome]</option>";
                              }
                            ?>
                          </select>
                        </div>
                      </div>
                      <div class="form-group">        
                        <div class="col-sm-offset-2 col-sm-10">
                          <button type="submit" name="submit" class="btn btn-success">ENVIAR</button>
                          <button type="reset" class="btn btn-danger">LIMPAR</button>
                        </div>
                      </div>
                    </form>
                  </div>
              </div>
            </div>                   

          </div>

        </div>
      </div>

    </div>


    <footer class="text-center" id="footer"><strong>Project Manager &copy </strong></footer>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js"></script>

    <script src="js/bootstrap.min.js"></script>

    <script src="calendar/external/jquery/jquery.js"></script>
    <script src="calendar/jquery-ui.js"></script>

    <script>
      $('#dataInicio').datepicker({ dateFormat: 'dd-mm-yy' }).val();

      $('#dataFim').datepicker({ dateFormat: 'dd-mm-yy'}).val();
    </script>
  </body>
</html>