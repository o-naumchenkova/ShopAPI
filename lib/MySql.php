<?php
class MySqlWork{
	//private static $db = null;
	protected $mysqli; 
	private $sym_param = "{?}";
	
	private  $host = 'localhost';
	private  $user = 'root';
	private  $password = '';
	private  $database = 'shop';
	
	function __construct() {
       $this->_connect();
   }
	
	private function _connect(){
		
		$this->mysqli = new mysqli($this->host, $this->user, $this->password, $this->database); 
		if (mysqli_connect_errno()) { 
		   echo "Ошибка подключения к серверу MySQL:".mysqli_connect_error(); 
		   exit; 
		} 
	}
	
	
	private function resultToArray($result) {
		$array = array();
		
		$success = $this->mysqli->use_result();
		if ($result->num_rows !== 0) {
			while (($row = $result->fetch_assoc()) != false) {
				$array[] = $row;
			}
		}		
		$result->close();
		return $array;
	}
  
	public function prepareQuery($query, $params){

		if (!empty($params)) {
			for ($i = 0; $i < count($params); $i++) {
				$pos = strpos($query, $this->sym_param);
				if(is_numeric($params[$i])){
					$arg = $params[$i];
				}elseif(is_null($params[$i])){
					$arg = "NULL";
				}
				else{
					$arg = "'".$this->mysqli->real_escape_string($params[$i])."'";
				}				
				$query = substr_replace($query, $arg, $pos, strlen($this->sym_param));				
			}
		}
		return $query;
	 }
	
	//для запросов, не требующих возвращаемых ответов, таких как create, upd, del (при вставке возвращается id добавленной записи)
	public function querySimple($query, $params = false){
		$res = $this->query($query, $params) ;
		
		if($this->mysqli->insert_id === 0){
			$result = true;
		} else {
			$result = $this->mysqli->insert_id;		
		}
		
		return $result;
	}
	
	//для запросов, возвращающих массив значений 
	public function queryArray($query, $params = false){
		$res = $this->query($query, $params) ;		
		return $this->resultToArray($res);
	}
  
  	private function query($query, $params = false) {
		$query = $this->prepareQuery($query, $params) ;
		$success = $this->mysqli->query($query) ;
		if ($success) {
			$result = $success ;
		}
		else {
			//$result = array("is_mysql_error"=>1, "error_text"=>$this->mysqli->error, "err_code"=>$this->mysqli->errno, "query"=>$query);			
			throw new Exception($this->mysqli->errno." : ".$this->mysqli->error);
		}
		
		while($this->mysqli->more_results()){
			$this->mysqli->next_result();
			$this->mysqli->use_result();
		}
		return $result;
	}
  
	/*
	public function query($query, $params = false) {
		$query = $this->prepareQuery($query, $params) ;
		//print_r($this->prepareQuery($query, $params));
	//	return ; 
		$success = $this->mysqli->query($query) ;

		if ($success) {
			if($this->mysqli->insert_id === 0){
				if($this->mysqli->affected_rows){
					$result = $this->resultToArray($success);
				}else{
					$result = true;
				}
			}else{
				$result = $this->mysqli->insert_id;				
			}
		}
		else {
			$result = array(is_error=>1, error_text=>$this->mysqli->error, query=>$query);			
		}
		return $result;
	}
	*/
}
?>