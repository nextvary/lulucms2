<?php
/**
 * Created by renfei.
 * User: vary
 * Date: 2017/7/18
 * Time: 18:56
 */

namespace vary\luckmoney;
//include(dirname(__FILE__)."./RandMoney.php");

//维护策略的环境类
use Exception;

class RedPackageBuilder
{

    // 实例
    protected static $_instance = null;

    /**
     * Singleton instance（获取自己的实例）
     *
     */
    public static function getInstance()
    {
        if (null === self::$_instance)
        {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * 获取策略【使用反射】
     *
     * @param string $type 类型
     */
    public function getBuilderStrategy($type)
    {
        $class=__NAMESPACE__.'\\'.$type.'Money';
        if (class_exists($class)){
            return new $class;
        }else{
            throw new Exception('Class Not Exists!!');
        }
    }

    public function getRedPackageByDTO(OptionDTO $optionDTO)
    {

        //获取策略
        $builderStrategy = $this->getBuilderStrategy($optionDTO->builderStrategy);
        //设置参数
        $builderStrategy->setOption($optionDTO);

        return $builderStrategy->create();
    }

}
