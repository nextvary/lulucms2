<?php

namespace source\modules\rbac\models;

use source\modules\menu\models\Menu;
use Yii;
use source\LuLu;
use source\modules\rbac\RbacService;

/**
 * This is the model class for table "lulu_auth_relation".
 *
 * @property string $role
 * @property string $permission
 * @property string $value
 */
class Relation extends BaseRbacActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%auth_relation}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['role', 'permission'], 'required'],
            [['value'], 'string','max'=>128],
            [['role', 'permission'], 'string', 'max' => 64]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'role' => '角色',
            'permission' => '权限',
            'value' => '值',
        ];
    }

    public static function AddBatchItems($role, $permissions)
    {
        self::deleteAll(['role' => $role]);
        LuLu::deleteCache(RbacService::CachePrefix.$role);
        
        foreach ($permissions as $key => $value)
        {
            $menu=null;
            if(!in_array($key,['allow_access','deny_access','manager_admin'])){
                $menu=Menu::getParentMenuByUrl($key);
            }

            $newRelation = new Relation();
            $newRelation->role = $role;
            $newRelation->permission = $key;
            $newRelation->menu_id = $menu->id??0;
            $newRelation->value = is_string($value) ? $value : implode(',', $value);
            $newRelation->save();
        }
    }
}
