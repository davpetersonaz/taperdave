<?php
namespace classes;

class Show{
	
	public function __construct($db){
		$this->db = $db;
	}
	
	private $db = NULL;
	
}