<?php
// Admin Functions
/* Files Includes */
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

	/* User Authentication */
	function ChkUserAuth()
	{
		$qt = new login();
		$qt->checkUser();	
	}
	
	//check from email link to see user details
	
	/* Login Credentials */
	function LoginDietBlog($user,$psw)
	{
		$qt = new login();
		$error = $qt->loginUser($user,$psw);
		
		if($error == '')
		{
			header('Location: Home.php');
			exit();		
		}
	}
		
	/*Logout from Page*/
	function LogoutDietBlog()
	{
		$qt = new login();
		$qt->logoutUser();
		header('Location: Login.php');
		exit();	
		
	}
	
	function GetTotalusers()
	{
		$db = new Database();
		ConnectDB($db);
		$sql = "SELECT COUNT(id) AS total_count FROM user
		 where isActive=1 ORDER BY dateCreated DESC";
		$result = $db->ExecuteQuery($sql);
		 
		 if($result)
		 {
		   $arr = mysqli_fetch_array($result);
		 }
		 else
		 {
		 	return false;		 	
		 }
		 		 
		 $db->CloseDB();
		 return $arr[0];
	}
		
	function GetTotalNumbersOfVoters()
	{
		$db = new Database();
		ConnectDB($db);
		$sql = "SELECT COUNT(id) AS total_count FROM user
				where isActive = 1 AND isApproved = 1";
		$result = $db->ExecuteQuery($sql);
		
		if($result)
		{
		   $arr = mysqli_fetch_array($result);
		}
		else
		{
			return false;		 	
		}
		 		 
		 $db->CloseDB();
		 return $arr[0];
	}
	
	
	//Get total no of votes of a category
	function getTotalNoOfVotes($category,$locale)
	{
		/*
		 * sort can be likes,videoViews,latest
		 * category can be poetry,rap
		 * name can be any string
		 */
		global $locale;
		$db = new Database();
		ConnectDB($db);
		$db->ExecuteQuery('SET CHARACTER SET utf8');
		$db->ExecuteQuery('SET NAMES utf8');
		$arr = array();
		
		$query = "SELECT DISTINCT(video.id),  SUM((SELECT COUNT(id) FROM video_rating WHERE video.id = video_rating.videoId AND isLiked = 1)) AS `totalvotes`
					FROM `user`,category,video
					WHERE video.userId = user.id
					AND video.categoryId = category.id
					AND video.isActive = 1
					AND video.isApproved = 1
					AND `user`.isActive = 1
					AND `user`.isApproved = 1 "; 
					
		if($category != "")
		{
			$query .= " AND category.name = '$category' ";
		}
		
		$query;	
		$result = $db->ExecuteQuery($query);
		  
		if(!$result)
		{
			$db->CloseDB();
			//return false;
			return false;   
		}
		else
		{
			$arr = mysqli_fetch_assoc($result);
		    
		}
  		
   		$db->CloseDB();
   		return $arr['totalvotes'];
	}
	
	
	function getTotalNoOfViews($category)
	{
		/*
		 * sort can be likes,videoViews,latest
		 * category can be poetry,rap
		 * name can be any string
		 */
		global $locale;
		$db = new Database();
		ConnectDB($db);
		$db->ExecuteQuery('SET CHARACTER SET utf8');
		$db->ExecuteQuery('SET NAMES utf8');
		$arr = array();
		
		$query = "SELECT DISTINCT(video.id),  SUM((SELECT COUNT(id) FROM video_views WHERE video.id = video_views.videoId)) AS `totalviews`
					FROM `user`,category,video
					WHERE video.userId = user.id
					AND video.categoryId = category.id
					AND video.isActive = 1
					AND video.isApproved = 1
					AND `user`.isActive = 1
					AND `user`.isApproved = 1 "; 
					
		if($category != "")
		{
			$query .= " AND category.name = '$category' ";
		}
		
		$query;	
		$result = $db->ExecuteQuery($query);
		  
		if(!$result)
		{
			$db->CloseDB();
			//return false;
			return false;   
		}
		else
		{
			$arr = mysqli_fetch_assoc($result);
		    
		}
  		
   		$db->CloseDB();
   		return $arr['totalviews'];
	}
	
	function GetAllusers($Start,$Limit)
	{
		$db = new Database();
		ConnectDB($db);
		$db->ExecuteQuery('SET CHARACTER SET utf8');
		$db->ExecuteQuery('SET NAMES utf8');
		
		$sql = "SELECT * FROM user
				where isActive=1
				ORDER BY dateCreated DESC
				LIMIT $Start , $Limit";
		$result = $db->ExecuteQuery($sql);
		if($result->num_rows>0)
		{
		  while ($row = mysqli_fetch_assoc($result))
		  {
			 $arr[]= $row; 
		  }
		 
		  	return $arr;
		}
		else
		{
			return false;		 	
		}
		 		 
		$db->CloseDB();
	}
	
	
	// fetching user information by user id
	// this function is also used by getVideoByComment()
	function GetUser($user_id)
	{
		$db = new Database();
		ConnectDB($db);
		$db->ExecuteQuery('SET CHARACTER SET utf8');
		$db->ExecuteQuery('SET NAMES utf8');
		
		$sql = "SELECT * FROM user 
		WHERE isActive=1 and id=$user_id;";
		$result = $db->ExecuteQuery($sql);
		$arr = array();
		 if($result)
		 {
		  while ($row = mysqli_fetch_assoc($result))
		  {
		  	$arr[]= $row; 
		  }
		 }
		 else
		 {
		 	return 0;		 	
		 }
		 		 
		 $db->CloseDB();
		 return $arr;
	
	}
	
	
	function deleteuser($user_id)
	{
		try
		{
		  $db = new Database();
		  ConnectDB($db);
		  
		  // deleting videos from youtube using youtube video id saved in database of specific user
		  // getting videos of user using user ID
		  $sql = "SELECT id from video WHERE isActive=1 AND userId=$user_id;";
		  $result = $db->ExecuteQuery($sql);				 
		  $arr = mysqli_fetch_assoc($result);
		  				   
		  if(!empty($arr))
		  {
			  try
			  {
					deleteVideoYoutube($arr['id']); // function to delete videos from youtube
			  }
			  catch(Exception $err)
			  {
				  error_log($err->getMessage()."\n\r",3,"error.log");
			  }
		  }
				   
		  // 
		  $sql = "update user set isActive=0 where id=$user_id";
		  
		  if($db->ExecuteQuery($sql))
		   {
			   $sql = "update video set isActive=0 AND isApproved=0 where userId=$user_id";
			   if($db->ExecuteQuery($sql))
			   {				 
				   return true;
			   }
			   else
				  return false;
		   }
		   else
		   {
			   return false;		 	
		   }
		   
		   $db->CloseDB();
		}
		catch(Exception $err)
		{
			error_log($err->getMessage()."\n\r",3,"error.log");
		}
	}
	
	
	//deleting videos from youtube and from database
	function deletevideo($video_id)
	{
		$db = new Database();
		ConnectDB($db);
		$sql = "update video set isActive=0 where id=$video_id";
		
		 if($db->ExecuteQuery($sql))
		 {
			 try
			 {
            	deleteVideoYoutube($video_id);
			 }
			 catch(Exception $err)
			 {
				 error_log($err->getMessage()."\n\r",3,"error.log");
			 }
			 
			return true;
		 }
		 else
		 {
			 return false;		 	
		 }
		 
		 $db->CloseDB();
	}
	
	
	//function to delete videos from youtube using youtube video id from database
	function deleteVideoYoutube($video_id)
	{
		global $yt_user,$yt_pw,$yt_source,$yt_api_key;

		$db = new Database();
		ConnectDB($db);
		$sql = "SELECT url from video WHERE id=$video_id;";
		
		$result = $db->ExecuteQuery($sql);

		 if($result)
		 {
			$arr = mysqli_fetch_assoc($result);
			
			try
		  	{
				$authenticationURL= 'https://www.google.com/accounts/ClientLogin';
				
				$httpClient = Zend_Gdata_ClientLogin::getHttpClient(  
												  $username = $yt_user,  
												  $password = $yt_pw,  
												  $service = 'youtube',  
												  $client = null,  
												  $source = $yt_source, // a short string identifying your application  
												  $loginToken = null,  
												  $loginCaptcha = null,  
												  $authenticationURL);  
		   
			   $yt = new Zend_Gdata_YouTube($httpClient,'23', '234',$yt_api_key);
			   
			  //deleting a specific video from youtube	
			  $videoEntryToDelete = $yt->getVideoEntry($arr['url'], null, true);
			  $yt->delete($videoEntryToDelete);
		  }
		  catch(Excception $err)
		  {
			  error_log($err->getMessage()."\n\r",3,"error.log");
		  }
	 
			return true;
		 }
		 else
		 {
			 return false;		 	
		 }
		 
		 $db->CloseDB();
	}
	
	
	function UnAppOrApproveVideo($videoid,$type)
	{
		$db = new Database();
		ConnectDB($db);
		if($type==1)
			$sql = "update video set isApproved=$type where id=$videoid";
		else
			$sql = "update video set isApproved=$type where id=$videoid";
		if($db->ExecuteQuery($sql))
		 {
			return true;
		 }
		 else
		 {
			 return false;		 	
		 }
		 
		 $db->CloseDB();
	}
	
	
	/*
	function GetTotalVideos()
	{
		$db = new Database();
		ConnectDB($db);
		$sql = "SELECT COUNT(id) AS total_count FROM video
		 where isActive=1 ORDER BY dateCreated DESC";
		 
		$result = $db->ExecuteQuery($sql);
		 
		 if($result)
		 {
		   $arr = mysqli_fetch_array($result);
		 }
		 else
		 {
		 	return false;		 	
		 }
		 		 
		 $db->CloseDB();
		 return $arr[0];
	
	}*/
	function GetTotalVideos($name,$sort,$category)
	{
		/*
		  * sort can be likes,videoViews,latest
		  * category can be poetry,rap
		  * name can be any string
		*/

	$db = new Database();
	ConnectDB($db);
	$db->ExecuteQuery('SET CHARACTER SET utf8');
	$db->ExecuteQuery('SET NAMES utf8');
		
	$arr = array();
	
	$query = "SELECT count(DISTINCT(video.id)) as `count`,video.name AS `video`,url,video.description,video.isApproved,video.topic as `topic`,CONCAT(user.firstName,' ',user.lastName) AS `userName`,
	firstName,lastName,email,
	duration,bigImage,smallImage,category.name AS `category`,video.UUID,video.userId,
	(SELECT COUNT(video_views.id) FROM video_views WHERE video.id = video_views.videoId GROUP BY video_views.videoId) AS `videoViews`,
	(SELECT count(id) FROM video_rating WHERE video.id = video_rating.videoId AND isLiked = 1) AS `likes`,
	(SELECT count(id) FROM video_rating WHERE video.id = video_rating.videoId AND isLiked = 0) AS `disLikes`
	FROM `user`,category,video
	WHERE video.userId = user.id
	AND `user`.isActive = 1
	AND `user`.isApproved = 1
	AND video.categoryId = category.id
	AND video.isActive = 1 ";
	if($category != "")
	{
		$query .= " AND category.name = '$category' ";
	}
	
	
	if($name != "")
	{
		$query .= " AND video.name like '%$name%' ";
	}
	
	if($sort != "")
	{
	  if($sort == "latest")
	  {
	  	$query .= " ORDER BY video.dateCreated DESC ";
	  }
	  else
	  {
	  	$query .= " ORDER BY $sort DESC ";
	  }
	}
	else
	{
		$query .= " ORDER BY likes DESC ";
	}
	
	$query;


	$result = $db->ExecuteQuery($query);
	
	if(!$result)
	{
	$db->CloseDB();
	return false;
	}
	else
	{
		$arr = mysqli_fetch_array($result);
	}
	
	$db->CloseDB();
	return $arr;
	}
	
	
	
	function GetAllVideos($Start,$Limit)
	{
		$db = new Database();
		ConnectDB($db);
		
		$db->ExecuteQuery('SET CHARACTER SET utf8');
		$db->ExecuteQuery('SET NAMES utf8');
		
		$sql = "SELECT video.id, name, url, video.description, userId, categoryId, isReceivedVotesEmail, video.dateCreated, isShortListed, duration, bigImage, smallImage, video.isApproved, video.isActive,likeCount, uploadedPhase, video.dateModified,p.firstName,p.lastName,p.id as userid,p.email FROM video ,user p where userId=p.id and video.isActive=1 ORDER BY video.dateCreated DESC LIMIT $Start , $Limit";
		
		$result = $db->ExecuteQuery($sql);

		if($result->num_rows>0)
		 {
		  while ($row = mysqli_fetch_assoc($result))
		  {
			 $arr[]= $row; 
		  }
		 
		  	return $arr;
		 }
		 else
		 {
		 	return false;		 	
		 }
		 		 
		 $db->CloseDB();
	}
	
	
	function sendmail($from,$to,$body,$Subject)
	{
		try
		{
			$email=new SendEmail($from,$to,$body,$Subject);
		}
		catch(Exception $err)
		{
			error_log($err->getMessage()."\n\r",3,"error.log");
		}
		
		if($response = $email->Send())
		{
			  echo "Email Sent";  
		}
		else
		{
			  echo  $response; 
		}	
	}
	
	function getVideos($name,$sort,$category,$start,$limit)
	{
		/*
		  * sort can be likes,videoViews,latest
		  * category can be poetry,rap
		  * name can be any string
		*/

	$db = new Database();
	ConnectDB($db);
	
	$db->ExecuteQuery('SET CHARACTER SET utf8');
	$db->ExecuteQuery('SET NAMES utf8');
	
	$arr = array();
	
	$query = "SELECT DISTINCT(video.id),video.name AS `video`,url,video.description,video.isApproved,video.topic as `topic`,CONCAT(user.firstName,' ',user.lastName) AS `userName`,
	firstName,lastName,email,
	duration,bigImage,smallImage,category.name AS `category`,video.UUID,video.userId,
	(SELECT COUNT(video_views.id) FROM video_views WHERE video.id = video_views.videoId GROUP BY video_views.videoId) AS `videoViews`,
	(SELECT count(id) FROM video_rating WHERE video.id = video_rating.videoId AND isLiked = 1) AS `likes`,
	(SELECT count(id) FROM video_rating WHERE video.id = video_rating.videoId AND isLiked = 0) AS `disLikes`
	FROM `user`,category,video
	WHERE video.userId = user.id
	AND `user`.isActive = 1
	AND `user`.isApproved = 1
	AND video.categoryId = category.id
	AND video.isActive = 1 ";
	if($category != "")
	{
		$query .= " AND category.name = '$category' ";
	}
	
	
	if($name != "")
	{
		$query .= " AND video.name like '%$name%' ";
	}
	
	if($sort != "")
	{
	  if($sort == "latest")
	  {
	  	$query .= " ORDER BY video.dateCreated DESC ";
	  }
	  else
	  {
	  	$query .= " ORDER BY $sort DESC ";
	  }
	}
	else
	{
		$query .= " ORDER BY likes DESC ";
	}
	if($start >= 0 && $limit > 0)
	{
		$query .= "LIMIT $start,$limit";
	}

	$result = $db->ExecuteQuery($query);
	
	if(!$result)
	{
	$db->CloseDB();
	return false;
	}
	else
	{
	while($row = mysqli_fetch_assoc($result))
	{
	$arr[] = $row;
	}
	}
	
	$db->CloseDB();
	return $arr;
	}

	// get videos for jusdges
	function getVideosForJudge($name,$sort,$category,$start,$limit)
	{
		/*
		  * sort can be likes,videoViews,latest
		  * category can be poetry,rap
		  * name can be any string
		*/

	$db = new Database();
	ConnectDB($db);
	
	$db->ExecuteQuery('SET CHARACTER SET utf8');
	$db->ExecuteQuery('SET NAMES utf8');
	
	$arr = array();
	
	$query = "SELECT DISTINCT(video.id),video.name AS `video`,url,video.description,video.isApproved,video.topic as `topic`,CONCAT(user.firstName,' ',user.lastName) AS `userName`,
	firstName,lastName,email,
	duration,bigImage,smallImage,category.name AS `category`,video.UUID,video.userId,
	(SELECT COUNT(video_views.id) FROM video_views WHERE video.id = video_views.videoId GROUP BY video_views.videoId) AS `videoViews`,
	(SELECT count(id) FROM video_rating WHERE video.id = video_rating.videoId AND isLiked = 1) AS `likes`,
	(SELECT count(id) FROM video_rating WHERE video.id = video_rating.videoId AND isLiked = 0) AS `disLikes`
	FROM `user`,category,video
	WHERE video.userId = user.id
	AND `user`.isActive = 1
	AND `user`.isApproved = 1
	AND video.categoryId = category.id
	AND video.isActive = 1 
	AND video.isApproved = 1";
	if($category != "")
	{
		$query .= " AND category.name = '$category' ";
	}
	
	
	if($name != "")
	{
		$query .= " AND video.name like '%$name%' ";
	}
	
	if($sort != "")
	{
	  if($sort == "latest")
	  {
	  	$query .= " ORDER BY video.dateCreated DESC ";
	  }
	  else
	  {
	  	$query .= " ORDER BY $sort DESC ";
	  }
	}
	else
	{
		$query .= " ORDER BY likes DESC ";
	}
	if($start >= 0 && $limit > 0)
	{
		$query .= "LIMIT $start,$limit";
	}

	$result = $db->ExecuteQuery($query);
	
	if(!$result)
	{
	$db->CloseDB();
	return false;
	}
	else
	{
	while($row = mysqli_fetch_assoc($result))
	{
	$arr[] = $row;
	}
	}
	
	$db->CloseDB();
	return $arr;
	}
	
	

