<?php
/**
 * Created by renfei.
 * User: vary
 * Date: 2018/1/5
 * Time: 9:52
 */
namespace vary;


use Illuminate\Support\Facades\Log;
use source\LuLu;
use yii\helpers\Url;

class myfunction{
    public static function log($info){
        $uri=$_SERVER['REQUEST_URI']??$_SERVER['argv'];
        $addr=$_SERVER['REMOTE_ADDR']??'';
        $method=$_SERVER['REQUEST_METHOD']??'console';
        Log::info(['info'=>$info,'url'=>$uri,'ip'=>$addr,'method'=>$method]);

    }
    public static function mydate($timestamps){
        return date("Y-m-d H:i:s",$timestamps);
    }
    public static function mystamps($date){
        return strtotime($date);
    }
    public static function go($url){
        $url=Url::to($url);
        exit('<script>window.location.href=\''.$url.'\'</script>');
    }
    public static function historyGo($num){
        exit('<script>history.go("'.$num.'")</script>');
    }
    //秒换成算成小时分钟
    public static function secToTime($times,$type=1,$useday=0){
        $result = '00:00:00';
        if ($times>0) {
            $day=floor($times/(3600*24));
            if($useday && $type==2){
                $times=$times-3600*24*$day;
            }

            $hour = floor($times/3600);
            $minute = floor(($times-3600 * $hour)/60);
            $second = floor((($times-3600 * $hour) - 60 * $minute) % 60);

            $day=strlen($day)==1?"0".$day:$day;
            $hour=strlen($hour)==1?"0".$hour:$hour;
            $minute=strlen($minute)==1?"0".$minute:$minute;
            $second=strlen($second)==1?"0".$second:$second;


            if ($type==1){
                $result = $hour.':'.$minute.':'.$second;
            }else{
                $first=$useday?$day.'天':'';
                $result = $first.$hour.'时'.$minute.'分'.$second."秒";
            }
        }
        return $result;
    }

    public static function dealXMl($xml){
        $xml_rs=xml_parser_create();
        xml_parse_into_struct($xml_rs,$xml,$data,$index);
        xml_parser_free($xml_rs);
        $need=[];
        foreach ($index as $key=> $value) {
            $need[strtolower($key)]=trim($data[$index[$key][0]]['value']??'');
        }
        return $need;
    }
    public static function arrayToXml($arr)
    {
        $xml = "<xml>";
        foreach ($arr as $key=>$val)
        {
            if (is_numeric($val)){
                $xml.="<".$key.">".$val."</".$key.">";
            }else{
                $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
            }
        }
        $xml.="</xml>";
        return $xml;
    }

