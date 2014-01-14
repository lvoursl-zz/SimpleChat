<a href=/new/index.php?r=main/chat&logout><button>logout</button></a>

<form method="post" enctype="multipart/form-data">
  Text: <textarea name="text" cols="20" rows="10"></textarea><br>
  Image: <input type="file" name="userfile"><br><br>
	<input type="submit" value="submit">
</form>

<hr align="left" width="50%">

<?
if(!$messagesRes) 
{
	echo ('not selected<br>');
} 
else 
{
	for ( ; $messageRow = mysql_fetch_assoc($messagesRes); ) {
		$userId     = $messageRow['user_id'];
		$text       = $messageRow['text'];
		$image      = $messageRow['image'];
		$messageId  = $messageRow['id'];
	
	$sql        = "SELECT login FROM users WHERE id = $userId ";
	$userRow    = mysql_fetch_assoc(mysql_query($sql, $mySql));
	
	$tooLongMessage = false;
	if (strlen($text) < 50) {
	  $showText = $text;
	} else {
	  $showText = substr($text, 0, 50);
	  $tooLongMessage = true;
	}	
?>	

<h3><?= $userRow['login'] ?></h3>

<p><?= $showText ?><?= $tooLongMessage ? "... <a href=\"message/$messageId\">далее</a>" : '' ?></p>
<?php if($image != '') {  ?>
<img src="/<?= $image ?>" width="400" height="200">
<?php } ?> 

<br>
<a href="message/<?= $messageId ?>">Read more</a>

<hr align="left" width="30%">

<?php
	}
}

