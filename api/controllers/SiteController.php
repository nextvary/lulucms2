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
}