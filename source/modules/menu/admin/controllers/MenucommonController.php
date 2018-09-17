<?php
/**
 * Created by vary.
 * User: ASUS80
 * Date: 2018/9/17
 * Time: 11:12
 */

namespace source\modules\menu\admin\controllers;


use source\core\base\BaseController;
use source\LuLu;
use source\modules\menu\models\Menu;
use source\modules\rbac\RbacService;
use Yii;

class MenucommonController extends BaseController
{
    /**
     * 根据url,选中当前页面
     */
    public function actionCurrentitem(){
        $data=Yii::$app->request->get();
        $url=$data['url'];
        $menu=Menu::find()->select(['parent_id','id'])->where(['url'=>"/".$url])->one()->toArray();
        return json_encode($menu);

    }

    public function actionTest(){
        $data=Menu::getAdminMenuByRole();
    }
}