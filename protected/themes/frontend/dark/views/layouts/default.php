<!DOCTYPE html>
<html lang="<?= Yii::app()->getLanguage() ?>">
  <head>
    <title><?= CHtml::encode($this->getPageTitle()) ?></title>
    <meta charset="<?= Yii::app()->charset ?>" />
    <meta http-equiv="Content-Type" content="text/html; charset=<?= Yii::app()->charset ?>" />
    <!--[if lt IE 9]>
        <script type="text/javascript" src="<?= $this->coreScriptUrl('html5shiv') ?>"></script>
    <![endif]-->
  </head>
  <body>
    <a name="vira-page-top"></a>
    <div class="page">
      <?= $content ?>
    </div>
  </body>
</html>