    /**
     * 映射表,先查询到产品id:old ,新的产品id:new
     * 返回需要删除的id,添加的id
     * @param $old
     * @param $new
     * @return mixed
     */
    public static function myArrayDiff($old, $new){
        $need['delete']=array_values(array_diff($old, $new));
        $need['insert']=array_values(array_diff($new, $old));
        return $need;
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

    /**
     * 生成缩略图
     * @param $source
     * @param string $thumb
     * @param int $width
     * @param int $height
     * @return array|string
     */
    public static function makeThumb($source,$thumb='thumb',$width=200,$height=200){
        $target=dirname($source)."/".self::getBetween($source,"/",'.').$thumb.".png";
        list($swdith,$sheight,$type)=getimagesize($source);
        $map=[1=>'imagecreatefromgif',
            2=>'imagecreatefromjpeg',
            3=>'imagecreatefrompng',
            4=>'imagecreatefromwbmp'
        ];

        if (!isset($map[$type])){
            return ['rc'=>-1,'msg'=>'图片错误'];
        }
        $function=$map[$type];
        $source_img=$function($source);

        $small=imagecreatetruecolor($width,$height);
        $white=imagecolorallocate($small,255,255,255);
        imagefill($small,0,0,$white);

        $rate=min($width/$swdith,$height/$sheight);
        $rw=$swdith*$rate;
        $rh=$sheight*$rate;

//        imagecopyresampled($small,$source_img,($width-$rw)/2,($height-$rh)/2,0,0,$rw,$rh,$swdith,$sheight);
        imagecopyresampled($small,$source_img,($width-$rw)/2,0,0,0,$rw,$rh,$swdith,$sheight);
        imagepng($small,$target);
        imagedestroy($source_img);
        imagedestroy($small);
        return $target;
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
        return json_encode($result,320);
    }


    public static function send_tcp_message($host, $port, $message)
    {
        $message = $message."\r\n";
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        $msg=socket_connect($socket, $host, $port);

        $length = strlen($message);
        socket_write($socket,$message,$length);
        socket_close($socket);
        return $msg;

    }

    public  static function randomCode($length = 4, $numeric = 0) {


        if($numeric)
        {
            $hash = sprintf('%0'.$length.'d', mt_rand(0, pow(10, $length) - 1));
        }
        else
        {
            $hash = '';
            $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789abcdefghjkmnpqrstuvwxyz';
            $max = strlen($chars) - 1;
            for($i = 0; $i < $length; $i++)
            {
                $hash .= $chars[mt_rand(0, $max)];
            }
        }
        return $hash;
    }
    /**
     * 微信支付生成签名
     * @return 签名
     */
    public static function wechatPaySign($data,$pay_api_key){
        //获取微信支付秘钥
        $key =$pay_api_key;
        // 去空
        $data=array_filter($data);
        //签名步骤一：按字典序排序参数
        ksort($data);
        $string_a=http_build_query($data);
        $string_a=urldecode($string_a);
        //签名步骤二：在string后加入KEY
        //$config=$this->config;
        $string_sign_temp=$string_a."&key=".$key;
        //签名步骤三：MD5加密
        $sign = md5($string_sign_temp);
        // 签名步骤四：所有字符转为大写
        $result=strtoupper($sign);
        return $result;
    }

    public static function create_img($src,$make_thumb=0,$d_width=200){

        list($qr_width_old, $qr_height_old,$type) = getimagesize($src);

        $map=[1=>'imagecreatefromgif',
            2=>'imagecreatefromjpeg',
            3=>'imagecreatefrompng',
            4=>'imagecreatefromwbmp'
        ];

        if (!isset($map[$type])){
            return ['rc'=>-1,'msg'=>'图片错误'];
        }
        $function=$map[$type];
        $src_img=$function($src);
        if($make_thumb){
            //缩小二维码图片
            $per=round($d_width/$qr_width_old,3);
            $n_w=$qr_width_old*$per;
            $n_h=$qr_height_old*$per;
            $new_img=imagecreatetruecolor($n_w, $n_h);//生成黑图
            imagecopyresampled($new_img, $src_img,0, 0,0, 0,$n_w, $n_h, $qr_width_old, $qr_height_old);
        }
        //将二维码贴到原图上
//        imagecopymerge($bigImg['src_img'], $thumb['new_img'], $bigImg['src_width']/3.529,  $bigImg['src_height']/2.328, 0, 0, $thumb['new_width'], $thumb['new_height'], 100);

        //根据下面的代码生成文字
//        switch ($bigImg['type']) {
//            case 1: //gif
//                header('Content-Type:image/gif');
//                imagegif($bg_img);
//                break;
//            case 2: //jpg
//                header('Content-Type:image/jpg');
//                imagejpeg($bg_img);
//                break;
//            case 3: //jpg
//                header('Content-Type:image/png');
//                imagepng($bg_img);
//                break;
//            default:
//                # code...
//                break;
//        }

        //生成缩略图
        return ['type'=>$type,'new_img'=>$new_img??'','src_img'=>$src_img,'new_width'=>$n_w??'','new_height'=>$n_h??'','src_width'=>$qr_width_old,'src_height'=>$qr_height_old];
    }
    //生成文字
    public static function create_text($src,$x,$y,$text,$size=12,$red=0,$grn=0,$blu=0){
//        标题: 49 49 49
//        标题: 49 49 49
        $font=public_path().'/uploads/rifty/myfont.ttf';
        $textImg = imagecolorallocate($src, $red, $grn, $blu); // 创建白色
        imagettftext($src, $size, 0, $x,  $y, $textImg, $font, $text);
        return $src;
    }
    public static function create_text1($src,$x,$y,$text,$color,$size=12,$font='yahei.ttc'){
//        标题: 49 49 49
//        标题: 49 49 49
        $font=public_path().'/uploads/rifty/'.$font;
        imagettftext($src, $size, 0, $x,  $y+22, $color, $font, $text);
        return $src;
    }


    public  static function autowrap($fontsize,  $text, $width,$angle=0) {
        $fontface=public_path().'/uploads/rifty/myfont.ttf';
        $content = "";
// 将字符串拆分成一个个单字 保存到数组 letter 中
        preg_match_all("/./u", $text, $arr);
        $letter = $arr[0];
        foreach ($letter as $l) {
            $teststr = $content." ".$l;
            $testbox = imagettfbbox($fontsize, $angle, $fontface, $teststr);
// 判断拼接后的字符串是否超过预设的宽度
            if (($testbox[2] > $width) && ($content !== "")) {
                $content .= PHP_EOL;
            }
            $content .= $l;
        }
        return $content;
    }

    /**
     * 合并图片
     */
    public static function merge_img($img_url=null,$qr_url=null,$title=null,$desc=null,$price=null)
    {
//        var_dump($img_url,$qr_url,$title,$desc,$price);die();
        $bigImgPath = 'https://file.iqyone.com/rifty/short.png';
        $qr_url = $qr_url??'http://qr.iqyone.com/uploads/picture/QR/1528971209qr.png';
        $img_url=$img_url??'http://qr.iqyone.com/uploads/picture/20180614/87fe8b1437c515e5d28316b3c5870f53.jpeg';
//        $img_url=$img_url??'http://img.hb.aicdn.com/bcc3815315ea6d007ba4bd188223b23fe7068316c222-uOUwVA_fw658';
        $bigImg = self::create_img($bigImgPath,0);

//        $qr_img=imagecreatetruecolor($n_w, $n_h);//生成黑图

        //生成标题
        $title="【".mb_substr(($title??"帮主,请给才哥带回来"),0,15)."】";
        $bigImg['src_img']=self::create_text($bigImg['src_img'],$bigImg['src_width']*0.072,$bigImg['src_height']*0.0667,$title,28);
        //生成价格
        $price=$price??"6700.00";
        $bigImg['src_img']=self::create_text($bigImg['src_img'],$bigImg['src_width']*0.15,$bigImg['src_height']*0.152,$price,51,223,0,16);
        //生成描述
        $desc="小编推荐:".mb_substr(($desc??"玉质油润度高，糖色浓郁，糖色巧雕，远处层山叠嶂，近处童子坐于大象之上，俏皮可爱，右侧雕刻椰子树，椰岛风情跃然于眼前。使人心旷神怡。"),0,70);
        $data=self::autowrap(19,$desc,$bigImg['src_width']*0.95);
        $bigImg['src_img']=self::create_text($bigImg['src_img'],$bigImg['src_width']*0.04,$bigImg['src_height']*0.212,$data,19,243,88,104);


        //缩小二维码
        $thumb=self::create_img($qr_url,1,$bigImg['src_width']/2.3);
        //生成二维码缩略图
        imagecopyresampled($thumb['new_img'], $thumb['src_img'],0, 0,0, 0,$thumb['new_width'], $thumb['new_height'], $thumb['src_width'], $thumb['src_height']);
        //将二维码贴到原图上
        imagecopymerge($bigImg['src_img'], $thumb['new_img'], $bigImg['src_width']/3.529,  $bigImg['src_height']/2.328, 0, 0, $thumb['new_width'], $thumb['new_height'], 100);


        //缩小内容图片
        $thumb_content=self::create_img($img_url,1,$bigImg['src_width']);
        imagecopyresampled($thumb_content['new_img'], $thumb_content['src_img'],0, 0,0, 0,$thumb_content['new_width'], $thumb_content['new_height'], $thumb_content['src_width'], $thumb_content['src_height']);

        //生成幕布
        $bg_height=$thumb_content['new_height']+$bigImg['src_height'];
        $bg_width=$bigImg['src_width'];
        $bg_img=imagecreatetruecolor($bg_width,$bg_height);

        //将内容图合并到幕布上
        imagecopymerge($bg_img, $thumb_content['new_img'], 0, 0, 0, 0, $thumb_content['new_width'], $thumb_content['new_height'], 100);

        //将合并二维码后的底部图片合并到幕布上
        imagecopymerge($bg_img, $bigImg['src_img'], 0, $thumb_content['new_height'], 0, 0, $bg_width, $bg_height, 100);



        $relative_path='/uploads/createimg/'.uniqid().'.png';
        $path=(public_path().$relative_path);
        switch ($bigImg['type']) {
            case 1: //gif
//                header('Content-Type:image/gif');
                imagegif($bg_img,$path);
                break;
            case 2: //jpg
//                header('Content-Type:image/jpg');
                imagejpeg($bg_img,$path);
                break;
            case 3: //jpg
//                header('Content-Type:image/png');
                imagepng($bg_img,$path);
                break;
            default:
                # code...
                break;
        }
        imagedestroy($bg_img);
        imagedestroy($bigImg['src_img']);
        imagedestroy($thumb['new_img']);
        imagedestroy($thumb['src_img']);
        return "https://".request()->getHost().$relative_path;
    }

    public static function mk_color($pic,$r,$g,$b){
//        $c1 = mt_rand(50,200); //r(ed)
//        $c2 = mt_rand(50,200); //g(reen)
//        $c3 = mt_rand(50,200); //b(lue)
        //test if we have used up palette
        if(imagecolorstotal($pic)>=255) {
            //palette used up; pick closest assigned color
            $color = imagecolorclosest($pic, $r, $g, $b);
        } else {
            //palette NOT used up; assign new color
            $color = imagecolorallocate($pic, $r, $g, $b);
        }
        return $color;
    }

    public static function pmauctionimg($data){
        extract($data);

        $tpl_url='https://file.iqyone.com/common/20180622/5b2c9c4bdd3f9.png';
        if (isset($img_url)){
            $img_url='http://file.iqyone.com/'.$img_url;
        }else{
            $img_url='http://file.iqyone.com/file/066c402972bfd10cf386c8de77ecc98c.jpeg';
        }

        $tpl=self::create_img($tpl_url);




        //为模板添加文字
        $black=self::mk_color($tpl['src_img'],0,0,0);
        $gray=self::mk_color($tpl['src_img'],100,100,100);
        $bt_color=self::mk_color($tpl['src_img'],27,27,27);
        //标题
        $title=self::autowrap(28,mb_substr($title??'神器的神神器的神器的神器的神器的神器的神器的神器的神器的神器的神器的神器的神器的器的',0,25),$tpl['src_width']-60);
        self::create_text1($tpl['src_img'],28,30,$title,$bt_color,28);


        //起拍价
        self::create_text1($tpl['src_img'],163,147,(int)($floor_price??'6666'),$black,22);

        //当前价
        self::create_text1($tpl['src_img'],165,186,(int)($current_price??'9999'),$black,27,'yaheicu.ttc');

        //拍卖时间
        $start_time=date('Y.m.d H:i',strtotime($start_time??date('Y-m-d H:i:s')));
        self::create_text1($tpl['src_img'],150,260,$start_time,$gray,19);





//        header('Content-Type:image/png');
//
//
//        imagejpeg($tpl['src_img']);die();

        //缩小内容图片
        $thumb_content=self::create_img($img_url,1,610);
        imagecopyresampled($thumb_content['new_img'], $thumb_content['src_img'],0, 0,0, 0,$thumb_content['new_width'], $thumb_content['new_height'], $thumb_content['src_width'], $thumb_content['src_height']);



       //生成幕布
        $bg_height=$thumb_content['new_height']+$tpl['src_height'];
        $bg_width=$tpl['src_width'];
        $bg_img=imagecreatetruecolor($bg_width,$bg_height);

        //填充颜色
        $white=imagecolorallocate($bg_img,255,255,255);
        imagefill($bg_img,0,0,$white);

       //将内容图合并到幕布上
        imagecopymerge($bg_img, $thumb_content['new_img'], 20, 20, 0, 0, $thumb_content['new_width'], $thumb_content['new_height'], 100);

        //将模板合并到幕布
        imagecopymerge($bg_img, $tpl['src_img'], 0, $thumb_content['new_height'], 0, 0, $tpl['src_width'], $tpl['src_height'], 100);


        switch ($tpl['type']) {
            case 1: //gif
                header('Content-Type:image/gif');
                imagegif($bg_img);
                break;
            case 2: //jpg
                header('Content-Type:image/jpg');
                imagejpeg($bg_img);
                break;
            case 3: //jpg
                header('Content-Type:image/png');
                imagepng($bg_img);
                break;
            default:
                # code...
                break;
        }

        imagedestroy($bg_img);
        imagedestroy($tpl['src_img']);
        imagedestroy($thumb_content['src_img']);
        imagedestroy($thumb_content['new_img']);




    }

    public static function show_img($url)
    {
        $img=myfunction::create_img($url);

        switch ($img['type']) {
            case 1: //gif
                header('Content-Type:image/gif');
                imagegif($img['src_img']);
                break;
            case 2: //jpg
                header('Content-Type:image/jpeg');
                imagejpeg($img['src_img']);
                break;
            case 3: //jpg
                header('Content-Type:image/png');
                imagepng($img['src_img']);
                break;
            default:
                # code...
                break;
        }

        imagedestroy($img['src_img']);
    }

    public static function dealUrl($avatarUrl)
    {
        $re=strpos($avatarUrl,'http');
        $prefix=config('admin.upload.host');

        if($re===false){
            $url=$prefix.$avatarUrl;
        }else{
            $url=$avatarUrl;
        }
        return $url;
    }

    public static function createNumber()
    {
        $micr = explode(".", microtime(true));
        $new_end = str_pad($micr[1], 4, "0", 0);
        return date('His') . mt_rand(0, 9) . $new_end . mt_rand(1, 8);
    }

    //毫秒
    public static function microtime()
    {
        $time = explode(" ", microtime());
        $time = $time [1] . ($time [0] * 1000);
        $time2 = explode(".", $time);
        $time = $time2 [0];
        return $time;
    }


    /**
     *
     *             $prize_arr = array(
    '0' => array('id'=>1,'prize'=>'平板电脑','v'=>1),
    '1' => array('id'=>2,'prize'=>'数码相机','v'=>2),
    '2' => array('id'=>3,'prize'=>'音箱设备','v'=>3),
    '3' => array('id'=>4,'prize'=>'4G优盘','v'=>4),
    '4' => array('id'=>5,'prize'=>'10Q币','v'=>20),
    '5' => array('id'=>6,'prize'=>'下次没准就能中哦','v'=>10)
    );

    foreach ($prize_arr as $key => $val) {
    $arr[$val['id']] = $val['v'];
    }
    $rid=$this->get_rand($arr);

    $res['yes'] = $prize_arr[$rid-1]['prize']; //中奖项
    unset($prize_arr[$rid-1]); //将中奖项从数组中剔除，剩下未中奖项
    dump($prize_arr);
    foreach ($prize_arr as $prize) {
    $pr[] = $prize['prize'];
    }

    $res['no'] = $pr;
     * 抽奖
     * @param $proArr
     * @return int|string
     */
    public function get_rand($proArr)
    {
        $result = '';
        //概率数组的总概率精度
        $proSum = array_sum($proArr);
        //概率数组循环
        foreach ($proArr as $key => $proCur) {
            $randNum = mt_rand(1, $proSum);

            if ($randNum <= $proCur) {
                $result = $key;
                break;
            } else {
                $proSum -= $proCur;
            }
        }
        unset ($proArr);
        return $result;
    }




    /* 接口加密规则
        * 将参数按键升序后拼接成字符串
        * 空参数不参与排序
        * MD5该字符串后赋值给token发送到接口
        * PrivateKey:5LbmNaymeF7zvjtJyH5Ce7LhynuJ9L8A
        * 加密示例：MD5("PrivateKey&a=1&b=2&PrivateKey")
        */

    public  static function  sign($data_list,$key='h91hs8rjvejh124hv8dfh23jsenf') {

        unset($data_list['token']);
        unset($data_list['s']);
        unset($data_list['callback']);
        unset($data_list['sign']);

        ksort($data_list);

        $str = '';

        foreach($data_list as $keys => $value)
        {
            if (mb_strlen(trim($value)) == 0 )
            {
                continue;
            }

            $str .= $keys.'='.$value.'&';
        }
        $str = $str.$key;
//        dump(mb_strlen($str));
        return md5($str);
    }



















}