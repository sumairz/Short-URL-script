<?php

/* Database settings */
if($_SERVER['HTTP_HOST'] == "localhost")
{
	/* Staging Settings */
	$DatabaseHost = "localhost";
	$DatabaseName = 'short_url';
	$DatabaseUser = 'root';
	$DatabasePsw  = '';
	
	/* Website url */
	$URL = "http://localhost/shorturl/";
}	
else 
{	
	/* Production Settings */
	$DatabaseUser = '';
	$DatabasePsw  = '';
	$DatabaseHost = "";
	$DatabaseName = '';
	
	/* Website url */
	$URL = "";
}


/* General Settigns */
$websiteName = "Short Url";

/* ------------------ Messages -------------------- */
$invalidUrl = "Url seems to be invalid";
$emptyUrl = "Give us a URL to make it short";
