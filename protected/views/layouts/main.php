<?php /* @var $this Controller */ ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <meta name="language" content="en">

  <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/main.css">

  <title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>

  <body>
  
<!--    <?php echo CHtml::encode(Yii::app()->name); ?>-->

      <nav>
        <?php $this->widget('zii.widgets.CMenu',array(
          'items'=>array(
            array('label'=>'Chat',     'url'=>array('/main/chat')),
            array('label'=>'Login',    'url'=>array('/main/login')),
            array('label'=>'Register', 'url'=>array('/main/register')),
            array('label'=>'Logout',   'url'=>array('/main/chat&logout'))
//            array('label'=>'Login',    'url'=>array('/site/login'), 'visible'=>Yii::app()->user->isGuest),
          ),
        )); ?>
      </nav><!-- nav -->

      <?php if(isset($this->breadcrumbs)):?>
        <?php $this->widget('zii.widgets.CBreadcrumbs', array(
          'links'=>$this->breadcrumbs,
        )); ?><!-- breadcrumbs -->
      <?php endif?>

      <?php echo $content; ?>
      
  </body>
</html>
