<style>
#content
{
  width: 40%;
}
</style>

<form method="post" enctype="multipart/form-data">
  <textarea name="text" cols="20" rows="10" placeholder="Type something..."></textarea><br><br>
  <input type="file" name="userfile"><br><br>
	<button type="submit" value="submit">Post</button><br><br>
</form>

<hr>
<div id="messages">
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
	  $showText = substr($text, 0, 150);
	  $tooLongMessage = true;
	}	
?>	

<h2><?= $userRow['login'] ?></h2>

<p><?= $showText ?><?= $tooLongMessage ? "... <a href=\"message/$messageId\">more</a>" : '' ?></p>
<?php if($image != '') {  ?>
<img src="/<?= $image ?>"><br>
<?php } ?> 

<br>
<a href="message/<?= $messageId ?>">Read more</a>
<br>
<br>

<hr>

<?php
	}
}

?>

</div>
