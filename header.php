<?php 
include 'Includes/Config/Config.php';

if(isset($_GET))
{	
	if(!empty($_GET))
	{
		include 'Includes/Functions/FrontFunctions.php';
	
		$shortCode = htmlentities(trim(key($_GET)));	
		$url = redirectToWebsite($shortCode);
	
		if(count($url) > 0)
		{		
			header("location: ".$url);
			exit;
		}
		else 
		{
			header("location: 404.html");
			exit;
		}
	}
}

?>

<html>
<head>

<script type="text/javascript" src="Common/Javascript/jquery-1.6.2.min.js"></script>
<script type="text/javascript" src="Common/Javascript/myScript.js"></script>

<title><?php echo $websiteName; ?></title>

</head>

<body>

