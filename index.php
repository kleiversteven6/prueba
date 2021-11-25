<?php
require_once("core/config_var.php");
if(!isset($_GET['view']) ){
	$mensaje=array("error"=>1,"mensaje"=>"No ha indicado una ruta de consulta");
    die(json_encode($mensaje));
}else{
    if(file_exists(APIROOT.$_GET['view'].'.php')){
        include(APIROOT.$_GET['view'].'.php');
        die();
    }else{
    	$mensaje=array("error"=>1,"mensaje"=>"El archivo de consulta no existe");
        die(json_encode($mensaje));
    }
}

?>