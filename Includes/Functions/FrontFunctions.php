<?php
	
	
	include_once("require.php");
	
	/* Database connection  */
	function ConnectDB($db)
	{
		
		global $DatabaseHost;
		global $DatabaseName;
		global $DatabaseUser;
		global $DatabasePsw;
		
		$db->ConnectDB($DatabaseHost,$DatabaseName,$DatabaseUser,$DatabasePsw);	
	}

	
	// this function calculate the page load
	function pageLoadTime()
	{
		$time = explode(' ', microtime());  
		$time =  $time[1] + $time[0]; 
		
		return $time;
	}
	
	
	// this function gets the last insert id
	function lastInsertId()
	{
		$db = new Database();
		ConnectDB($db);	
		
		$query = "SELECT id 
				  FROM shorty
				  ORDER BY id DESC LIMIT 1";
		
		$result = $db->ExecuteQuery($query);		
		$id = mysqli_fetch_assoc($result);
		return $id['id'];
	}
	
	
	// make hash of url
	function makeHash($url)
	{
		return md5($url); // hashing the given url
	}
	
	
	// this function check if the link already exist or not 
	function isExist($fullCode)
	{
		$db = new Database();
		ConnectDB($db);	
		
		$query = "SELECT short_code 
				  FROM shorty
				  WHERE full_code = '$fullCode' ";
		
		$result = $db->ExecuteQuery($query);

		if($result)
		{
			$code = mysqli_fetch_assoc($result);
			return $code['short_code'];
		}
		else
			return 0;
		
		$db->CloseDB();		
	}
	
	
	// this function will give the short code
	function makeShorty($url)
	{
		global $URL;
		$shortCode = array();
		$fullCode = makeHash($url); // making hash of url
		$tempUrl = $url;
		$exist = isExist($fullCode); // checking if the hashcode already exist or not
		
		if($exist == NULL)
		{
			$tempLastId = lastInsertId(); // getting last insert id
			$hashCode = substr(makeHash($url), 0,3); // get first 3 character of hashcode
			$shortCode['shortCode'] = substr(md5($tempLastId.$hashCode),0,4); // combining lastinderID and hashcode
			$shortCode['isExist'] = 0; // 0 means code not exist
		}
		else
		{
			$shortCode['shortCode'] = $exist; // shortcode already exist
			$shortCode['isExist'] = 1; // 1 means the code already exist
		}
			$shortCode['fullCode'] = $fullCode;
			return $shortCode;
	}
			
	
	// this function is used to check "http://" or "https://" in url if not than add them
	function makeValidUrl($url)
	{
		$tempUrl = $url;
		
		if(preg_match("/http/", $tempUrl) || preg_match("/https/", $tempUrl) || preg_match("/ftp/", $tempUrl) || preg_match("/shttp/", $tempUrl))
		{			
			return $tempUrl;
		}		
		else
		{
			return $tempUrl = "http://".$tempUrl;
		}
	}
	
	
	// this function first call the function "makeShorty" to get a short code and save it in database
	function saveUrl($url)
	{
		$db = new Database();
		ConnectDB($db);
		$arr = array();		
		
		$shortCode = makeShorty($url);
		$realUrl = makeValidUrl($url);
		
		if($shortCode['isExist'] == 0)
		{
			$fullCode = makeHash($url); // making hash code of url;
			
			$query = "CALL saveShortUrly('".$realUrl."','".$shortCode['shortCode']."','".$fullCode."');";

			$result = $db->ExecuteQuery($query);
		  
			if(!$result)
			{
				$db->CloseDB();
				return false;   
			}
			else
			{
				return $shortCode['shortCode'];   
			}
		}
		else
		{
			return $shortCode['shortCode'];
		}
		
   		$db->CloseDB();   		
	}
	
	
	// this function check if shortcode is in the database and return full url
	function redirectToWebsite($shortCode)
	{
		$db = new Database();
		ConnectDB($db);	
		
		$query = "SELECT real_url AS `url`
				  FROM shorty
				  WHERE short_code = '$shortCode' ";
		
		$result = $db->ExecuteQuery($query);

		if($result)
		{
			$code = mysqli_fetch_assoc($result);			
		}
		else
			return 0;
		
		$db->CloseDB();		
		return $code['url'];
	}
	
	
	// function to validate url
	function validateURL($url)
	{
		$pattern = '/^(([\w]+:)?\/\/)?(([\d\w]|%[a-fA-f\d]{2,2})+(:([\d\w]|%[a-fA-f\d]{2,2})+)?@)?([\d\w][-\d\w]{0,253}[\d\w]\.)+[\w]{2,4}(:[\d]+)?(\/([-+_~.\d\w]|%[a-fA-f\d]{2,2})*)*(\?(&amp;?([-+_~.\d\w]|%[a-fA-f\d]{2,2})=?)*)?(#([-+_~.\d\w]|%[a-fA-f\d]{2,2})*)?$/';
		return preg_match($pattern, $url);
	}
?>