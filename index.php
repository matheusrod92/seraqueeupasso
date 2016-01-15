<?php 
//inicializa banco de dados
class MyDB extends SQLite3{
//cria o banco de dados sqlite
  function __construct(){
    $this->open('sqep.db');
  }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Será que eu passo na UFABC?</title>
  <link rel="icon" type="image/png" href="favicon.png" />
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
  <link rel="stylesheet" type="text/css" href="style.css">
  <link rel="stylesheet" href="http://ufabchelp.me/assets/css/default.css">
  <script src="//code.jquery.com/jquery-1.12.0.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
</head>
<body>
<?php
//variaveis proprias
$mostraNota = false;
$reprovado = false;
$resultado = false;
$passou = false;
//variaveis segundo form
if(isset($_POST["curso"]) && isset($_POST["mat"]) && isset($_POST["nat"]) && isset($_POST["hum"]) && isset($_POST["lin"]) && isset($_POST["red"])){
  if($_POST["mat"] < 450 || $_POST["nat"] < 450 || $_POST["hum"] < 450 || $_POST["lin"] < 450 || $_POST["red"] < 450)
    $reprovado = true;
  else {
    $curso = $_POST["curso"];
    $mostraNota = true;
    if($curso == "bct") $nota = (1.5*$_POST["mat"] + 1.5*$_POST["nat"] + $_POST["hum"] + $_POST["lin"] + $_POST["red"]) / 6;
    else if($curso == "bch") $nota = ($_POST["mat"] + $_POST["nat"] + 1.5*$_POST["hum"] + 1.5*$_POST["lin"] + $_POST["red"]) / 6;
  }
}
//Logica para verificar se aluno está aprovado ou nao
if(isset($_POST["campus"]) && isset($_POST["turno"]) && isset($_POST["curso"]) && isset($_POST["nota"])){
  //inicializa variaveis do primeiro form
  $campus = $_POST["campus"];
  $turno = $_POST["turno"];
  $curso = $_POST["curso"];
  $nota = $_POST["nota"];
  if(isset($_POST["cota"])){
   $cota = $_POST["cota"];
  } else {
    $cota = 0;
  }
  //mostra o div com o resultado
  $resultado = true;
  //instancia bd
  $db = new MyDB();
  //inicializa mensagem de aprovação ou não
  $msg = '';
  //loop para verificar aprovação em cada ano
  for($i=2013; $i<2016; $i++){
    $maiorNota = "SELECT corte, chamada FROM notas WHERE ano = '" . $i . "' AND campus = '" . $campus . "' AND turno = '" . $turno . "' AND cota = '" . $cota . "' AND curso = '" . $curso . "';";
    $foiChamado = false;
    $ins = $db->query($maiorNota);
    while($row = $ins->fetchArray(SQLITE3_ASSOC)){
      if($nota > $row['corte']){
        $foiChamado = true;
        $passou = true;
        $msg .= "<p>Em <strong>" . $i . "</strong> você seria aprovado na " . $row['chamada'] . "ª chamada</p>";
        break;
      }
    }
    if(!$foiChamado) $msg .= "<p>Em <strong>" . $i . "</strong> você não seria aprovado em nenhuma chamada</p>";
  }
  //fecha bd
  $db->close();
}
?>
<div id="header-wrapper">
  <div id="header" class="container">
    <div id="logo">
      <h1><a href="#">UFABC Help&nbsp;<span class="logo-help"></span></a></h1>
    </div>
    <div id="menu">
      <ul>
        <li><a href="http://www.ufabchelp.me" accesskey="1" title="">Principal</a></li>
        <li><a href="http://www.ufabchelp.me/avaliacoes" accesskey="2" title="">Avaliações</a></li>
        <li class="current_page_item"><a href="http://www.ufabchelp.me/seraqueeupasso" accesskey="3" title="" target="_blank">Será que eu passo?</a></li>
        <li><a href="http://www.ufabchelp.me/lab" accesskey="4" title="">Help LAB</a></li>
        <li><a href="http://www.ufabchelp.me/contato" accesskey="5" title="">Contato</a></li>
      </ul>
    </div>
  </div>
</div>
<div id="header-featured">
  <div class="inner-bg">
    <div id="banner-wrapper">
      <div id="banner" class="container">
        <img src="logo-ufabc.png"  height="200px">
        <h1><strong>Será que eu passo?</strong></h1>
        <p>Escolha uma das opções abaixo e veja se você passaria em 2013, 2014 ou 2015</p>
        <br>
      </div>
    </div>
  </div>
</div> 
<div class="wrapper container">
  <?php if($resultado): ?>
    <div class="alert alert-<?php echo ($passou)?'success':'danger'; ?> alert-dismissible" role="alert">
      <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <?php echo isset($msg)?$msg:""; ?>
      <?php if($passou): ?>
        <p><strong>Parabéns,</strong> você foi aprovado em alguma chamada! Mas lembre-se, este site é <strong>apenas uma simulação</strong> em relação aos anos anteriores, 
        então fique de olho nas chamadas no site da <a href="http://prograd.ufabc.edu.br/sisu" target="_blank">PROGRAD</a> e não se esqueça de dar uma socializada 
        no <a href="https://www.facebook.com/groups/1077941312230492/?fref=ts" target="_blank">grupo dos bixos no facebook</a>.</p>
      <?php else: ?>
        <p>Infelizmente você não foi aprovado em nenhuma chamada <i class="fa fa-frown-o"></i>...</p>
        <p>Continue acompanhando o resultado oficial no site do Sisu. E se caso não der certo desta vez, <strong>não desista</strong>, continue estudando que poderemos nos ver aqui nos próximos anos. Caso tenha alguma dúvida é só entrar no 
        <a href="https://www.facebook.com/groups/ufabc/" target="_blank">grupo do facebook</a> e conversar com a galera! Aproveita e da 
        uma olhadinha no cursinho ministrado pelos alunos: <a href="http://www.epufabc.com.br/" target="_blank">EPUFABC</a>.</p>
      <?php endif; ?>
    </div>
  <?php endif; ?>

