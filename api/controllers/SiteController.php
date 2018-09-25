<?php
/**
 * Created by vary.
 * User: ASUS80
 * Date: 2018/9/25
 * Time: 16:11
 */

namespace api\controllers;


use source\core\base\BaseController;
use vary\myfunction;

class SiteController extends BaseController
{

    public function actionIndex(){
        return myfunction::json(1,'eee');
    }

    public function actionGit(){
        echo 1;
        $result=system('sh '.$_SERVER['DOCUMENT_ROOT'].'/crontab/git.sh');
        var_dump($result);
//        system('sh /usr/local/work/.sh');
    }
}