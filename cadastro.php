<?php 
  include 'conexao.php';
  // Incluindo o arquivo de conexão ao banco de dados
?>

<!DOCTYPE html>

<!-- Nas páginas de login e cadastro não foi utilizado Bootstrap para o estilo das páginas -->

<html>
  <head>
    <meta charset="UTF-8">
    <title>Cadastro - Project Manager</title>

    <link rel="stylesheet" href="css/style.css">

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
    <?php 
      @session_start();

      if (isset($_SESSION['login'])) {
        header("Location:index.php");
      }
    ?>
    <div id="login" style="width: 900px;height: 770px; margin-top: 20px; margin-bottom: 20px;">
      <h1 id="header">CADASTRAR</h1>

      <form name="formPagina" method="post" action="cadastrar.php" enctype="multipart/form-data">

      <!-- Formulário da página de cadastro -->

        <input style="width: 800px;" type="text" placeholder="Nome de Login" name="login" required/><br />
        <input style="width: 800px;" type="email" required name="email" placeholder="E-mail">
        <input style="width: 800px;" type="text" placeholder="Nome completo" name="completo" required/><br />
        <input style="width: 800px;" type="password" placeholder="Senha (Máximo 14; Mínimo 6)" name="senha" maxlength="14" required /><br />
        <input style="width: 800px;" type="password" placeholder="Repetir senha" name="reSenha" required /><br />
        <input style="width: 800px;" type="number" placeholder="Idade (deve ser maior que 16 anos)" name="idade" required /><br />
        <input style="width: 800px;" type="text" minlength="14" maxlength="14" onkeydown="mascaraCPF()" name="cpf" id="cpf" placeholder="CPF (Apenas números)" required><br />
        <input type="hidden" name="superuser" value="0">
        <!-- Este campo "superuser" do tipo hidden confirma o valor de cadastro para usuários comuns, já que seu valor é 0 -->
        <input style="width: 800px;" type="text" placeholder="Cidade" name="cidade" required /><br />
        <small> || UF:</small>
        <select required name="uf" id="uf">
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
        <small align="center"> || FOTO DE PERFIL:</small>
        
        <input  style="width: 300px; height: 50%; " type="file" name="arquivo" required accept="image/x-png, image/gif, image/jpg" /><br />
        <!-- Campo de arquivos, que só aceita imagens -->

        <a class="esquerda" href="login.php">Voltar</a>
        <input class="direita" type="reset" value="LIMPAR">
        <input style="width: 900px;" type="submit" value="SUBMETER" id="button"></input>

      </form>
    </div>

  </body>
</html>