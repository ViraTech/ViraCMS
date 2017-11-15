<!DOCTYPE html>
<html>
  <head>
    <title><?= Yii::app()->site->name ?></title>
    <style type="text/css">
      body {
        font-family: 'Open Sans',sans-serif;
        margin: 0;
        padding: 0;
        background-color: #000000;
        color: #ffffff;
        background: transparent url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAATIAAAAOCAYAAACsCZCnAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAB3RJTUUH3QQSCQQsNIEO7QAAAB1pVFh0Q29tbWVudAAAAAAAQ3JlYXRlZCB3aXRoIEdJTVBkLmUHAAABnUlEQVR42u1aSa7DIAzFXOQvvtT7nzB0RWVRg01IUjDvbVooIM8Dhf7+XykwEFE4jiPEGENK6TMmohBC+MzltSklcY6fx8d8rTSuIZ+jnVfbJ9HSQ4N2juWMvEbar8mNy9+KUl78jKxfjVerrCxy7qW7tD2LTUg2WfJ/NWq8a99L/iR++JjTL/F5Voea7jQ99MpIm9d8QZqPLWPaDTvyDMBWPSBCwOGr6vGOXfj0rAvosAhkniI7shSwi80+vW+Jioz36PmzdS/Rmit/1wR3RWYp6e9V3CgNLXlZjclCZ68R1vTSe462XrrLu4P/XbqAfPc1YqO1/ZJ/Svdj5afVZ2rzFp2f9SMiwh0ZsG8bvHJ7htZSqMiA9SsAr4nnTr48J+vdAh0CGYwEmES/dz8T0XidJbCfkUP05KxPKWLGTI7gikoNrSUAAI8khdE/hZCUfxTIrjCKEWHvXKk8ZaSjL/mBfr21Xu3f4R8z+hGnyVVFhvbqd44H2c9hx6PPNFb11+jNoVYonVfL8sA6lfSuOowzKwVtHIDKDD5jKVTemKV1faXZFGsAAAAASUVORK5CYII=') left top repeat;
        color: #fafafa;
      }
      a,a:hover,a:visited,a:active {
        color: #ffffff;
      }
      a:hover {
        text-decoration: none;
      }
      .logo {
        text-align: center;
        margin: 100px auto;
        z-index: -1;
        width: 200px;
        height: 200px;
        background: transparent url('data:image/png;base64,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') center center no-repeat;
      }
      .logo a {
        position: relative;
        top: 230px;
        font-size: 14px;
      }
      .maintenance {
        position: absolute;
        left: 0;
        top: 50%;
        margin-top: -220px;
        width: 100%;
        text-align: center;
      }
      .maintenance h1 {
        margin: 0;
        font-weight: 100;
        font-size: 32px;
        line-height: 40px;
        margin-bottom: 10px;
      }
      .sitename {
        text-align: center;
        font-size: 25px;
        line-height: 25px;
        margin: 30px 0;
      }
      .sitename small {
        padding: 0 0 0 10px;
        margin: 0 0 0 5px;
        border-left: 1px solid #ffffff;
        font-size: 16px;
        line-height: 16px;
        position: relative;
        top: -2px;
      }
      .powered {
        position: absolute;
        bottom: 20px;
        left: 0;
        right: 0;
        text-align: center;
        font-size: 12px;
        line-height: 30px;
      }
    </style>
  </head>
  <body>
    <div class="maintenance">
      <h1><?= Yii::t('common', 'The Site is Under Maintenance') ?></h1>
      <div class="sitename"><?= Yii::app()->site->name ?> <small><?= Yii::app()->params['adminEmail'] ?></small></div>
      <small><?= Yii::t('common', "We're currently updating the site. We'll be back soon") ?></small>
      <div class="logo">
        <?php if (Yii::app()->user->type == VAccountTypeCollection::ADMINISTRATOR): ?>
          <a href="<?= $this->createUrl('/admin/default/index') ?>"><?= Yii::t('common', "Administrator's Entrance") ?></a>
        <?php endif; ?>
      </div>
    </div>
    <div class="powered"><?= Yii::t('common', 'Powered by') ?> <a href="http://www.viracms.ru/" target="_blank">ViraCMS</a></div>
  </body>
</html>
