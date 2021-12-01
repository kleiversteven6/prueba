<?php
class General extends Validate{
	function get_leads(){
		$query="SELECT content FROM fro_formcraft_3_submissions ";
		return $this->_SQL_tool(array($this->SELECT, __METHOD__,$query));
	}
}
