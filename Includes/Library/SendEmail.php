<?php
// Email functionality utility

class SendEmail
{
	var $From 		= NULL;
	var $To 		= NULL;
	var $Body 		= NULL;
	var $Subject 	= NULL;
	var $Headers 	= NULL;
	
	function SendEmail($from,$to,$body,$subject)	
	{
		$this->To = $to;
		$this->From = $from;
		$this->Body = $body;
		$this->Subject = $subject;
	}
	
	function addHeader($header)
	{
       $this->Headers .= $header;
    }
	
	
	/*function Send()
	{
		 $this->addHeader('From: '.$this->From."\r\n");
         $this->addHeader('Reply-To: '.$this->From."\r\n");
         $this->addHeader('Return-Path: '.$this->From."\r\n");
		 //$this->addHeader('bcc: '."s.zafar@ovrlod.com\r\n");
		 $this->addHeader("MIME-Version: 1.0 \r\n");
         $this->addHeader("Content-Type: text/html; charset=ISO-8859-1 \r\n");
		 $this->addHeader("Content-Transfer-Encoding: 8bit\r\n");
		 $this->addHeader("X-Mailer: PHP4 \r\n");        
		 
		try
		{
         	mail($this->To,$this->Subject,$this->Body,$this->Headers);	
		}
		catch(Exception $e)
		{
			error_log($e->getMessage(),3,"error.log");
		}
	}*/
    
	function Send()
	{
		 $this->addHeader('From: '.$this->From."\r\n");
         $this->addHeader('Reply-To: '.$this->From."\r\n");
         $this->addHeader('Return-Path: '.$this->From."\r\n");
		 //$this->addHeader('bcc: '."s.zafar@ovrlod.com\r\n");
		 $this->addHeader("MIME-Version: 1.0 \r\n");
         $this->addHeader("Content-Type: text/html; charset=UTF-8 \r\n");
		 $this->addHeader("Content-Transfer-Encoding: 8bit\r\n");
		 $this->addHeader("X-Mailer: PHP4 \r\n");         
		 
		try
		{
         	mail($this->To,"=?UTF-8?B?".base64_encode($this->Subject)."?=",$this->Body,$this->Headers);	
		}
		catch(Exception $e)
		{
			error_log($e->getMessage(),3,"error.log");
		}
	}
}

?>