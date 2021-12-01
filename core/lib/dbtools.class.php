<?php
class cls_dbtools{

    
/******************************************************************
/*******  VARIABLES
/******************************************************************/
var $return_id='';
var $time='';
var $genera_log=true;
static $arr_codigosLogNoRewrite=array('INSERT'=>201,'UPDATE'=>202,'DELETE'=>203);
private $codigos=array('INSERT'=>201,'UPDATE'=>202,'DELETE'=>203);
private $add_myoptime_errors=true;
public static $dbDebug =array();
var $var_trans = '0';
var $INSERT = 'INSERT';
var $UPDATE = 'UPDATE';
var $DELETE = 'DELETE';
var $SELECT = 'SELECT';
var $SELECT_SINGLE = 'SELECT_SINGLE';
var $_DIE = 'DIE';
var $_ECHO = 'ECHO';
public static $DBParameters = array();
private $DBconnection = array();
private $resultDB = false;
public static $DBsession = '';

    
public static function assignDBParameters($arrDB){
	self::$DBParameters = $arrDB;
}
function _connectDB($app){
$arrDBP = self::$DBParameters[$app];


$this->DBconnection[$app] = new mysqli($arrDBP['host'], $arrDBP['user'], $arrDBP['pass'],$arrDBP['db'],$arrDBP['port'])or die($mysqli->connect_errno);
	if(!is_object($this->DBconnection[$app]))
	{
		//echo ' => NO conecto';
		return false;
	}
	else
	{
	   //echo ' => SI conecto';
		return true;
	}
}
    
function _SQL_tool($request ){

    $tipo=$request[0];
    $funct_call=$request[1]; 
    $query=$request[2]; 
    $comentario=(isset($request[3])) ? $request[3] : "";
    $tabla=(isset($request[4])) ? $request[4] : "";
    $viewQ=(isset($request[5])) ? $request[5] : "";
    $app=(isset($request[6])) ? $request[6] : '_DEFAULT';
    $user='0';
    cls_dbtools::$dbDebug[] = array('class'=>get_class($this),'method'=>$funct_call,'query'=>$query,'time'=>  $this->time);
    

    $tipo=strtoupper($tipo);
    $this->return_id = '';
    $query = trim($query);
    // Chequeo de conexion
    $this->check_connect($app);

    switch($tipo){

            case 'SELECT':
                        if( stripos($query,'GROUP_CONCAT') !== false ){ $this->alterar_group_concat_max_len($app); }
                        set_time_limit(0);
                        ini_set('memory_limit',-1);
                       // if(!isset($calcrows)){ $query = substr($query,0,6)." SQL_CALC_FOUND_ROWS ".substr($query,6); }
                        $inicio = microtime();
                        $result = mysqli_query($this->DBconnection[$app],"set names 'utf8'");
                        $result = mysqli_query($this->DBconnection[$app],$query);
                        $fin = microtime();
                     
                        $res_array = array ();
                        $i = 0;
                        //Consulta general
                        if ($result) {
                                while($rows=mysqli_fetch_assoc($result)){
                                        foreach($rows as $columna => $valor){
                                                $res_array[$i][$columna] = $valor;
                                        }
                                        $i++;
                                }
                                //$rows2=mysqli_fetch_assoc($result);
                                //print_r($rows=mysqli_fetch_assoc($result));
                                //Para retornar el total de registros si no existiera el limite
                                $result = mysqli_query($this->DBconnection[$app],'SELECT FOUND_ROWS() as total');
                                if($row=mysqli_fetch_assoc($result)){
                                        $this->total_verdadero = $row['totalreg'];
                                } else {
                                        $this->total_verdadero = 0;
                                }

                                return $res_array;
                        }else{
                            return 0;
                        }
                    
                    break;
            case 'SELECT_SINGLE':
                    if( stripos($query,'GROUP_CONCAT') !== false ){ $this->alterar_group_concat_max_len($app); }
                    $inicio = microtime();
                    //~ $result = mysqli_query("set names 'utf8'");
                    $result = mysqli_query($this->DBconnection[$app],$query);

                    $fin = microtime();
                    $this->time = $fin - $inicio;
                    $res_array=array();
                    if ($result) {
                            if($rows=mysqli_fetch_assoc($result)){
                                    foreach($rows as $columna => $valor){
                                            $res_array[$columna] = $valor;
                                    }
                            }
                            return $res_array;
                    }else{
                        return 0;
                    }
                    break;
            case 'INSERT':
            case 'UPDATE':
            case 'DELETE':
                //return false;
                //include_once(APPROOT.'../../browser_detection/your_computer_info.php');

                //$html=mysqli_real_escape_string($html);
                    $return_value="0";
                    try{
                            $inicio = microtime();
                            $result = mysqli_query($this->DBconnection[$app],"set names 'utf8'");
                            $result = mysqli_query($this->DBconnection[$app],$query);
                            $query=addslashes($query);
                            if($result){
                                    $return_value = true;
                                    if($tipo=='INSERT'){
                                            $this->return_id = mysqli_insert_id($this->DBconnection[$app]);
                                            $return_value = $this->return_id;
                                    }
                            }else{
                                return 0;
                            }
                                //****************************************
                                //****************************************
                                if ($this->genera_log==true) {
                                    $this->save_log($request);
                                }
                                //****************************************
                                //****************************************                            
                        return $return_value;
                    } catch(Exception $e) {
                            die("Sentencia no corresponde con el primer parametro de la funcion _SQL_tool. Debe ser corregido para continuar");
                    }
                    break;
    }
}
/**
 * Propiedad para alargar el resultado de la lista al ejecutar
 * el comando GROUP_CONCAT de mysqli al hacer un select
*/
    private function alterar_group_concat_max_len(){
    		//Hay que quitar el limite de la funcion para poder mostrar todos los posibles valores
    		$prequery="SET @@group_concat_max_len = 9999999";
    		mysqli_query($this->DBconnection[$app],$prequery);
    }
    function check_connect($app){
    	$arr_config = '';
        
    	if (!isset($this->DBconnection[$app]) && !is_object($this->DBconnection[$app])){
    		if (!isset($this->DBconnection[$app]) && !is_object($this->DBconnection[$app])){
    				$this->_connectDB($app);
                }
        }
    } 
    private function save_log($request){
        $tipo=$request[0];
        $funct_call=$request[1]; 
        $query=addslashes($request[2]); 
        $comentario=(isset($request[3])) ? $request[3] : "";
        $tabla=(isset($request[4])) ? $request[4] : "";
        $viewQ=(isset($request[5])) ? $request[5] : "";
        $app=(isset($request[6])) ? $request[6] : '_DEFAULT';
        $user=$_SESSION['USER']['id'];
        $insert="insert into hist_log(funcion,query,comentario,tabla,conexion,usuario,tipo)values('$funct_call','$query','$comentario','$tabla','$app','$user','$tipo')";
        //$query=addslashes($query);
        $result = mysqli_query($this->DBconnection[$app],"set names 'utf8'");
        $result = mysqli_query($this->DBconnection[$app],$insert);
        
    }
    
}
?>