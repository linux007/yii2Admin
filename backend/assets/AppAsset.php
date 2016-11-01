<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'adminlte/bootstrap/css/bootstrap.min.css',
        'adminlte/libs/font-awesome.min.css',
        'adminlte/libs/ionicons.min.css',
        'adminlte/dist/css/AdminLTE.min.css',
        'adminlte/dist/css/skins/_all-skins.min.css',
        'adminlte/plugins/iCheck/flat/blue.css',
        'adminlte/plugins/morris/morris.css',
        'adminlte/plugins/jvectormap/jquery-jvectormap-1.2.2.css',
        'adminlte/plugins/datepicker/datepicker3.css',
        'adminlte/plugins/daterangepicker/daterangepicker-bs3.css',
        'adminlte/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css',
    ];
    public $js = [
//        'adminlte/plugins/jQuery/jQuery-2.1.4.min.js',
        'adminlte/plugins/jQueryUI/jquery-ui.js',
//        'adminlte/bootstrap/js/bootstrap.min.js',
        'adminlte/libs/raphael-min.js',
//        'adminlte/plugins/morris/morris.min.js',
        'adminlte/plugins/sparkline/jquery.sparkline.min.js',
        'adminlte/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js',
        'adminlte/plugins/jvectormap/jquery-jvectormap-world-mill-en.js',
        'adminlte/plugins/knob/jquery.knob.js',
        'adminlte/libs/moment.min.js',
        'adminlte/plugins/daterangepicker/daterangepicker.js',
        'adminlte/plugins/datepicker/bootstrap-datepicker.js',
        'adminlte/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js',
        'adminlte/plugins/slimScroll/jquery.slimscroll.min.js',
        'adminlte/plugins/fastclick/fastclick.min.js',
        'adminlte/dist/js/app.min.js',
//        'adminlte/dist/js/pages/dashboard.js',
        'adminlte/dist/js/demo.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
//        'yii\bootstrap\BootstrapAsset',
    ];
}
