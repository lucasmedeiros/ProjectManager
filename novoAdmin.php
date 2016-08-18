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

    <title>Novo Administrador - Project Manager</title>

    <meta name="generator" content="Bootply" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">

    <link href="calendar/jquery-ui.css" rel="stylesheet">

    <script type="text/javascript">
      function mascaraCPF() {
        // Função JavaScript que serve para introduzir uma máscara ao CPF
          var valorCPF = document.formPagina.cpf.value;
          var ultimoValor = document.formPagina.cpf.value.length - 1;

          if(isNaN(parseInt(valorCPF.charAt(ultimoValor))) == true) {
              valorCPF = valorCPF.replace(valorCPF.charAt(ultimoValor), "");
              document.formPagina.cpf.value = valorCPF;
              return false;
          } else {
              if (valorCPF.length == 3) document.formPagina.cpf.value = document.formPagina.cpf.value + ".";    
              if (valorCPF.length == 7) document.formPagina.cpf.value = document.formPagina.cpf.value + ".";
              if (valorCPF.length == 11) document.formPagina.cpf.value = document.formPagina.cpf.value + "-";
              return true;
          }
      }
    </script>

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

                // Conexão com o banco de dados para exibição dos dados do usuário no topo da página

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
                  <div class="panel-heading"><h4><strong>CADASTRAR NOVO USUÁRIO ADMINISTRADOR</strong></h4></div>
                  <div class="panel-body">
                    <form accept-charset="UTF-8" enctype="multipart/form-data" name="formPagina" class="form-horizontal" role="form" method="post" action="cadastrar.php">
                      <div class="form-group">
                        <label class="control-label col-sm-2" for="login">Nome de Login:</label>
                        <div class="col-sm-10">
                          <input type="text" placeholder="Nome para logar no site" class="form-control" name="login" id="login" required>
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-sm-2" for="completo">Nome completo:</label>
                        <div class="col-sm-10">
                          <input style="margin-top: 8px;" placeholder="Exemplo: José da Silva" type="text" class="form-control" name="completo" id="completo" required>
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-sm-2" for="email">E-mail:</label>
                        <div class="col-sm-10">
                          <input type="email" placeholder="example@example.com" class="form-control" name="email" id="email" required>
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-sm-2" for="senha">Senha:</label>
                        <div class="col-sm-4">
                          <input type="password" placeholder="Mínimo: 6 caracteres, máximo: 14" maxlength="14" class="form-control" name="senha" id="senha" required>
                        </div>
                        <label class="control-label col-sm-2" for="reSenha">Confirmar:</label>
                        <div class="col-sm-4">
                          <input type="password" placeholder="Confirme a senha" class="form-control" name="reSenha" id="reSenha" required>
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-sm-2" for="cpf">CPF:</label>
                        <div class="col-sm-10">
                          <input type="text" placeholder="Apenas números..." class="form-control" onkeydown="mascaraCPF()" maxlength="14" name="cpf" id="cpf" required>
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-sm-2" for="idade">Idade:</label>
                        <div class="col-sm-4">
                          <input type="number" class="form-control" name="idade" id="idade" required>
                        </div>
                        <label class="control-label col-sm-2" for="cidade" >Cidade:</label>
                        <div class="col-sm-4">
                          <input type="text" name="cidade" class="form-control" id="cidade" required>
                        </div>
                      </div>
                      <input type="hidden" name="superuser" value="1">
                      <!-- Este campo "superuser" do tipo hidden confirma o valor de cadastro para usuários administradores, já que seu valor é 1 -->
                      <div class="form-group">
                        <label class="control-label col-sm-2" for="uf">UF:</label>
                        <div class="col-sm-10">
                          <select required class="form-control" name="uf" id="uf" >
                            <option value="">---</option>
                            <?php
                              $query = "select * from getUfs;";

                              // Pegando os dados das UF's do banco de dados e adicionando-os ao input select

                              $ufs = mysqli_query($connection, $query);
                              while(($uf = mysqli_fetch_array($ufs)) != null) {
                                  echo "<option value='$uf[codUf]'>$uf[sigla]</option>";
                              }
                            ?>
                          </select>
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-sm-2" for="arquivo" >Foto de perfil:</label>
                        <div class="col-sm-10">
                          <input type="file" name="arquivo" accept="image/x-png, image/gif, image/jpg" required>
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