<?php
namespace vary\luckmoney;
//配置传输数据DTO
class OptionDTO
{
    //红包总金额
    public $totalMoney;
    //红包数量
    public $num;
    //范围开始
    public $rangeStart;
    //范围结算
    public $rangeEnd;
    //生成红包策略
    public $builderStrategy;
    //随机红包剩余规则
    public $randFormatType; //Can_Left：不修数据,可以有剩余；No_Left：不能有剩余
    public static function create($totalMoney,$num,$rangeStart,$rangEnd,$builderStrategy,$randFormatType = 'No_Left')
    {
        $self = new self();
        $self->num = $num;
        $self->rangeStart = $rangeStart;
        $self->rangeEnd = $rangEnd;
        $self->totalMoney = $totalMoney;
        $self->builderStrategy = $builderStrategy;
        $self->randFormatType = $randFormatType;
        return $self;
    }
}





//class Client
//{
//    public static function main()
//    {
//        //固定红包
//        $dto = OptionDTO::create(1000,10,100,100,'Gd');
//        $data = RedPackageBuilder::getInstance()->getRedPackageByDTO($dto);
//        // print_r($data);
//
//        //随机红包[修数据]
//        $dto = OptionDTO::create(100,10,5,20,'Rand');
//        $data = RedPackageBuilder::getInstance()->getRedPackageByDTO($dto);
//
//        //随机红包[不修数据]
//        $dto = OptionDTO::create(5,10,0.01,0.99,'Rand','Can_Left');
//        $data = RedPackageBuilder::getInstance()->getRedPackageByDTO($dto);
//        // print_r($data);
//    }
//}

//$money=Client::main();
//var_dump($money);
// $key=mt_rand(0,10);