// *************************************************** Sumair Function **********************************************************

	function PrintArray($arr)
	{
		echo "<pre>";
		print_r($arr);
		echo "</pre>";
		
		return $arr;		
	}
	
	//Send Email function call the SendMail Class
	function SendEmail($From,$To,$Content,$Subject)
	{
		try
		{				
			$email=new SendEmail($From,$To,$Content,$Subject);
			$email->Send();
		}
		catch (Exception $err)
		{
			error_log($err->getMessage()."\n\r",3,"error.log");
		}
	}

	// get video details from youtube public video xml
	function GetVideoIds()
	{
	  $db = new Database();
	  ConnectDB($db);
	  $arr = array();
	      
	  $query = "SELECT id,url FROM video 
				WHERE duration is NULL
				OR duration = 0
				OR bigImage is NULL
				OR smallImage is NULL
				AND isActive = 1;";
	  
	  $result = $db->ExecuteQuery($query);
	  if($result)
	  {		 
		 while($data = mysqli_fetch_assoc($result))
		 {
			$arr[] = $data;
		 } 
	  }
	  else
	  {
		  return 0; 
	  }
	  
	   
	   $db->CloseDB();
	   return $arr;
	}
	
	
	// get video details from youtube public video xml
	function UpdateVideoInfo($id,$vinfo)
	{
	  $db = new Database();
	  ConnectDB($db);
	  $arr = array();
	      
	  $query = "UPDATE video SET duration='".$vinfo['length']."', bigImage='".$vinfo['BigImgURL']."', smallImage='".$vinfo['thumbnail']."' WHERE id=$id;";

	  $result = $db->ExecuteQuery($query);

	  if($result)
	  {		 
		 return 1;
	  }
	  else
	  {
		  return 0; 
	  }
	   $db->CloseDB();
	}
	
	// function to parse a video <entry>  
	function parseVideoEntry($entry) { 
	 
	 try
	 {
		$obj= new stdClass;  
		  
		// get nodes in media: namespace for media information  
		$media = $entry->children('http://search.yahoo.com/mrss/'); 	 
		
		// get video Big image
		$attrs = $media->group->thumbnail[0]->attributes();  
		$obj->BigImgURL = $attrs['url'];  
		  
		// get video thumbnail  
		$attrs = $media->group->thumbnail[2]->attributes();  
		$obj->thumbnailURL = $attrs['url'];  	
		  
		// get <yt:duration> node for video length  
		$yt = $media->children('http://gdata.youtube.com/schemas/2007');  
		$attrs = $yt->duration->attributes();  
		$obj->length = $attrs['seconds']; 
	  
		if(!empty($obj))
		{
		  // return object to caller  
		  return $obj;  
		}
		else
		  return false;
	   }
	   catch(Exception $err)
	   {
		   error_log($err->getMessage()."\n\r",3,"error.log");
	   }
	}  

	// Fetching youtube video duration/big and small images
	function SaveVideoInfo($id,$vid) 
	{		
		$vid = stripslashes("http://www.youtube.com/watch?v=$vid");  
		$string = $vid;  
		$url = parse_url($string);  
		parse_str($url['query']);  
		  
		// set video data feed URL 
		$feedURL = 'http://gdata.youtube.com/feeds/api/videos/'. $v;  
		
		// read feed into SimpleXML object  
		$entry = simplexml_load_file($feedURL);  
		
		if($entry)
		{
		  // parse video entry  
		  $video = parseVideoEntry($entry);
		}
		else
		{
			return false;
		}
		
		if(!empty($video))  
		{
		  //These variables include the video information  
		  $video_info['BigImgURL'] = stripslashes($video->BigImgURL); 
		  $video_info['thumbnail'] = stripslashes($video->thumbnailURL); 
		  $video_info['length'] = (stripslashes($video->length)); 

	      $a = UpdateVideoInfo($id,$video_info);
		  return true;	
		}
		else
			return false;
	}
	
	
	// Cron job funciton to send user vote count on there videos daily
	function SendDailyEmail() 
	{
		
      global $admin_email,$URL; 		
	  $db = new Database();
	  ConnectDB($db);
	  $db->ExecuteQuery('SET CHARACTER SET utf8');
	  $db->ExecuteQuery('SET NAMES utf8');
	  $arr = array();
	      
	  $query = "Select vid.id as videoId, vid.userId, vid.name as vname,vid.`local`,vid.dateCreated,vid.UUID,
	  (SELECT COUNT(isLiked) from video_rating WHERE isLiked=1 AND videoId = vid.id) liked,
	  (SELECT email from `user` WHERE id=vid.userId) email,
	  (SELECT firstName FROM `user` WHERE id=vid.userId) name from video as vid WHERE vid.isReceivedVotesEmail =1 AND vid.isApproved = 1 AND vid.isActive = 1;";
	  
	  $result = $db->ExecuteQuery($query);
 	  
	  if($result)
	  {
		 while($data = mysqli_fetch_assoc($result))
		 {
			$arr[] = $data;
		 }
		
		 $From = $admin_email;
		 
		 foreach($arr as $user)
		 {
			$To = $user['email'];
			
			$dateOfUpload = explode(" ", $user['dateCreated']);
			
			if($user['local'] = "en")
			{				
				$Subject = "Red Bull - Competition daily report";			
				$Content = "Hello ".$user['name']."
						<br /><br />
						Here goes your daily Red Bull Ghazwat Al Lisan report:
						<br /><br />
						The video you submitted on ".$dateOfUpload[0]." has collected so far:
						<br /><br />
						".$user['liked']." votes
						<br /> <br />
						It can be viewed on the following link:
						<br /><br />
						<a href='".$URL.$user['local']."/chooseside.php?video=".$user['UUID']."' target='_blank'>".$URL.$user['local']."/chooseside.php?video=".$user['UUID']."</a>
						<br /><br />
						Share this link with your friends to get the highest number of votes and get closer to winning Red Bull Ghazwat Al Lisan competition.
						<br /><br />
						Good luck!
						<br /><br />
						Red Bull Team";
			}
			else if($user['local'] == "ar")
			{
				$Subject = "ريد بُل – التقرير اليومي";
								
				$Content = '<table cellpadding="0" cellspacing="0" style="width: 100%; direction:rtl;">
							<tr>
							<td>مرحباً "'.$user["name"].'"
						<br /><br />
						إليك بآخر المستجدات المتعلقة بمسابقة ريد بُل – غزوة اللسان:
						<br /><br />
						الفيديو الخاص بك الذي أتممت تحميله في ".$dateOfUpload[0]." حصد حتى الآن:
						<br /><br />
						"'.$user["liked"].'" صوتاً
						<br /> <br />
						يمكنك مشاهدة الفيديو الخاص بك على الرابط التالي :
						<br /><br />
						<a href="'.$URL.$user["local"].'"/chooseside.php?video="'.$user['UUID'].'" target="_blank">"'.$URL.$user["local"].'"/chooseside.php?video="'.$user["UUID"].'"</a>
						<br /><br />
						أرسل هذا الرابط إلى أصدقائك. دعمهم وأصواتهم سبيلك للفوز باللقب!
						<br /><br />
						حظاً سعيداً!
						<br /><br />
						فريق عمل ريد بُل						
							</td>
							</tr>
							</table>';
				
			}			
			
			try
			{			
		 		$email_status = SendEmail($From,$To,$Content,$Subject);
			}
			catch(Exception $err)
			{
				error_log($err->getMessage(),3,"error.log");
			}
			
		 }
	  }
	  else
	  {
		  return false; 
	  }	   
	   $db->CloseDB();
	   return true;   
	}
	
	
	//Convert seconds to minutes
	function Sec2Min($s) 
	{
	  $str = "";
	  $d = intval($s/86400);
	  $s -= $d*86400;
	  
	  $h = intval($s/3600);
	  $s -= $h*3600;
	  
	  $m = intval($s/60);
	  $s -= $m*60;
	  
	  if ($d) $str = $d . 'd ';
	  if ($h) $str .= $h . 'h ';
	  if ($m) $str .= $m . 'min ';
	  if ($s) $str .= $s . 'sec';
	  
	  return $str;
	}
	
	// Writing home page message in a text file
	function WriteFile($message)
	{
		global $messageFileName;
		
		try
		{
		  $fp = fopen($messageFileName, 'w');
		  fwrite($fp, $message);
		  fclose($fp);
		  return 1; // true
		}
		catch(Exception $err)
		{
			// if text file is not found write error log
			error_log($err->getMessage()." date: ". date() ."\n",3,"error.log");
			return 0; // false
		}
	}
	
	// function to read home page message text file
	function ReadMessageFile()
	{
		global $messageFileName;
		
		try
		{
			if(filesize($messageFileName) > 0)
			{
			  $handle = fopen($messageFileName, "r");
			  $contents = fread($handle, filesize($messageFileName));
			  fclose($handle);
			  return $contents;
			}
			else
				return 0;
		}
		catch(Exception $err)
		{
			// if text file is not found write error log
			error_log($err->getMessage()." date: ". date() ."\n",3,"error.log");
			return 0; // false
		}
	}
	
	// Set status for the activation/deactivation of video uploading
	//Operation can be videouploading and voting
	//Data save in file as videouploading[0 or 1] | voting [0 or 1] 0 for stop and 1 for start 
	function activateDeactivateVideoUploading($operation,$status)
	{
		global $activationDeactivationFileName;
		
		try
		{
		  
		  $fp = fopen($activationDeactivationFileName, 'r');
		  $contents = fgets($fp);
		  fclose($fp);
		  $fp = fopen($activationDeactivationFileName, 'w');
		  //$contents = fgets($fp,8);
		  if($operation == "videouploading")
		  {
		  	$arr = explode("|",$contents);
		  	$data = $status."|".$arr[1];
		  }
		  else if($operation == "voting")
		  {
		  	$arr = explode("|",$contents);
		  	$data = $arr[0]."|".$status;
		  }
		  else 
		  {
		  	fclose($fp);
		  	return false;
		  }
		  fwrite($fp, $data);
		  fclose($fp);
		  return 1; // true
		}
		catch(Exception $err)
		{
			// if text file is not found write error log
			error_log($err->getMessage()." date: ". date() ."\n",3,"error.log");
			return 0; // false
		}
	}
	
	// Read the status of the activation/deactivation of the video uploading
	//Operation can be videouploading and voting
	function readActivateDeactivateVideoUploading($operation)
	{
		global $activationDeactivationFileName;
		
		try
		{
		  
		  $fp = fopen($activationDeactivationFileName, 'r');
		  $contents = fgets($fp);
		  if($operation == "videouploading")
		  {
		  	$arr = explode("|",$contents);
		  	fclose($fp);
		  	return $arr[0];
		  }
		  else if($operation == "voting")
		  {
		  	$arr = explode("|",$contents);
		  	fclose($fp);
		  	return $arr[1];
		  }
		  else 
		  {
		  	fclose($fp);
		  	return false;
		  }
		  
		  fclose($fp);
		  return 1; // true
		}
		catch(Exception $err)
		{
			// if text file is not found write error log
			error_log($err->getMessage()." date: ". date() ."\n",3,"error.log");
			return 0; // false
		}
	}
		
		
	// function to fetch judge comments on a spcific video
	function insertJudgeVideoComments($comment,$videoId,$adminId)
	{
	
		$db = new Database();
		
		ConnectDB($db);
		$db->ExecuteQuery('SET CHARACTER SET utf8');
		$db->ExecuteQuery('SET NAMES utf8');
		
		$sql = "SELECT local FROM video WHERE id=".$videoId."";
		$result = $db->ExecuteQuery($sql);
		$data = mysqli_fetch_assoc($result);
		$videoLocale = $data['local'];
		
		if(isset($videoLocale))
		{
			$sql = "CALL add_edit_comments(0,".$adminId.",".$videoId.",'".$comment."',1,'".$videoLocale."');";
			$result = $db->ExecuteQuery($sql);
		}
	
		 if($result)
		 {
		   	return 1;
		 }
		 else
		 {
			return 0;		 	
		 }
		 $db->CloseDB();	
	}
	
	// function to fetch judge comments on a spcific video
	function getJudgeVideoComments($adminId,$videoId,$Start,$Limit)
	{
		global $BackendUsers;
		
		$arr = array();
		$db = new Database();
		
		ConnectDB($db);
		$db->ExecuteQuery('SET CHARACTER SET utf8');
		$db->ExecuteQuery('SET NAMES utf8');
		
		foreach($BackendUsers as $rec)
		{
			if($rec['type'] == "admin")
			{
				$bckndUserAdmin = $rec['id'];
				break;
			}				
		}
		
		if($adminId == $bckndUserAdmin)
		{
			$sql = "SELECT id,userId,comments,dateCreated from video_comment WHERE videoId = $videoId AND isAdminComment = 1 AND isActive = 1 
				ORDER BY dateCreated DESC
				LIMIT $Start , $Limit;";
		}
		else
		{
			$sql = "SELECT id,userId,comments,dateCreated from video_comment WHERE videoId = $videoId AND isAdminComment = 1 AND isActive = 1 AND userId = $adminId 
				ORDER BY dateCreated DESC
				LIMIT $Start , $Limit;";
		}
		
		$result = $db->ExecuteQuery($sql);

		 if($result)
		 {
		   	while($data = mysqli_fetch_assoc($result))
		 	{
				$arr[] = $data;
		 	} 
		 }
		 else
		 {
			return 0;		 	
		 }
				 
		 $db->CloseDB();	
		 return $arr;
	}
	
	// function to fetch judge comments on a spcific video
	function getTotalJudgeVideoComments($adminId,$videoId)
	{
		global $BackendUsers;
		
		$db = new Database();
		ConnectDB($db);
		
		foreach($BackendUsers as $rec)
		{
			if($rec['type'] == "admin")
			{
				$bckndUserAdmin = $rec['id'];
				break;
			}				
		}
		
		if($adminId == $bckndUserAdmin)
		{
			$sql = "SELECT COUNT(id) AS total_count FROM video_comment
		 	WHERE videoId = $videoId AND isAdminComment = 1 AND isActive = 1;";
		}
		else
		{
			$sql = "SELECT COUNT(id) AS total_count FROM video_comment
		 	WHERE videoId = $videoId AND isAdminComment = 1 AND isActive = 1 AND userId=$adminId;";
		}
		$result = $db->ExecuteQuery($sql);
		 
		 if($result)
		 {
		   $arr = mysqli_fetch_array($result);
		 }
		 else
		 {
		 	return false;		 	
		 }
		 		 
		 $db->CloseDB();
		 return $arr[0];
	}
	
	// function to check that judge can not see other pages
	function CheckJudge($userType)
	{
		if($userType != "admin")
		{
			header("Location:Home.php");
			exit;
		}		
	}
	
	// function to fetch specific video information
	// this function is also used by getVideoByComment()
	function getVideoById($videoId)
	{
		$db = new Database();
	ConnectDB($db);
	
	$db->ExecuteQuery('SET CHARACTER SET utf8');
	$db->ExecuteQuery('SET NAMES utf8');
	
	$arr = array();
	
	
	$query = "SELECT DISTINCT(video.id),video.name AS `video`,video.dateCreated,url,video.description,video.isApproved,video.topic as `topic`,CONCAT(user.firstName,' ',user.lastName) AS `userName`,
	firstName,lastName,email,
	duration,bigImage,smallImage,category.name AS `category`,video.UUID,video.local,video.userId,
	(SELECT COUNT(video_views.id) FROM video_views WHERE video.id = video_views.videoId GROUP BY video_views.videoId) AS `videoViews`,
	(SELECT count(id) FROM video_rating WHERE video.id = video_rating.videoId AND isLiked = 1) AS `likes`,
	(SELECT count(id) FROM video_rating WHERE video.id = video_rating.videoId AND isLiked = 0) AS `disLikes`
	FROM `user`,category,video
	WHERE video.userId = user.id
	AND video.id = $videoId
	AND `user`.isActive = 1
	AND `user`.isApproved = 1
	AND video.categoryId = category.id
	AND video.isActive = 1;";


	$result = $db->ExecuteQuery($query);
	
	if(!$result)
	{
	$db->CloseDB();
	return false;
	}
	else
	{
	while($row = mysqli_fetch_assoc($result))
	{
	$arr[] = $row;
	}
	}
	
	$db->CloseDB();
	return $arr;
	}
	
	
	// this function will take comment id as input and get video info and user info 
	function getVideoByComment($commentId)
	{
		try
			{
			  $db = new Database();
			  ConnectDB($db);		  
		  
			  $sql = "SELECT videoId,userId,locale FROM video_comment
		 			  WHERE id = $commentId;";
		  
			  $result = $db->ExecuteQuery($sql);
		 
		 	  if($result)
		 	  {
		   		$arr = mysqli_fetch_array($result);
		   		
		   		$video = getVideoById($arr['videoId']); // getting video information
		   		$userEmail = GetUser($arr['userId']); // getting user email address
		   		
		   		$video[0]['commentLocale'] = $arr['locale']; // attaching comment locale to video/user info array
		   		$video[0]['userEmail'] = $userEmail[0]['email']; // attaching user email address
		   		$video[0]['name'] = html_entity_decode(strip_tags(trim($userEmail[0]['firstName'])),ENT_QUOTES,'UTF-8');
		 	  }
		  	  else
		 	  {
		  		return false;		 	
		  	  }
		 		 
		 	  $db->CloseDB();
		 	  return $video[0];
			}
			catch(Exception $err)
			{
				error_log($err->getMessage()."\n\r",3,"error.log");
			}
	}
	
	// function to delete comments
	function deleteComment($comment_id)
	{
		try
		{
		  $db = new Database();
		  ConnectDB($db);		  
		  
		  $sql = "update video_comment set isActive=0 AND isApproved=0 where id=$comment_id";
		  
		  if($db->ExecuteQuery($sql))
		   {
		   	return 1;			   
		   }
		   else
		   {
			   return 0;		 	
		   }		   
		   $db->CloseDB();
		}
		catch(Exception $err)
		{
			error_log($err->getMessage()."\n\r",3,"error.log");
		}
	}
	
	// function to get total user commnets
	function getTotoalUserComments()
	{
		try
		{
		  $db = new Database();
		  ConnectDB($db);		  
		  
		  /*
		   $sql = "SELECT COUNT(id) AS count FROM video_comment
		 		  WHERE isAdminComment = 0 AND isActive = 1;";
		 */
		  $sql = "SELECT COUNT(video_comment.id) as count
		  		FROM video_comment,video,`user`
				WHERE video_comment.isActive = 1
				AND video_comment.videoId = video.id
				AND video_comment.userId = `user`.id
				AND video.isActive = 1
				AND video.isApproved = 1
				AND `user`.isActive = 1
				AND `user`.isApproved = 1
				AND video_comment.isAdminComment = 0;";	
		  
		 $result = $db->ExecuteQuery($sql);
		 
		 if($result)
		 {
		   $arr = mysqli_fetch_array($result);
		 }
		 else
		 {
		 	return false;		 	
		 }
		 		 
		 $db->CloseDB();
		 return $arr[0];
		}
		catch(Exception $err)
		{
			error_log($err->getMessage()."\n\r",3,"error.log");
		}
	}
	
	// function to fetch user comments
	function getUserComments($Start,$Limit)
	{		
		$arr = array();
		$db = new Database();
		
		ConnectDB($db);
		$db->ExecuteQuery('SET CHARACTER SET utf8');
		$db->ExecuteQuery('SET NAMES utf8');
		
		$sql = "SELECT video_comment.id as cid,video_comment.userId,video_comment.videoId,video_comment.comments,video_comment.isActive as cact,
				video_comment.dateCreated,video_comment.isApproved as capp,video_comment.isAdminComment,
				video.name,video.id,video.isActive,video.isApproved,`user`.id,`user`.firstName,`user`.isActive,`user`.isApproved
				FROM video_comment,video,`user`
				WHERE video_comment.isActive = 1
				AND video_comment.videoId = video.id
				AND video_comment.userId = `user`.id
				AND video.isActive = 1
				AND video.isApproved = 1
				AND `user`.isActive = 1
				AND `user`.isApproved = 1
				AND video_comment.isAdminComment = 0
				ORDER BY video_comment.dateCreated DESC
				LIMIT $Start , $Limit;";		
		
		$result = $db->ExecuteQuery($sql);

		 if($result)
		 {
		   	while($data = mysqli_fetch_assoc($result))
		 	{
				$arr[] = $data;
		 	} 
		 }
		 else
		 {
			return 0;		 	
		 }
				 
		 $db->CloseDB();	
		 return $arr;
	}
	
	// function to approve / unapprove comments
	function UnAppOrApproveComment($videoid,$type)
	{
		$db = new Database();
		ConnectDB($db);
		
		if($type==1)
			$sql = "update video_comment set isApproved=$type where id=$videoid";
		else
			$sql = "update video_comment set isApproved=$type where id=$videoid";
			
		if($db->ExecuteQuery($sql))
		 {
			return true;
		 }
		 else
		 {
			 return false;		 	
		 }
		 
		 $db->CloseDB();
	}
	
	// function to approve / unapprove comments
	function getCommentById($commentId)
	{
		$db = new Database();
		ConnectDB($db);
		$db->ExecuteQuery('SET CHARACTER SET utf8');
		$db->ExecuteQuery('SET NAMES utf8');
		$sql = "SELECT * from video_comment WHERE id=$commentId;";

		$result = $db->ExecuteQuery($sql);
			
		if($result)
		 {
			$data = mysqli_fetch_assoc($result);
		 }
		 else
		 {
			 return false;		 	
		 }
		 
		 $db->CloseDB();
		 return $data;
	}
	
	// function to approve / unapprove comments
	function updateCommentById($editComment,$commentId)
	{
		$db = new Database();
		ConnectDB($db);
		
		//$sql = "UPDATE video_comment set comments='".$editComment."' WHERE id=$commentId;";
		$sql = "CALL add_edit_comments($commentId,'','','".$editComment."','');";
		$result = $db->ExecuteQuery($sql);
			
		if($result)
		 {
			return 1;
		 }
		 else
		 {
			 return 0;		 	
		 }
		 
		 $db->CloseDB();
	}
	
	function GetVideosByGenderAgeGroup($gender,$startDate,$endDate)
	{
		/*
		  * sort can be likes,videoViews,latest
		  * category can be poetry,rap
		  * name can be any string
		*/

		$db = new Database();
		ConnectDB($db);
		$db->ExecuteQuery('SET CHARACTER SET utf8');
		$db->ExecuteQuery('SET NAMES utf8');
			
		$arr = array();
		
		$query = "SELECT COUNT(id) AS `totalVideos`,
					(SELECT COUNT(id) FROM `user` 
						WHERE isApproved = 1 
						AND isActive = 1 ";
		if ($gender != "" and strtolower($gender) != "all")
		{
			$query .= " AND gender = '$gender' ";
		}
		if ($startDate != "" AND $endDate !="")
		{
			$query .= " AND  `user`.dateOfBirth <= '$startDate' AND `user`.dateOfBirth > '$endDate'";
		}
		else if($startDate != "")
		{
			$query .= " AND  dateOfBirth < '$startDate' ";
		}
		$query .=" ) AS `noOfUsers`,
					(SELECT COUNT(DISTINCT(video.id))  AS `uploads` 
						FROM video,`user`
						WHERE video.userId = user.id 
						AND `user`.isActive = 1 
						AND `user`.isApproved = 1 
						AND video.isActive = 1
						AND video.isApproved = 1 ";
		if ($gender != "" and strtolower($gender) != "all")
		{
			$query .= " AND `user`.gender = '$gender' ";
		}
		if ($startDate != "" AND $endDate !="")
		{
			$query .= " AND  `user`.dateOfBirth <= '$startDate' AND `user`.dateOfBirth > '$endDate'";
		}
		else if($startDate != "")
		{
			$query .= " AND  `user`.dateOfBirth < '$startDate'";
		}
		$query .= " ) AS `uploades`,
					(SELECT COUNT(video_rating.id) 
						FROM video_rating,video,`user` 
						WHERE video_rating.videoId = video.id 
						AND isLiked = 1 
						AND video.isActive = 1 
						AND video.isApproved = 1 
						AND video.userId = user.id 
						AND user.isActive = 1 
						AND user.isApproved = 1 ";
		if ($gender != "" and strtolower($gender) != "all")
		{
			$query .= " AND `user`.gender = '$gender' ";
		}
		if ($startDate != "" AND $endDate !="")
		{
			$query .= " AND  `user`.dateOfBirth <= '$startDate' AND `user`.dateOfBirth > '$endDate'";
		}
		else if($startDate != "")
		{
			$query .= " AND  `user`.dateOfBirth < '$startDate'";
		}
		$query .= " ) AS `votes`
				FROM video
				WHERE isActive = 1
				AND isApproved = 1 ";
		
		//echo "<br/></br>".$query;
		$result = $db->ExecuteQuery($query);
		
		if(!$result)
		{
			$db->CloseDB();
			return false;
		}
		else
		{
			$arr = mysqli_fetch_assoc($result);
		}
		
		$db->CloseDB();
		return $arr;
	}
?>