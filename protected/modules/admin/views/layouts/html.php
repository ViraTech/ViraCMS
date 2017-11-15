<!DOCTYPE html>
<html>
  <head>
    <title><?= $this->getPageTitle() ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=<?= Yii::app()->charset ?>" />
    <link href="<?= Yii::app()->theme->getImageUrl('favicon.ico') ?>" rel="icon" type="image/x-icon" />
    <!--[if lt IE 9]>
      <script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
  </head>
  <body>
    <?= $content ?>
  </body>
</html>
