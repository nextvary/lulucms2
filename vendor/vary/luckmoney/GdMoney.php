<?php
/**
 * Created by renfei.
 * User: vary
 * Date: 2017/7/18
 * Time: 18:41
 */
namespace vary\luckmoney;


//固定等额红包策略
class GdMoney
{
    //单个红包金额
    public $oneMoney;
    //数量
    public $num;
    public function construct($option = null)
    {
        if($option instanceof OptionDTO)
        {
            $this->setOption($option);
        }
    }
    public function setOption(OptionDTO $option)
    {
        $this->oneMoney = $option->rangeStart;
        $this->num = $option->num;
    }
    public function create()
    {
        $data = array();
        if(false == $this->isCanBuilder())
        {
            return $data;
        }
        $data = array();
        if(false == is_int($this->num) || $this->num <= 0)
        {
            return $data;
        }
        for($i = 1;$i <= $this->num;$i++)
        {
            $data[$i] = $this->fx($i);
        }
        return $data;
    }

    /**
     * 等额红包的方程是一条直线
     *
     * @param mixed $x
     * @access public
     * @return void
     */
    public function fx($x)
    {
        return $this->oneMoney;
    }

    /**
     * 是否能固定红包
     *
     * @access public
     * @return void
     */
    public function isCanBuilder()
    {
        if(false == is_int($this->num) || $this->num <= 0)
        {
            return false;
        }

        if(false ==  is_numeric($this->oneMoney) || $this->oneMoney <= 0)
        {
            return false;
        }

        //单个红包小于1分
        if($this->oneMoney < 0.01)
        {
            return false;
        }
        return true;
    }
}
