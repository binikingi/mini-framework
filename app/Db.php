<?php

class Db{

	private $mysqli;

	public function __construct(array $database)
	{
		$this->mysqli = mysqli_connect($database['server'], $database['username'],
										$database['password'], $database['database']);
		if (!$this->mysqli) {
		    echo "Error: Unable to connect to MySQL." . PHP_EOL;
		    echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
		    echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
		    exit;
		}
	}

	public function update($tableName, array $values, array $primaryKey)
	{
		$sets = '';
		$first = true;
		foreach($values as $key=>$val){
			$keyEs = $this->mysqli->real_escape_string($key);
			$valEs = $this->mysqli->real_escape_string($val);
			if(!$first)
				$sets .= ', ';
			$sets .= $keyEs .= "='" . $valEs . "'";
			$first = false;
		}
		foreach($primaryKey as $key=>$val)
			$where = $this->mysqli->real_escape_string($key) . "=" . $this->mysqli->real_escape_string($val) ."";
		if(!$this->mysqli->query('update ' . $tableName . ' set ' . $sets . ' where ' . $where))
			echo mysqli_error($this->mysqli);
	}

	public function create($tableName, array $values)
	{
		$valueNames = '(';
		$rowValues = '(';
		foreach($values as $key => $val){
			$valueNames .= $key . ',';
			$rowValues .= "'".$val."'" . ',';
		}
		$valueNames[strlen($valueNames)-1] = ')';
		$rowValues[strlen($rowValues)-1] = ')';
		$this->mysqli->query('INSERT INTO ' . $tableName . ' ' . $valueNames . ' VALUES ' . $rowValues);
		return $this->mysqli->insert_id;
	}

	public function destroy($tableName, array $primaryKey){
		foreach($primaryKey as $key=>$val)
			$where = $key . '=' . $val;
		if(!$this->mysqli->query('DELETE FROM ' . $tableName . ' WHERE ' .$where))
			die(mysqli_error($this->mysqli));
		else 
			return true;
	}

	public static function find($tableName, $primaryKey, $primaryVal){
		$db = new Db($GLOBALS['database']);
		$row = $db->mysqli->query("SELECT * FROM `".$tableName."` WHERE `".$primaryKey."`='".$primaryVal."' LIMIT 1");
		if($row->num_rows <= 0)
			return false;
		return $row->fetch_assoc();
	}

	public static function where($tableName, $column, $value){
		$db = new Db($GLOBALS['database']);
		$rows = $db->mysqli->query("SELECT * FROM `".$tableName."` WHERE `".$column."` LIKE '".$value."'");
		if($rows->num_rows <= 0)
			return false;
		while($row = $rows->fetch_assoc()){
			$newLine = [];
			foreach($row as $key=>$val)
				$newLine[$key] = $val;
			$allAttributes[] = $newLine;
		}
		return $allAttributes;
	}

	public static function query($query){
		$db = new Db($GLOBALS['database']);
		return $db->mysqli->query($query);
	}
}