<?php
	$lead = $obj_General->get_leads();
	$leads['error']=0;
	
	foreach($lead as $l){
		$content=stripslashes($l['content']);
		$leads['leads'][]=$content;
	}
	echo json_encode($leads);
	/*
 [
 {"label":"Nombres","value":"Diego Test","identifier":"field1","type":"oneLineText","page":1,"page_name":"Step 1","width":"50%","altLabel":"Nombres"}
 ,{"label":"Telefono","value":"04126660400","identifier":"field6","type":"oneLineText","page":1,"page_name":"Step 1","width":"50%","altLabel":"Telefono"}
 ,{"label":"Correo electronico","value":"ccdiego.ve@gmail.com","identifier":"field2","type":"email","page":1,"page_name":"Step 1","width":"100%","altLabel":"Correo electronico"}
]
	{"DB":{"_DEFAULT":{"host":"localhost","user":"root","pass":"","port":"3306","db":"dbxgv18mdibyvx"}},"FOLDER":"api.wisplaynetworks.com"}*/
?>