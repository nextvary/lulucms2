<?php
/**
 * Created by renfei.
 * User: Administrator
 * Date: 2017/6/30 0030
 * Time: 9:23
 */
namespace vary;
use yii\web\UploadedFile;
use Yii;

class yiifunction
{
    public  static function  getParam($param,$str){
        preg_match_all("/{$param}=([^&?]*)/",$str,$result);
        return $result[1];
    }
    public static function mvFile($file,$newfile){
        Yii::console($file);
        Yii::console($newfile);
        if (!is_dir(dirname($newfile))){
            mkdir(dirname($newfile),0744,true);
        }
        if (@copy($file,$newfile)){
            return @unlink($file);
        }
        return false;
    }
    public static function oneError($model){
        $error='';
        foreach ($model->getErrors() as $v){
            $error=$v[0];
            break;
        }
        return $error;
    }

    /**
     *  $filename='预约结果';
        $title=array('预约内容','观看url','预约用户','联系方式','预约类型','预约时间',"通过时间");
        $width=['A'=>20,'B'=>40,"D"=>20,"F"=>40,"G"=>40];
     * @param $need
     * @param $filename
     * @param $title
     * @param $width
     */
    public static function downDload($need,$filename,$title,$width)
    {
        require_once(Yii::getAlias("@vendor/yiisoft/yii2/phpexcel/PHPExcel.php"));

        $excel = new \PHPExcel();
        $letter = array('A','B','C','D','E','F','G','H','I',"J","K","L","M","N","O","P","Q","R","S","T","U","V","W");
        foreach ($width as $key=>$v){
            $excel->getActiveSheet()->getColumnDimension($key)->setWidth($v);

        }

        $tableheader = $title;
        $headerlenth=count($tableheader);
        for($i = 0;$i < $headerlenth;$i++) {
            $excel->getActiveSheet()->setCellValue("$letter[$i]1","$tableheader[$i]");
        }
        $excel->getActiveSheet()->setTitle($filename);
        for ($i = 2;$i <= count($need) + 1;$i++) {
            $j = 0;
            foreach ($need[$i - 2] as $key=>$value) {
                $excel->getActiveSheet()->setCellValue("$letter[$j]$i","$value");
                $j++;
            }
        }

        $write = new \PHPExcel_Writer_Excel2007($excel);
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
        header("Content-Type:application/force-download");
        header("Content-Type:application/vnd.ms-execl");
        header("Content-Type:application/octet-stream");
        header("Content-Type:application/download");;
        header('Content-Disposition:attachment;filename="'.$filename.'.xlsx"');
        header("Content-Transfer-Encoding:binary");
        $write->save('php://output');
        die();

    }

    /**
     * 上传文件
     * @param $file_name //文件名
     * @param $save_dir "data/runtime/tmp/tmpimg/".date('Ymd')."/";
     * @param $save_name  //mt_rand(0,999999).time()
     * @param $file_type
     * @return string|array 相对路径或者绝对路径
     */
    public function upload_file($file_name, $save_dir, $save_name, $file_type='jpg,txt,png,jpeg'){
        $file_type=explode(',',$file_type);
        if (!is_dir($dir=Yii::$app->basePath.'/../'.$save_dir)){
            mkdir(Yii::$app->basePath.'/../'.$save_dir,0777,true);
        }
        $file=UploadedFile::getInstanceByName($file_name);
        if (empty($file)){
            return "参数错误";
        }
//        echo json_encode(($file_type));
        $absolute_path=$dir.$save_name.'.'.$file->extension;
        $relative_path=$save_dir.$save_name.'.'.$file->extension;
        if (in_array($file->extension,$file_type)){
            if ($file && $file->saveAs($absolute_path)){
                $data['absolute_path']=$absolute_path;
                $data['relative_path']=$relative_path;
                $data['extension']=$file->extension;
                return $data;
            }else{
                return '上传失败';
            }
        }else{
            return '请选择正确的文件';
        }
    }

    /**
     * 二维数组根据键去重
     * @param $arr
     * @param $key array
     * @return array
     */
    public static function array_diff($arr, $key){
        $result=[];
        $lenth=count($key);

        foreach ($arr as $value) {
            $has=false;
            foreach ($result as $val) {
                $str='';
                for ($i=0; $i < $lenth; $i++) {
                    $str .= "'".$val[$key[$i]]."' == '".$value[$key[$i]]."' && ";
                }
                $str=rtrim($str,'&& ');
                if (eval("return ($str) ;" )) {
                    $has=true;
                    break;
                }

            }
            if (!$has) {
                $result[]=$value;
            }
        }
        return $result;
    }
    /**
     * 删除目录下的文件以及文件夹
     * @param $file_path //绝对路径
     */
    public function remove_div_file($file_path){
        $path=dirname($file_path);
        $dh=opendir($path);
        while ($file=readdir($dh)){
            if ($file!='.' &&$file!='..'){
                if (is_dir("$path/$file")){
                    $this->remove_div_file("$path/$file");
                    rmdir("$path/$file");
                }else{
                    unlink("$path/$file");
                }
            }
        }
    }

    /**
     * 创建目录
     * @param $structure
     * @param int $mode
     * @param bool|false $force
     * @return bool
     */
    public static function createDir($structure, $mode = 0755, $force = false)
    {
        if (is_dir($structure) || $structure=='')
        {
            return true;
        }
        if (is_file($structure))
        {
            if (!$force || !@unlink($structure))
            {
                return false;
            }
        }
        if (self::createDir(dirname($structure), $mode, $force))
        {
            return @mkdir($structure, $mode);
        }
        else
        {
            return false;
        }
    }

