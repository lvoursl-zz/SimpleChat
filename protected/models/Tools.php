<?php
class Tools 
{
	public static function textIsValid($name, $text)
	{
	  $res = true;
	  if (trim($name) == '')
	  {
		$res = false;
		return $res;
	  }
	  
	  if (trim($text) == '')
	  {
		$res = false;
		return $res;
	  }
	  
	  return $res;
	}

	public static function ourIsAuth( $cookieName, $salt, 
					 &$loggedUserId, &$loggedUserName) {	  
	  if(isset($_COOKIE[$cookieName])) {
		$broken = $_COOKIE[$cookieName];
		$broken = explode(';',$broken);
		
		$key = md5($broken[0] . $broken[1] . $broken[2] . $salt);
		
		if($broken[3] == $key) {
		  Yii::app()->params->loggedUserId = $broken[0];
		  Yii::app()->params->loggedUserName = $broken[1];
		  return true;
		} else {
		  return false;
		}
	  } else {
		return false;
	  }
		  
	}

	public static function messageIsUnique($name, $text, $image, $mysql) 
	{
	  $hash = md5($name . $text . $image);
	  $sql = "SELECT * FROM messages WHERE hash = '$hash' ";
	  $res = mysql_query($sql, $mysql);
	  $row = mysql_fetch_assoc($res);
	  if ($row == false) {
		return true;
	  }  else {
		return false;
	  }
	  
	}

	public static function commentIsUnique($id, $messageId, $text, $mysql) 
	{
	  $hash = md5($id . $text);
	  $sql = "SELECT * FROM comment WHERE message_id = '$messageId' AND hash = '$hash' ";
	  $res = mysql_query($sql, $mysql);
	  $row = mysql_fetch_assoc($res);
	  if ($row == false) {
		return true;
	  }  else {
		return false;
	  }
	  
	}
}