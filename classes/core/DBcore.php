<?php
class DBcore{

	function __construct($host, $user, $pass, $db_tables){
		$this->connection = new PDO($host, $user, $pass);
		$this->db_tables = $db_tables;
	}

	protected function getLastSequence($table, $show_id=false){
		$this->handleFakeTables($table);
		$where = ($show_id ? ' WHERE show_id=:show_id' : '');
		$params = ($show_id ? array('show_id'=>$show_id) : array());
		if($show_id){
			$params = array('show_id'=>$show_id);
		}
		$sequence = 0;
		$rows = $this->select('SELECT MAX(sequence) AS sequence FROM '.$table.$where, $params);
		if($rows[0]['sequence']){
			$sequence = $rows[0]['sequence'];
		}
//		logDebug('getLastSequence returning: '.$sequence);
		return $sequence;
	}

	protected function query($query, $values=array()){
		$return = false;
		try{
			$stmt = $this->connection->prepare($query);
			$params = array();//purely for logging purposes
			foreach($values as $column=>$value){
				$stmt->bindValue(':'.$column, $value);
				$params[':'.$column] = $value;
			}
			logDebug('query: '.$query);
			if($params){ logDebug('param: '.var_export($params, true)); }
			$return = $stmt->execute();
		}catch (PDOException $e){
			logDebug('ERROR', 'ERROR selecting: '.var_export($e, true));
			logDebug('ERROR', 'params: '.var_export($values, true));
		}
//		logDebug('query returning: '.var_export($return, true));
		return $return;
	}

	protected function select($query, $values=array()){
		$return = false;
		try{
			$stmt = $this->connection->prepare($query);
			$params = array();//purely for logging purposes
			foreach($values as $column=>$value){
				$stmt->bindValue(':'.$column, $value);
				$params[':'.$column] = $value;
			}
			logDebug('query: '.$query);
			if($params){ logDebug('param: '.var_export($params, true)); }
			$stmt->execute();
			$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
			if(isset($rows[0])){
				$return = $rows;
			}
		}catch (PDOException $e){
			logDebug('ERROR', 'ERROR selecting: '.var_export($e, true));
			logDebug('ERROR', 'params: '.var_export($values, true));
		}
//		logDebug('select returning: '.var_export($return, true));
		return $return;
	}

	protected function update($query, $values=array()){
		$return = false;
		try{
			$stmt = $this->connection->prepare($query);
			$params = array();//purely for logging purposes
			foreach($values as $placeholder=>$value){
				$stmt->bindValue(':'.$placeholder, $value);
				$params[':'.$placeholder] = $value;
			}
			logDebug('query: '.$query);
			if($params){ logDebug('param: '.var_export($params, true)); }
			$stmt->execute();
			$return = $stmt->rowCount();
		}catch (PDOException $e){
			logDebug('ERROR', 'ERROR updating: '.var_export($e, true));
			logDebug('ERROR', 'params: '.var_export($values, true));
		}
//		logDebug('update returning: '.$return);
		return $return;
	}

	protected function insert($table, $values=array()){
		$this->handleFakeTables($table);
		$return = false;
		try{
			$query = 'INSERT INTO '.$table;
			$firstIteration = true;
			foreach($values as $column=>$value){
				$query .= ($firstIteration ? ' (' : ', ').$column;
				$firstIteration = false;
			}
			if(!$firstIteration){ $query .= ')'; }
			if(!empty($values)){ $query .= ' VALUES '; }
			$firstIteration = true;
			foreach($values as $column=>$value){
				$query .= ($firstIteration ? ' (' : ', ').':'.$column;
				$firstIteration = false;
			}
			if(!$firstIteration){ $query .= ')'; }
			$stmt = $this->connection->prepare($query);
			$params = array();//purely for logging purposes
			foreach($values as $column=>$value){
				$stmt->bindValue(':'.$column, $value);
				$params[':'.$column] = $value;
			}
			logDebug('query: '.$query);
			if($params){ logDebug('param: '.var_export($params, true)); }
			if(!$stmt->execute()){
				logDebug('ERROR', 'ERROR executing query: '.$this->connection->errorCode().': '.var_export($this->connection->errorInfo(), true));
			}else{
				$return = $this->connection->lastInsertId();
			}
		}catch (PDOException $e){
			logDebug('ERROR', 'ERROR inserting: '.var_export($e, true));
			logDebug('ERROR', 'params: '.var_export($values, true));
		}
		logDebug('insert returning: '.$return);
		return $return;
	}

	protected function delete($table, $where=false, $values=array()){
		$this->handleFakeTables($table);
		$return = false;
		try{
			$query = 'DELETE FROM '.$table.($where?' WHERE '.$where:'');
			$stmt = $this->connection->prepare($query);
			$params = array();//purely for logging purposes
			foreach($values as $column=>$value){
				$stmt->bindValue(':'.$column, $value);
				$params[':'.$column] = $value;
			}
			logDebug('query: '.$query);
			if($params){ logDebug('param: '.var_export($params, true)); }
			$stmt->execute();
			$return = $stmt->rowCount();
		}catch (PDOException $e){
			logDebug('ERROR', 'ERROR selecting: '.var_export($e, true));
			logDebug('ERROR', 'params: '.var_export($values, true));
		}
		logDebug('delete returning: '.$return);
		return $return;
	}

	private function handleFakeTables($table){
		if(!in_array($table, $this->db_tables)){
			$msg = 'ERROR: attempted access of fake table ['.$table.'], exiting';
			logDebug($msg);
			exit;
		}
	}

	private $db = NULL;
	private $connection = NULL;//connection
	private $db_tables = NULL;
}