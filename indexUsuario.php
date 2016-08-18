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
		<title>Home - Project Manager</title>
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
    <div class="col-sm-12">
      	
       <h3>MEUS PROJETOS</h3>  
            
       <hr style="border: 1px solid #ccc;"/>
      
	   <div class="row">

         	<div class="col-md-12">
              <div class="alert alert-warning">
                <strong><small>*Seu nome, se estiver participando de um projeto, está grifado de amarelo</small></strong>
              </div>
              <?php
                $codigoLogin = 0;

                $query = "select p.* from Projeto p, Login l, Participante pt
                            where l.login = '$loginName'
                            and l.codigo = pt.codLogin
                            and p.codProj = pt.projeto;";
                $result = mysqli_query($connection, $query);
                $row = mysqli_fetch_array($result, MYSQLI_ASSOC);

                if (!$row) {
                  ?>
                  <div class="alert alert-danger">
                    <small><strong>Sem projetos cadastrados para você...</strong></small>
                  </div>
                  <?php
                } else {

                  $projs = mysqli_query($connection, $query);
                  while(($projeto = mysqli_fetch_array($projs)) != null) {

                      $timeInicio = strtotime($projeto['dtInicio']);
                      $newformatinicio = date('d/m/Y',$timeInicio);

                      $timeFim = strtotime($projeto['dtFim']);
                      $newformatfim = date('d/m/Y',$timeFim);

                      ?>
                      <div class="panel panel-primary">
                        <div class="panel-heading">
                          <p class="pull-right" style="font-size: 10px;">Projeto de <?php

                          $codigoProjeto = 0;

                          $query2 = "select t.descr, p.codProj from Projeto p, TipoProjeto t
                                      where t.codTipo = $projeto[tipo]
                                      and p.codProj = $projeto[codProj]";
                          $result2 = mysqli_query($connection, $query2);

                          while (($tipos = mysqli_fetch_array($result2)) != null) {
                            $tipo = $tipos['descr'];
                            $codigoProjeto = $tipos['codProj'];
                          }
                          echo strtolower($tipo);
                          ?></p>
                          <h4><strong><?php echo "#$codigoProjeto - " . $projeto['nome']; ?></strong></h4>
                        </div>
                        <div class="alert alert-info" style="margin: 10px;">
                          <small><strong>Guarde o código deste projeto: #<?php echo "$codigoProjeto"; ?></strong></small>
                        </div>
                        <div class="panel-body">
                          <table class="table">
                            <thead>
                              <tr>
                                <th>Descrição</th>
                                <th>Início</th>
                                <th>Término</th>
                                <th></th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr>
                                <td><?php echo $projeto['descr']; ?></td>
                                <td><?php echo $newformatinicio;?></td>
                                <td><?php echo $newformatfim; ?></td>
                                <td><?php
                                  $newformatfim = date('Y/m/d',$timeFim);
                                  $dateAtual = date('Y/m/d');
                                  if ($dateAtual >= $newformatfim) {
                                    ?>
                                        <div class="alert alert-success" style="text-align: center; width: 70%;">
                                          <strong>FINALIZADO</strong>
                                        </div>
                                    <?php
                                  } else {
                                    ?>
                                      <div class="alert alert-warning" style="text-align: center; width: 70%;">
                                        <strong>EM ANDAMENTO</strong>
                                      </div>
                                    <?php
                                  }
                                ?></td>
                              </tr>
                            </tbody>
                          </table>
                          <hr style="border: 1px solid #ccc;"/>
                          <h4 align="center"><strong>Participantes:</strong></h4>
                          <?php

                            $query3 = "select pt.coordenador, l.codigo, l.login,l.foto, l.nomeCompleto, l.email
                                        from Participante pt, Projeto p, Login l
                                        where pt.projeto = p.codProj
                                        and p.codProj = $projeto[codProj]
                                        and pt.codLogin = l.codigo;";
                            $result3 = mysqli_query($connection, $query3);
                            $row3 = mysqli_fetch_array($result3, MYSQLI_ASSOC);

                            if (!$row3) {
                              echo "<h4>Sem participantes cadastrados...</h4>";
                            } else {
                                ?>
                                  <table class="table">
                                    <thead>
                                      <tr>
                                        <th></th>
                                        <th>Nome</th>
                                        <th>Email</th>
                                        <th></th>
                                      </tr>
                                    </thead>
                                    <tbody>
                                      <?php
                                      $parts = mysqli_query($connection, $query3);
                                      while(($part = mysqli_fetch_array($parts)) != null) {
                                        $codigoLogin = $part['codigo'];
                                      ?>
                                      <tr>
                                        <td>
                                        <?php
                                          $path = "upload/".$part['foto'];
                                          echo "<img src=\"$path\" style=\"border-radius: 50px; width: 40px; height: auto;\"></img>";
                                        ?>
                                        </td>
                                      
                                        <td><?php 
                                          if ($loginName == $part['login']) {
                                            echo "<b style=\"background-color: yellow;\">$part[nomeCompleto]</b>";
                                          } else{
                                            echo $part['nomeCompleto'];
                                          } ?></td>
                                        <td><?php 

                                          echo $part['email']
                                        ?></td>
                                        <td><?php

                                          if ($part['coordenador'] == 1) {
                                            ?>
                                              <div class="alert alert-success" style="width: 50%; text-align: center;">
                                                <strong>ORIENTADOR</strong>
                                              </div>
                                            <?php
                                          } else {
                                            ?>
                                              <div class="alert alert-warning" style="width: 50%; text-align: center;">
                                                <strong>PARTICIPANTE</strong>
                                              </div>
                                            <?php
                                          }
                                        ?></td>
                                      </tr>
                                      <?php
                                      }?>
                                    </tbody>
                                  </table>
                                <?php
                            }
                          ?>
                          <hr style="border: 1px solid #ccc;"/>
                          <h4 align="center"><strong>Meus objetivos:</strong></h4>
                          <?php

                            $query4 = "select o.descricao, o.horaInicio, o.codObjetivo, l.nomeCompleto, o.tempoEntrega
                                        from Participante pt, Projeto p, Login l, Objetivo o
                                        where o.codProjeto = p.codProj
                                        and p.codProj = $projeto[codProj]
                                        and pt.codLogin = l.codigo
                                        and l.codigo = $codigo
                                        and o.codParticipante = pt.codPart;";

                            // Como dito lá em cima, a variável $codigo sendo usada aqui na parte dos objetivos
                            $result4 = mysqli_query($connection, $query4);
                            $row4 = mysqli_fetch_array($result4, MYSQLI_ASSOC);
                            // Verifica a existência de ocorrências desta última requisição
                            if (!$row4) {
                              ?>
                                <div class="alert alert-danger">
                                  <small><strong>Ainda não há objetivos cadastrados para você neste projeto</strong></small>
                                </div>
                              <?php
                            } else {
                              // Mostrando os objetivos do projeto, quando houver
                                ?>
                                  <table class="table">
                                    <thead>
                                      <tr>
                                        <th>#</th>
                                        <th>Descrição</th>
                                        <th>Responsável</th>
                                        <th>Data/Hora de Início</th>
                                        <th>Tempo para entrega</th>
                                      </tr>
                                    </thead>
                                    <tbody>
                                      <?php
                                      $objs = mysqli_query($connection, $query4);
                                      while(($obj = mysqli_fetch_array($objs)) != null) {
                                      ?>
                                      <tr>
                                        
                                        <td>
                                        <?php
                                        echo $obj['codObjetivo']; ?>
                                        </td>
                                        <td><?php
                                          echo $obj['descricao'];?></td>
                                        <td><?php 
                                          echo $obj['nomeCompleto'];
                                        ?>                                        
                                        </td>
                                        <td>
                                          <?php echo date("d/m/Y / H:i:s", strtotime("$obj[horaInicio]"));?>
                                          <!-- Exibindo data e hora formatadas com uma máscara -->
                                        </td>
                                        <td><?php 
                                          echo $obj['tempoEntrega']." horas.";
                                        ?>                                        
                                        </td>
                                      </tr>
                                      <?php
                                      }?>
                                    </tbody>
                                  </table>
                                <?php
                            }
                          ?>
                        </div>
                      </div>
                      <?php
                  }
                }
              ?>
          </div>                   
              
      </div>
     
    </div>
  </div>
    
</div>

<footer class="text-center" id="footer"><strong>Project Manager &copy </strong></footer>
		<script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js"></script>
		<script src="js/bootstrap.min.js"></script>
	</body>
</html>