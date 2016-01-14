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
  <script src="//code.jquery.com/jquery-1.12.0.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
</head>
<body>
<?php
//variaveis proprias
$mostraNota = false;
$reprovado = false;
$cota = 0;

//variaveis segundo form
if(isset($_GET["mat"]) && isset($_GET["nat"]) && isset($_GET["hum"]) && isset($_GET["lin"]) && isset($_GET["red"])){
  if($_GET["mat"] < 450 || $_GET["nat"] < 450 || $_GET["hum"] < 450 || $_GET["lin"] < 450 || $_GET["red"] < 450)
    $reprovado = true;
	else {
    $mostraNota = true;
  	if($curso == "bct") $nota = (1.5*$_GET["mat"] + 1.5*$_GET["nat"] + $_GET["hum"] + $_GET["lin"] + $_GET["red"]) / 6;
  	else if($curso == "bch") $nota = ($_GET["mat"] + $_GET["nat"] + 1.5*$_GET["hum"] + 1.5*$_GET["lin"] + $_GET["red"]) / 6;
  }
}

//Logica para verificar se aluno está aprovado ou nao
if(isset($_GET["campus"]) && isset($_GET["turno"]) && isset($_GET["curso"]) && isset($_GET["nota"])){
  //inicializa variaveis do primeiro form
  $campus = $_GET["campus"];
  $turno = $_GET["turno"];
  if(isset($_GET["cota"])){
   $cota = $_GET["cota"];
  } else {
    $cota = 0;
  }
  $curso = $_GET["curso"];
  $nota = $_GET["nota"];


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
        $msg .= "Em <strong>" . $i . "</strong> você seria aprovado na " . $row['chamada'] . "ª chamada<br>";
        break;
      }
    }
    if(!$foiChamado) $msg .= "Em <strong>" . $i . "</strong> você não foi aprovado em nenhuma chamada<br>";
  }
  //fecha bd
  $db->close();
}

?>
  <div class="container">
    <div class="jumbotron">
      <h1>Será que eu passo na <strong><span style="color: #006633;">UFABC</span></strong>?</h1>
      <p>Escolha uma das opções abaixo e veja se você passaria em 2013, 2014 ou 2015</p>
  </div>

  <div class="alert alert-info alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <?php echo isset($msg)?$msg:""; ?>
  </div>
  	
  <div class="alert alert-success alert-dismissible <?php if(!$mostraNota) echo "hide"; ?>" role="alert">
	  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	  Sua nota é: <strong><?php echo isset($nota)?$nota:""; ?></strong>.
	</div>

  <div class="alert alert-danger alert-dismissible <?php if(!$reprovado) echo "hide"; ?>" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    Infelizmente alguma das suas notas está abaixo de <strong>450</strong> e você não pode concorrer a uma vaga na UFABC <strong><i class="fa fa-frown-o"></i></strong>
  </div>

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
            <form action="index.php" method="get">
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
            <form action="index.php" method="get">
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
    <footer>
      <p>Desenvolvido por <strong><a href="mailto:matt_rodrigues@live.com">Matheus Rodrigues</a></strong> <a href="http://fb.com/mattrodriguees" target="_blank"><i class="fa fa-facebook-official"></i></a></p>
    </footer>
  </div>
</body>
</html>