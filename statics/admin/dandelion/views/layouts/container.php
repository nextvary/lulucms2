<?php
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use source\models\Taxonomy;
use source\libs\Resource;
use source\LuLu;
use yii\helpers\Url;
use source\modules\menu\models\Menu;

/* @var $this \yii\web\View */
/* @var $content string */

$rbacService = LuLu::getService('rbac');

?>

<?php $this->beginPage() ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <!-- Viewport metatags -->
    <meta name="HandheldFriendly" content="true" />
    <meta name="MobileOptimized" content="320" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />

    <!-- iOS webapp metatags -->
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black" />

    <!-- iOS webapp icons -->
    <link rel="apple-touch-icon" href="touch-icon-iphone.png" />
    <link rel="apple-touch-icon" sizes="72x72" href="touch-icon-ipad.png" />
    <link rel="apple-touch-icon" sizes="114x114" href="touch-icon-retina.png" />

    <?php Resource::registerAdmin([
        '/css/reset.css',
        '/css/fluid.css',
        '/css/dandelion.theme.css',
        '/css/dandelion.css',
        '/css/demo.css',

    ]);?>


    <!-- jQuery JavaScript File -->
    <?php Resource::registerAdmin('/js/jquery-1.7.2.min.js');?>

    <!-- jQuery-UI JavaScript Files -->

    <?php Resource::registerAdmin([
        '/plugins/jui/js/jquery-ui-1.8.20.min.js',
        '/plugins/jui/js/jquery.ui.timepicker.min.js',
        '/plugins/jui/js/jquery.ui.touch-punch.min.js',
        '/plugins/jui/css/jquery.ui.all.css',
    ]);

    ?>

    <!-- Plugin Files -->
    <?php Resource::registerAdmin([
        '/plugins/fileinput/jquery.fileinput.js',
        '/plugins/placeholder/jquery.placeholder.js',
        '/plugins/mousewheel/jquery.mousewheel.js',
        '/plugins/tinyscrollbar/jquery.tinyscrollbar.min.js',
        '/plugins/tipsy/jquery.tipsy-min.js',
        '/plugins/tipsy/tipsy.css',
    ]);?>

    <!-- Demo JavaScript Files -->
    <?php Resource::registerAdmin([
        '/js/demo/demo.validation.js',
        '/js/demo/demo.ui.js',
        '/js/demo/demo.tables.js',
    ]);?>

    <!-- Core JavaScript Files -->
    <?php Resource::registerAdmin([
        '/js/core/dandelion.core.js',
        '/js/core/dandelion.customizer.js',
    ]);?>

    <title><?php echo $this->title ?> - <?php echo $this->getConfigValue('sys_site_name')?></title>
    <script type="text/javascript">
        function toggleMenu(id) {
            $(".menu-item").hide();
            $("#menu-item-" + id).show();
            $(".da-header-menu-item").removeClass("current");
            $("#menu-" + id).addClass("current");
            $("#menu-item-" + id).find('ul > li:eq(0) >a')[0].click();
            $("#menu-item-" + id).find('ul > li:eq(0) >a').addClass('current_item');
        }
        $(function () {
            $('#da-main-nav  a[href!=#]').click(function (e) {
                var a=$(e.target);
                $('#da-main-nav a').removeClass('current_item');

                a.addClass('current_item');
                var u = window.location.href;
                var end = u.indexOf("#");
                var rurl = u.substring(0,end);
                //设置新的锚点
                window.location.href = rurl + "#" + a.attr('href');

            });

//            $.ajax()
        });
        document.addEventListener('DOMContentLoaded', function () {
            var hash = location.hash;
            var url = hash.substring(1,hash.length);
            console.log(hash);
            if(url==''){
                url="<?=Url::to(['menu/menucommon/currentitem',"url"=>'']) ?>"+'site/welcome';
                $('#da-header-menu >div:eq(0)').click();return;
            }else{
                $("#mainFrame").attr("src", url);
                url=url.split('=')[1];
                url="<?=Url::to(['menu/menucommon/currentitem',"url"=>'']) ?>"+url;
            }

            $.ajax({
                'url':url,
                "dataType":"json",
                'success':function (e) {
                    $('#da-header-menu >div ').removeClass('current');
                    $('#menu-'+e.parent_id).addClass('current');
                    $('.menu-item').hide();
                    $('#menu-item-'+e.parent_id).show();
                    $('#menu-item-'+e.id+" > a").addClass('current_item');
                }
            });
        }, false);


    </script>
    <style type="text/css">
        html, body {
            height:100%;
        }
        #da-content #da-main-nav ul li a.current_item{
            background: red;
        }
    </style>
    <?php $this->head() ?>