    /**
     *是否可以提交
     */
    public static function cansend($user_id,$time=10){
        $session = Yii::$app->session;
        $sessionKey = $user_id.'_is_sending';
        if(isset($session[$sessionKey])){
            $first_submit_time = $session[$sessionKey];
            $current_time      = time();
            if($current_time - $first_submit_time < $time){
                $session[$sessionKey] = $current_time;
                return false;
            }else{
                unset($session[$sessionKey]);//超过限制时间，释放session";
                return true;
            }
        }else{
            //第一次点击确认按钮时执行
            $session[$sessionKey] = time();
            return true;
        }

    }
    /**
     * 字符串插入特定字符
     * @param $str
     * @param $pos
     * @param $instr
     * @return string
     */
    function insertstr($str, $pos, $instr) {
        return substr ( $str, 0, $pos ) . $instr . substr ( $str, $pos, strlen ( $str ) );
    }

    public function isLock($id)
    {
        $cache = Yii::$app->cache;
        $key = 'lock_'.$this->id.'_'.$id;
        $value = $cache->get($key);
        if ($value && $value['lockedby'] != Yii::$app->session->get('__id')) {
            Yii::$app->session->setFlash('tips', '<span id="message" style="color:#004eff;display: block;text-indent: 40%;">此条记录已锁定,请稍后再试</span>');
            return false ;//被锁定
        }else{
            return true;//可以编辑
        }
    }
    //生成提示
    public static function createtips($info='已更改',$bool='success',$title='tips',$pos='40%'){
        if ($bool=='success'){
            Yii::$app->session->setFlash($title,'<span id="message" style="color:red;display: block;text-indent: '.$pos.';">'.$info.'</span> '.'<script>$(\'#message\').click(function(){$(this).remove();});setTimeout(function(){$(\'#message\').trigger(\'click\');},2000);</script>');
        }else{
            Yii::$app->session->setFlash($title,'<span id="message" style="color:#2a55ff;display: block;text-indent:'.$pos.';">'.$info.'</span>'.'<script>$(\'#message\').click(function(){$(this).remove();});setTimeout(function(){$(\'#message\').trigger(\'click\');},2000);</script>');
        }

    }

    public function ajaxupload($model,$savepath=''){
        if (Yii::$app->request->isAjax){
            $savepath=$savepath?:'data/attachment/'.date('Ymd').'/';
            $data=$this->upload_file('file',$savepath,time().mt_rand(01,1000));
            if (isset($data['relative_path'])){
                $model->content_img=$data['relative_path'];
                if ($model->save()){
                    return ($data['relative_path']);
                }else{
                    return json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE);
                }
            }else{
                return 'error';
            }
        }
    }
    //秒换成算成小时分钟
    public static function secToTime($times,$type=1){
        $result = '00:00:00';
        if ($times>0) {
            $hour = floor($times/3600);
            $minute = floor(($times-3600 * $hour)/60);
            $second = floor((($times-3600 * $hour) - 60 * $minute) % 60);

            $hour=strlen($hour)==1?"0".$hour:$hour;
            $minute=strlen($minute)==1?"0".$minute:$minute;
            $second=strlen($second)==1?"0".$second:$second;
            if ($type==1){
                $result = $hour.':'.$minute.':'.$second;
            }else{
                $result = $hour.'时'.$minute.'分'.$second."秒";
            }
        }
        return $result;
    }
    //生成验证码
    public static function createCode($length=5){
        $code='';
        for ($i=0;$i<$length;$i++) {
            $code .= dechex(mt_rand(0,15));
        }
        return $code;
    }

    //二维数组给键去重
    public static function array_unset_tt($arr,$key){
        //建立一个目标数组
        $res = array();
        foreach ($arr as $value) {
            //查看有没有重复项
            if(!isset($res[$value[$key]])){
                $res[$value[$key]] = $value;
            }else{
                unset($value[$key]);
            }
        }
        return $res;
    }

    /**
     * 判断是否为关联数组
     * @param $arr
     * @return bool
     */
    public static function is_assoc($arr) {
        return array_keys($arr) == range(0, count($arr) - 1);
    }

    /**
     * 按json方式输出通信数据
     * @param integer $code 状态码
     * @param string $message 提示信息
     * @param array $data 数据
     * return string
     */
    public static function json($code, $message = '', $data = '') {

        if(!is_numeric($code)) {
            return '';
        }

        $result = array(
            'state'=>['rc'=>(int)$code,'msg'=>$message],

        );
        if (!empty($data)){
            $result['result']=$data;
        }
        return json_encode($result,256);
    }

    /**
     * 获取微妙数
     * @return float
     */
    public static function microtime_float()
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }


    /**
     * 截取两个字符之间的字符串
     * @param $str
     * @param $start
     * @param $end
     * @return string
     */
    public static function getBetween($str, $start, $end){
        $st=strripos($str, $start);
        $end=strripos($str,$end);
        return mb_substr($str, $st+1,$end-$st-1);
    }

    public static function exec($action){
         $action="/usr/local/php7/bin/php /home/v114/appserver/vcloud.com/vendor/yiisoft/yii2/yii ".$action;
         exec($action,$info,$status);
         return $status=!$status;

    }
}