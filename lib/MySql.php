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
		if ($this->mysqli->connect_errno) { 
		   throw new Exception("Ошибка подключения к серверу MySQL:".$this->mysqli->connect_error, $this->mysqli->connect_errno); 
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
		$this->query($query, $params) ;
		
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
	
	//для запросов, возвращающих скалярное значение
	public function queryScalar($query, $params = false){
		$res = $this->query($query, $params) ;		
		return $res->fetch_row()[0];
	}
  
  	private function query($query, $params = false) {
		$query = $this->prepareQuery($query, $params) ;
		$success = $this->mysqli->query($query) ;
		if ($success) {
			$result = $success ;
		}
		else {
			throw new Exception($this->mysqli->errno." : ".$this->mysqli->error);
		}
		
		while($this->mysqli->more_results()){
			$this->mysqli->next_result();
			$this->mysqli->use_result();
		}
		return $result;
	}
}
?>
