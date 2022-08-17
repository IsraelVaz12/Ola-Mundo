<?php

class Processadores
{
  public $id;
  public $fabricante;
  public $modelo;
  public $frequencia;
  public $nucleos;
  public $consumo;

  function __construct($id, $fabricante, $modelo, $frequencia, $nucleos, $consumo)
  {
    $this->id = $id;
    $this->fabricante = $fabricante;
    $this->modelo = $modelo;
    $this->frequencia = $frequencia;
    $this->nucleos = $nucleos;
    $this->consumo = $consumo;
  }
}

class Fabricantes
{
  public $id;
  public $modelo;

  function __construct($id, $modelo)
  {
    $this->id = $id;
    $this->modelo = $modelo;
  }
}


$fabricante = $_GET['fabricante'] ?? '';
$id = $_GET['id'] ?? '';

$arrayDeObjetos = [];

require "conexaoMysql.php";
$pdo = mysqlConnect();


if ($id == '' && $fabricante == '') {
  try {
    $sql = <<<SQL
  SELECT DISTINCT fabricante
  FROM processadores
  SQL;

    $stmt = $pdo->query($sql);

    $i = 0;
    while ($row = $stmt->fetch()) {
      $arrayDeObjetos[$i] =  htmlspecialchars($row['fabricante']);
      $i++;
    }
  } catch (Exception $e) {
    exit('Ocorreu uma falha: ' . $e->getMessage());
  }
} else if ($id != '') {
  try {
    $sql = <<<SQL
  SELECT *
  FROM processadores
  WHERE id = ?
  SQL;
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);

    $i = 0;
    while ($row = $stmt->fetch()) {

      $idResponse  = $row['id'];
      $fabricanteResponse = htmlspecialchars($row['fabricante']);
      $modeloResponse = htmlspecialchars($row['modelo']);
      $frequenciaResponse = htmlspecialchars($row['frequencia']);
      $nucleosResponse = htmlspecialchars($row['nucleos']);
      $consumoResponse = htmlspecialchars($row['consumo']);


      $arrayDeObjetos[$i] =  new Processadores($idResponse,  $fabricanteResponse, $modeloResponse, $frequenciaResponse, $nucleosResponse, $consumoResponse);
      $i++;
    }
  } catch (Exception $e) {
    exit('Ocorreu uma falha: ' . $e->getMessage());
  }
} else if ($id == '' && $fabricante != '') {
  try {
    $sql = <<<SQL
  SELECT id, modelo
  FROM processadores
  WHERE fabricante = ?
  SQL;

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$fabricante]);


    $i = 0;
    while ($row = $stmt->fetch()) {

      $idResponse  = $row['id'];
      $modeloResponse = htmlspecialchars($row['modelo']);

      $arrayDeObjetos[$i] =  new Fabricantes($idResponse,  $modeloResponse);
      $i++;
    }
  } catch (Exception $e) {
    exit('Ocorreu uma falha: ' . $e->getMessage());
  }
}

header('Content-type: application/json');
echo json_encode($arrayDeObjetos);
