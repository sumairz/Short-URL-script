<?php 

include 'Includes/Functions/FrontFunctions.php';
	
	//$starttime = pageLoadTime();

	$url = trim(htmlentities($_POST['url']));
	
	if(!empty($url))
	{	
		if(validateURL($url))
		{
			echo $URL."?".saveUrl($url);
		}
		else
		{
			echo $invalidUrl;
		}
	}
	else
		echo $emptyUrl;		
	
	/*
	echo "<br><br>";
	$endtime = pageLoadTime();
	
	$totaltime = $endtime - $starttime;  
	printf('Page loaded in %.5f seconds.',  $totaltime); 
	*/

?>