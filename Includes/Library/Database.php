<?php

/*

Database Class

*/

class Database
{
	var $_DBHost;
	var $_DBName;
	var $_DBUser;
	var $_DBPsw;
	var $_DBLink;
	var $_SPName;
	
	var $ret;
	
	function ConnectDB($DBHost,$DBName,$DBUser,$DBPsw)
	{
		$this->_DBHost = $DBHost;
		$this->_DBName = $DBName;
		$this->_DBUser = $DBUser;
		$this->_DBPsw = $DBPsw;
				
		// connect db link
		
		$this->_DBLink = mysqli_connect($this->_DBHost, $this->_DBUser , $this->_DBPsw ,$this->_DBName);
						
		if(!$this->_DBLink)
		{
			$this->ret = false;
				
		}
		else
		{
			$this->ret = $this->_DBLink;	
		}
		
		return $this->ret;
	}
	
	function CloseDB()
	{
		mysqli_close($this->_DBLink);
	}
	
	function ExecuteSP($SPName)
	{
		$this->_SPName = $SPName;
		
		mysqli_query($this->_DBLink,"set character set utf8");
		mysqli_query($this->_DBLink,"set names 'utf8'");
		$result = mysqli_query($this->_DBLink, $this->_SPName);
		
		if(!$result)
			return false;
		
		return $result;
	}
	
	function ExecuteQuery($sql)
	{
		$result = mysqli_query($this->_DBLink, $sql);
		
		if(!$result)
			return false;
		
		return $result;
	}
	
}

?>