  <?php if($mostraNota): ?>
    <div class="alert alert-info alert-dismissible" role="alert">
      <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      Sua nota é: <strong><?php echo isset($nota)?$nota:""; ?></strong>.
    </div>
  <?php endif; ?> 
  
  <?php if($reprovado): ?>
    <div class="alert alert-danger alert-dismissible" role="alert">
      <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      Infelizmente alguma das suas notas está abaixo de <strong>450</strong> e você não pode concorrer a uma vaga na UFABC <strong><i class="fa fa-frown-o"></i></strong>
    </div>
  <?php endif; ?>

    <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
      <div class="panel panel-default">
        <div class="panel-heading" role="tab" id="headingOne">
          <h4 class="panel-title">
            <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
              Já sei minha nota
            </a>
          </h4>
        </div>
        <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
          <div class="panel-body">
            <form action="/" method="post">
              <div class="form-group">
                <label for="Campus">Campus*:</label>
                <div class="radio"><label><input type="radio" name="campus" value="sa" checked>Santo André</label></div>
                <div class="radio"><label><input type="radio" name="campus" value="sbc">São Bernardo</label></div>
              </div>
              <div class="form-group">
                <label for="Turno">Turno*:</label>
                <div class="radio"><label><input type="radio" name="turno" value="mat" checked>Diurno</label></div>
                <div class="radio"><label><input type="radio" name="turno" value="not">Noturno</label></div>
              </div>
              <div class="form-group">
                <label for="Cotas">Cota:</label>
                <div class="radio"><label for="EP"><input type="radio" name="cota" value="1">Ensino Público</label></div>
                <div class="radio"><label for="EP PPI"><input type="radio" name="cota" value="2">Ensino Público + PPI</label></div>
                <div class="radio"><label for="EP R"><input type="radio" name="cota" value="3">Ensino Público + Renda</label></div>
                <div class="radio"><label for="EP PPI R"><input type="radio" name="cota" value="4">Ensino Público + PPI + Renda</label></div>
              </div>
              <div class="form-group">
                <label for="Curso">Curso*:</label>
                <div class="radio"><label><input type="radio" name="curso" value="bct" checked>BC&T</label></div>
                <div class="radio"><label><input type="radio" name="curso" value="bch">BC&H</label></div>
              </div>
              <div class="form-group">
                <label for="Nota">Nota*:</label>
                <input type="text" name="nota" class="form-control" value="<?php if(isset($nota)) echo $nota; ?>" required>
              </div>
              <button type="submit" class="btn btn-primary btn-block">Enviar</button>
            </form>
          </div>
        </div>
      </div>
      <div class="panel panel-default">
        <div class="panel-heading" role="tab" id="headingTwo">
          <h4 class="panel-title">
            <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
              Não sei minha nota
            </a>
          </h4>
        </div>
        <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
          <div class="panel-body">
            <form action="/" method="post">
              <div class="form-group">
                <label for="Curso">Curso:</label>
                <div class="radio"><label><input type="radio" name="curso" value="bct" checked>BC&T</label></div>
                <div class="radio"><label><input type="radio" name="curso" value="bch">BC&H</label></div>
              </div>
              <div class="form-group">
                <label for="Nota">Notas:</label>
                <input type="text" name="mat" class="form-control" placeholder="Matemática e suas tecnologias" required>
              </div>
              <div class="form-group">
                <input type="text" name="nat" class="form-control" placeholder="Ciências da natureza e suas tecnologias" required>
              </div>
              <div class="form-group">
                <input type="text" name="hum" class="form-control" placeholder="Ciências humanas e suas tecnologias" required>
              </div>
              <div class="form-group">
                <input type="text" name="lin" class="form-control" placeholder="Linguagens, códigos e suas tecnologias" required>
              </div>
              <div class="form-group">
                <input type="text" name="red" class="form-control" placeholder="Redação" required>
              </div>
              <button type="submit" class="btn btn-primary btn-block">Enviar</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
    <div id="copyright">
      <p>Desenvolvido por 
        <strong>
          <a id="matt" tabindex="0" role="button" data-toggle="popover" data-trigger="focus" data-placement="top">
            Matheus Rodrigues
          </a>
        </strong>
      </p>
    </div>
  <script>
  var tmsg = '<strong>Quer saber mais?</strong>';
  var info = '<span><i class="fa fa-envelope-o"></i> matt_rodrigues@live.com<br>' + 
  '<a href="http://fb.com/mattrodriguees" target="_blank"><i class="fa fa-facebook-official"></i> fb.com/mattrodriguees</a><br>' +
  '<a href="http://github.com/mattS1XX" target="_blank"><i class="fa fa-code-fork"></i> github.com/mattS1XX</a></span>';
  $(document).ready(function() {
    $('#matt').popover({title : tmsg, content : info, html : true});
  });
  </script>
</body>
</html>