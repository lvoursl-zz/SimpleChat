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
		# code...
		
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
				echo "<P style = 'color:red'>We haven't got this user ;[</P>";
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
			      echo "<P style = 'color:red'>wrong password</P>";
			    }
		 	}
		}
		$this->render('login');
		//$this->render('login');
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
			    echo "ERROR!!!"; 
			} else {
			    echo "Register complete successfully";
			} 
			  
		    $id = mysql_insert_id($mySql);
		}

	}

	public function actionChat()
	{
		# code...
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
		if( $testAuth == false) {
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
		      echo ("<P style = 'color:red'>Stop spam & flood, $name</P>");
		    }
		    
		  
		  } else {
		    echo  "<P style = 'color:red'>Incorrect message</P>";
		  }

		}		
		$sql = "SELECT * FROM messages ORDER BY id DESC ";

		$messagesRes = mysql_query($sql, $mySql);
		
		$this->render('chat', array('messagesRes' => $messagesRes, 'mySql' => $mySql));
	}
}