</head>

<body onresize="iFrameHeight();">
    <?php $this->beginBody() ?>

    <table style="width: 100%; height: 100%; margin:0px;" id="tableLayout">
        <tr>
            <td>
                <!-- Header -->
                <div id="da-header">

                    <div id="da-header-top">

                        <!-- Container -->
                        <div class="da-container clearfix">

                            <!-- Logo Container. All images put here will be vertically centere -->
                            <div id="da-logo-wrap">
                                <div id="da-logo">
                                    <div id="da-logo-img">
                                        <a href="<?php echo LuLu::getAlias('@web').'/admin.php';?>">
                                            <img src="<?php echo Resource::getAdminUrl()?>/images/logo.png" alt="Dandelion Admin" />
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div id="da-header-menu">
                                <?php
                                foreach($this->rbacService->getParentMenuByRole(LuLu::getIdentity()->role) as $item)
                                {

                                    ?>
                                    <div class="da-header-menu-item " id="menu-<?php echo $item['id']?>" onclick="toggleMenu(<?php echo $item['id']?>);"><?php echo $item['name']?></div>
                                    <?php
                                }

                                ?>

                            </div>
                            <!-- Header Toolbar Menu -->
                            <!-- Header Toolbar Menu -->
                            <div id="da-header-toolbar" class="clearfix">
                                <div id="da-user-profile">
                                    <div id="da-user-avatar">
                                        <img src="<?php echo Resource::getAdminUrl()?>/images/pp.jpg" alt="" />
                                    </div>
                                    <div id="da-user-info">
                                        <?php echo LuLu::$app->user->identity->username?>
                                    </div>
                                    <ul class="da-header-dropdown">
                                        <li class="da-dropdown-caret">
                                            <span class="caret-outer"></span>
                                            <span class="caret-inner"></span>
                                        </li>
                                        <li class="da-dropdown-divider"></li>
                                        <li><a href="<?php echo LuLu::getAlias('@web').'/index.php';?>" target="_blank">站点首页</a></li>
                                    </ul>

                                </div>
                                <div id="da-header-button-container">
                                    <ul>
                                        <li class="da-header-button logout">
                                            <?php echo Html::a('退出',['/site/logout'])?>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <td height="100%">
                <table style="width: 100%; height: 100%;margin:0px; padding:0px;" id="da-content">
                    <tr>
                        <td width="200px">
                            <div id="da-sidebar">

                                <!-- Main Navigation -->
                                <div id="da-main-nav" class="da-button-container">
                                    <ul>
<!--                                        --><?//= echo Menu::getAdminMenu();?>
                                        <?php echo Menu::getAdminMenuByRole(LuLu::getIdentity()->role);?>
                                    </ul>
                                </div>

                            </div>
                        </td>
                        <td >
                            <iframe  id="mainFrame" name="mainFrame" width="100%" height="100%"
                                style="overflow: visible;background-color:transparent" frameborder="0" scrolling="yes"
                                allowtransparency="true"
                                src="<?php echo Url::to(['/site/welcome'])?>" onLoad="iFrameHeight()"></iframe>
    <script type="text/javascript" language="javascript">

        function iFrameHeight() {
            var bodyHeight = document.body.scrollHeight;

            var contentHeight = document.body.scrollHeight - 100;

            console.log(contentHeight);

            //$("#tableLayout").height(bodyHeight);

            var ifm = document.getElementById("mainFrame");
            ifm.height = contentHeight;


            //ifm.height=300;
        }
    </script>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td height="40px">
                <!-- Footer -->
                <div id="da-footer">
                    <div class="da-container clearfix">
                        <p>
                        Copyright 2018.  Admin. All Rights Reserved.</p>
                    </div>
                </div>
            </td>
        </tr>
    </table>
    <?php $this->endBody() ?>
</body>
</html>

<?php $this->endPage() ?>
