<?php
use yii\helpers\Html;
?>
<!-- Main content -->
<section class="content">
    <div class="error-page">
        <h2 class="headline text-yellow"> <?= $exception->statusCode ?></h2>
        <div class="error-content">
            <h3><i class="fa fa-warning text-yellow"></i> <?= $name .' '. Html::encode($this->title) ?></h3>
            <p>
                <?= nl2br(Html::encode($message)) ?>
            </p>
            <div class="margin-bottom"></div>
            <div class="input-group">
                <a href="javascript:history.go(-1);" class="btn btn-default"><i><<</i>返回</a>
            </div>
        </div><!-- /.error-content -->
    </div><!-- /.error-page -->
</section><!-- /.content -->
