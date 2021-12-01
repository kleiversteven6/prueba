<?php 
class Licencia extends Validate{

	function save_licencia($key){

		$query="select * from licencia where id_key='".$key."'";
		$datos= $this->_SQL_tool(array($this->SELECT_SINGLE, __METHOD__,$query));
		if (!empty($datos)) {
			$d=$this->Decrypting($key);
			$resp=json_decode($d);
			$upd_query="update licencia set id_key='$key' ,valid_inic='".$resp->desde."',valid_fin='".$resp->hasta."',direccion='".$resp->direccion."',rif='".$resp->rif."',nombemp='".$resp->empresa."',razonsocial='".$resp->razon."',correo='".$resp->correo."',estatus='1' where   id_key='".$key."'";
			$this->_SQL_tool(array($this->UPDATE, __METHOD__,$upd_query,'Validar licencia'));
		}else{
			$d=$this->Decrypting($key);
			$resp=json_decode($d);
			$upd_query="update licencia set estatus='0' ";
			$this->_SQL_tool(array($this->UPDATE, __METHOD__,$upd_query),'Desactivar licencia');
			$query="insert into licencia (id_key, valid_inic,valid_fin,direccion, rif , estatus, nombemp, razonsocial,correo)values('".$key."','".$resp->desde."','".$resp->hasta."','".$resp->direccion."','".$resp->rif."','1','".$resp->empresa."','".$resp->razon."','".$resp->correo."')";
			 
			$this->_SQL_tool(array($this->INSERT, __METHOD__,$query,'Agregar licencia'));
		}
	}
	function active_licencia(){
		$query="select id_key from licencia where estatus='1'";
		$datos= $this->_SQL_tool(array($this->SELECT_SINGLE, __METHOD__,$query));
		if (empty($datos)) {
			return 0;
		}

		$query="select * from licencia where id_key='".$datos['id_key']."' ";
		$datos= $this->_SQL_tool(array($this->SELECT_SINGLE, __METHOD__,$query));

			$d=$this->Decrypting($datos['id_key']);
			$resp=json_decode($d);
			$upd_query="update licencia set id_key='".$datos['id_key']."' ,valid_inic='".$resp->desde."',valid_fin='".$resp->hasta."',direccion='".$resp->direccion."',rif='".$resp->rif."',nombemp='".$resp->empresa."',razonsocial='".$resp->razon."',correo='".$resp->correo."',estatus='1' where   id_key='".$datos['id_key']."'";
			$this->_SQL_tool(array($this->UPDATE, __METHOD__,$upd_query,'Validar licencia activa'));
		

		$query="select * from licencia where estatus='1'";
		return $this->_SQL_tool(array($this->SELECT_SINGLE, __METHOD__,$query));
	}
		function mi_licencia(){
		$query="select * from licencia where estatus='1' ";
		$datos= $this->_SQL_tool(array($this->SELECT_SINGLE, __METHOD__,$query));
		if (empty($datos)) {
			$licencia['estatus']=0;
			$licencia['mensaje']="La aplicacion se encuentra sin lincencia activa. ";
			return $licencia;
		}
		$fecha=date("Y-m-d");
		$d=$this->Decrypting($datos['id_key']);
		$resp=json_decode($d);
		$licencia=array();
 		if ( $datos['valid_inic']==$resp->desde && $datos['valid_fin']==$resp->hasta && $datos['direccion']==$resp->direccion && $datos['rif']==$resp->rif && $datos['nombemp']==$resp->empresa && $datos['razonsocial']==$resp->razon && $datos['correo']==$resp->correo ) {
			$licencia=$datos;

			$datetime1 = new DateTime($datos['valid_fin']);
			$datetime2 = new DateTime($fecha);
			$dife = $datetime1->diff($datetime2);

			if ($dife->invert == 0) {
				$d=7-$dife->days;
				if ($dife->days > 7 ) {
					$licencia['estatus']=0;
					$licencia['mensaje']='Esta licencia a caducado hace '.$dife->days.' dias debes activar una nueva.';
				}elseif ($dife->days > 0) {
					$licencia['estatus'] =1;
					$licencia['mensaje']='Esta licencia caduco debe solicitar una nueva tienes '.$d.' dias para activarla.';
				}
			}else if ($dife->invert == 1) {
				if ($dife->days == 0) {
					$licencia['estatus'] =1;
					$licencia['mensaje']='Esta licencia caduco debe solicitar una nueva tienes 7 dias para activarla.';
				}else{
					$licencia['estatus'] =2;
					$dif=$dife->days;
					$licencia['dif'] =$dif;
					$licencia['mensaje']='Su licencia se encuentra activa, caduca en '.$dif.' dias.';
				}
			}



		}else{
			$licencia['estatus']=1;
			$licencia['mensaje']="Los datos de su licencia han sio alterados de forma inadecuada. ";
			$this->active_licencia();
			
		}
		return $licencia;
	}
}