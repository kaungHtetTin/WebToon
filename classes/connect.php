<?php

class Database {

	// private $host="localhost";
	// private $username="u148234809_webtoon";
	// private $password="Webtoon5241@@";
	// private $db="u148234809_webtoon";

	private $host="localhost";
	private $username="root";
	private $password="";
	private $db="webtoon2";

	//this is git ignore file
	// I want to ignore this file

	function connect(){
		$connection=mysqli_connect($this->host,$this->username,$this->password,$this->db);
		return $connection;
	}

	function read($query){
		$conn=$this->connect();

		$result=mysqli_query($conn,$query);

		if(!$result){
			return false;
		}else{

			$data=false;
			 while ($row=mysqli_fetch_assoc($result)) {
					 
					$data[]=$row;
			}

			return $data;

		}
	}

	function save($query){
		$conn=$this->connect();
		$result=mysqli_query($conn,$query);

		if(!$result){
			return false;
		}else{
			return true;
		}
	}

}
