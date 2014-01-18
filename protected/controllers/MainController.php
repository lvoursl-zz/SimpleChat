<?php
class MainController extends Controller
{
	public function actionIndex()
	{
		if (!Tools::ourIsAuth(Yii::app()->params->cookieName, 
				 Yii::app()->params->salt, 
         Yii::app()->params->loggedUserId, 
         Yii::app()->params->loggedUserName)) {
			$this->redirect('index.php?r=main/login');
		} else {
			$this->redirect('index.php?r=main/chat');
		}
	}

	public function actionLogin()
	{
		if (isset($_POST['login'])) {
			$mySql = mysql_connect(Yii::app()->params->server, 
									Yii::app()->params->dbuser, 
									Yii::app()->params->dbpassword);			  
			mysql_select_db(Yii::app()->params->dbName, $mySql);
			  
			$login = $_POST['login'];
			$password = $_POST['password'];		  
		  $password = md5($password . Yii::app()->params->salt);
		  
			$sql = "SELECT * FROM users WHERE login='$login'"; 
			$res = mysql_query($sql, $mySql);
			$userRow = mysql_fetch_assoc($res);
			if ($userRow == false) {
				echo "<div class=\"info error\">We haven't got this user :[</div>";
			} else {
			    $userId = $userRow['id']; 
			    if ($password == $userRow['password']) {
			      $cookieValue = $userId . ';' . $login . ';' . $password . ';' .
			                      md5($userId . $login . $password 
			                      	   . Yii::app()->params->salt);
			      setcookie(Yii::app()->params->cookieName,
			      			$cookieValue, time() 
			      			+ Yii::app()->params->cookieTime);
			      Yii::app()->params->loggedUserName = $login;
			      Yii::app()->params->loggedUserId = $userId;
			      Yii::app()->params->logged = true;

			      $this->redirect('index.php?r=main/chat');
			    } elseif ($userRow != false) {
			      echo "<div class=\"info error\">Wrong password!</div>";
			    }
		 	}
		} else {
			echo "<div class=\"info\">You must sign in to view chat!</div>";
		}
		$this->render('login');
	}

	public function actionRegister()
	{
		# code...
		$this->render('register');
		if (isset($_POST['register'])) {			
			$mySql = mysql_connect(Yii::app()->params->server, 
									Yii::app()->params->dbuser, 
									Yii::app()->params->dbpassword);			  
			mysql_select_db(Yii::app()->params->dbName, $mySql);

			$login = $_POST['login'];
			$password = $_POST['password'];			  
			$password = md5($password . Yii::app()->params->salt);
			$sql = "INSERT INTO users (login, password) VALUES ('$login', 
																'$password') ";  
			$res = mysql_query($sql, $mySql);

			if($mySql==false) {
			    echo "<div class=\"info error\">ERROR!!!</div>"; 
			} else {
			    echo "<div class=\"info success\">Register complete successfully!</div>";
			} 
			  
		    $id = mysql_insert_id($mySql);
		}

	}

	public function actionChat()
	{
		if (isset($_GET['logout']))
		{
		  setcookie(Yii::app()->params->cookieName, '', time() - 86400);
		  $this->redirect('index.php?r=main/login'); 
		}

		//fail here cuz we Yii::app()->params->loggedUserName
		// is empty
		$testAuth = Tools::ourIsAuth(Yii::app()->params->cookieName, 
						   	Yii::app()->params->salt, 
                Yii::app()->params->loggedUserId, 
                Yii::app()->params->loggedUserName);

		//$testAuth = isAuth();
		if($testAuth == false) {
		  $this->redirect('index.php?r=main/login'); // мы всегда оказываемся в логине, т.к. данные в конфиге (config/main.php) пусты
		}
		$mySql = mysql_connect(Yii::app()->params->server, 
								Yii::app()->params->dbuser, 
								Yii::app()->params->dbpassword);

		mysql_select_db(Yii::app()->params->dbName, $mySql);
		if (isset($_POST['text'])) {
		  $name = Yii::app()->params->loggedUserName; // кофиг пуст. поэтому не можем постить
		  $loggedUserId = Yii::app()->params->loggedUserId;
		  $text = $_POST['text'];
		  if (Tools::textIsValid($name, $text))
		  {
		    $image = '';
		  	if ($_FILES['userfile']['name'] != '') {
		      $uploaddir 	= realpath(Yii::app()->basePath . '/../images');
			  $img			= 	$loggedUserId . '_' . time() . 
								substr(basename($_FILES['userfile']['name']), 
		                        strripos(basename($_FILES['userfile']['name']), '.'));
		      $uploadfile 	= $uploaddir . '/' . $img;
		    
		      if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile))
		      {
		    	  $image = "new/images/{$img}";
		      }
		    }
		    
		    $file = $image == '' ? '' : file_get_contents($uploadfile);
		    if (Tools::messageIsUnique($name, $text, $file, $mySql)) {
		      $loggedUserIdAgain = Yii::app()->params->loggedUserId; 
		      $hashMessage = md5($name . $text . $file);
		      $sql = "INSERT INTO messages (user_id, text, image, hash) 
		              VALUES ('$loggedUserIdAgain', '$text', 
		              		  '$image', '$hashMessage') ";
		      $res = mysql_query($sql, $mySql);
		    } else {
		      echo ("<div class=\"info error\">Stop spam & flood, $name</div>");
		    }
		    
		  
		  } else {
		    echo  "<div class=\"info error\">Incorrect message</div>";
		  }

		}		
		$sql = "SELECT * FROM messages ORDER BY id DESC ";

		$messagesRes = mysql_query($sql, $mySql);
		
		$this->render('chat', array('messagesRes' => $messagesRes, 'mySql' => $mySql));
	}
}
