<?php

namespace source\modules\download\models;

use Yii;
use source\LuLu;
use yii\helpers\ArrayHelper;
/**
 * This is the model class for table "{{%download_task}}".
 *
 * @property integer $id
 * @property string $filename
 * @property string $download_url
 * @property integer $download_status
 * @property integer $download_type
 */
class Download extends \source\core\base\BaseActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%download_task}}';
    }

    public static function insertData($data)
    {

        try{
            $model=new self();
            $model->download_url=$data['downurl'];
            $model->filename=$data['filename'];
            $model->dirname=$data['dirname'];
            if($model->save()){
                return true;
            }else{
                return $model->getErrors();
            }

        }catch (\Exception $e){
            return $e->getMessage();
        }

    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['download_status', 'download_type'], 'integer'],
            [['filename','dirname'], 'string', 'max' => 255],
            [['download_url'], 'string']
        ];
    }
    
    public static function getAttributeLabels($attribute = null)
    {
        $items = [
            'id' => 'ID',
            'filename' => 'Filename',
            'dirname' => 'Dirname',
            'download_url' => 'Download Url',
            'download_status' => 'Download Status',
            'download_type' => 'Download Type',
        ];
        return ArrayHelper::getItems($items, $attribute);
    }
    
}
