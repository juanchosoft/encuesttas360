<?php 

	
define("DS", DIRECTORY_SEPARATOR);

require_once ($_SERVER["CONTEXT_DOCUMENT_ROOT"].DS."7division".DS."admin".DS."classes".DS."DbConection.php");
require_once ($_SERVER["CONTEXT_DOCUMENT_ROOT"].DS."7division".DS."admin".DS."classes".DS."Util.php");
require_once ($_SERVER["CONTEXT_DOCUMENT_ROOT"].DS."7division".DS."admin".DS."classes".DS."Estado.php");


function setMunicipios($codeDept = "05"){
	$db = new DbConection();
    $pdo = $db->openConect();

    $q = "SELECT * FROM ".$db->getTable('tbl_ciudades')." WHERE codigo_departamento = '$codeDept'";
    $result = $pdo->query($q);
    $db->closeConect();
    if ($result) {

    	foreach ($result as $key => $value) {
    		setPuntaje($value["codigo_muncipio"]);
    	}
    }
    return null;
}

function setPuntaje($codeMun){
	$db = new DbConection();
    $pdo = $db->openConect();
    $codeMun = ltrim($codeMun,"0");

    $q = "SELECT * FROM ".$db->getTable('tbl_vereda')." WHERE municipio_id = '$codeMun';";
    $result = $pdo->query($q);

    $sql = "";

    if ($result) {
    	foreach ($result as $key => $value) {
    		    		$sql.=Estado::setVereda($value["id"]);
    	}
    }
    $pdo->query($sql);
    $db->closeConect();
}


setMunicipios();

// $veredas  = Estado::getVeredasByColor("Bajo");

// var_dump($veredas);


 ?>