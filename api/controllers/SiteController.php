<?php
/**
 * Created by vary.
 * User: ASUS80
 * Date: 2018/9/25
 * Time: 16:11
 */

namespace api\controllers;


use Redis;
use source\core\base\BaseController;
use source\modules\download\models\Download;
use vary\curl;
use vary\myfunction;
use Yii;

class SiteController extends BaseController
{

    private static function download($url,$dir='',$filename='123.jpg')
    {
        set_time_limit(0);
//        $header = get_headers($url, 1);
//        $size = $header['Content-Length'];

        $dir=$_SERVER['DOCUMENT_ROOT'].'/statics/common/download/'.$dir;
        if(!file_exists($dir) ){
            mkdir($dir,'0777',true);
        }
        $filename=$dir.$filename;
//        die();
        $hostfile = fopen($url, 'r');
        $fh = fopen($filename, 'w');
        while (!feof($hostfile)) {
            $output = fread($hostfile, 8192);
            fwrite($fh, $output);
        }
        fclose($hostfile);
        fclose($fh);
        return true;
    }

    public function actionIndex()
    {
        $str = <<<str
<a href="javascript:void(0)" class="button light" onclick="free_down('214707137', 0, 'f7c17fd29315e787b2e47f193d39346b', 0, 0)" id="free_down_link"><i class="icon ico-normal"></i><em>普通下载</em></a>
str;

        return myfunction::json(1, 'eee');
    }

    public function acitonTest(){
        $redis = new Redis();
        $redis->connect('192.168.1.12');
        $task = [
            'task'=>'send_email',
            'data'=>'你好，隔壁老王',
        ];
        $redis->publish('task_queue', serialize($task));

    }


    public function actionGit()
    {
        $result = system('sh ' . $_SERVER['DOCUMENT_ROOT'] . '/crontab/git.sh');
//        $result=system('echo 2');
    }

    /**
     * 向数据库插入数据
     * @param null $url
     * @return array
     */
    public function actionDown($url=null)
    {
        set_time_limit(0);

        $url=$url??Yii::$app->request->get('url');
        $curl = new curl($url);
        $curl->close=false;
        $data = $curl->get();
        $patter = "/free_down\('(\d+)'.*(\w+).*'([\w\d]+)'.*0\)/";
        preg_match_all($patter, $data, $matchs);

        //匹配目录名称
        $patter="/active.*>(.*)<\/a>/";
        preg_match_all($patter,$data,$dir);
        $dir=$dir[1][0];


        $fid = $matchs[1][0];
        $folder_id = $matchs[2][0];
        $check = $matchs[3][0];
        $url = 'https://dayo1982.ctfile.com/get_file_url.php?uid=1738248&fid=' . $fid . '&folder_id=' . $folder_id . '&file_chk=' . $check . '&mb=0&app=0&verifycode=&rd=0.24565890485711717';
        $curl->setUrl($url);
        $down = $curl->get();
        $down = json_decode($down, true);
        if(isset($down['downurl'])){
            $downurl = $down['downurl'];
            preg_match_all("/.*\/(.+)\?/", $downurl, $filename);
            $filename = urldecode($filename[1][0]??'获取失败');
            //向数据库插入
            $data= ['filename'=>$filename,'downurl'=>$downurl,'dirname'=>$dir];
            $bool=Download::insertData($data);
            $data['status']=$bool;
            return $data;
        }

//        self::download($downurl,$filename);

    }

    public function actionGetdownloadurl(){
        $url=Yii::$app->request->get('url');
        if(!$url){
            return 'Params Error!!!';
        }
        $curl = new curl($url);
        $curl->close=false;
        $curl->setOption(CURLOPT_REFERER,$url);
        $curl->setOption(CURLOPT_COOKIE,'clicktopay=1537859364620; UM_distinctid=1660f8fc1702c9-0c6a174947d086-404c0328-1fa400-1660f8fc173347; protected_uid=1744823380; PHPSESSID=o9ru0nj0e88lopghbd71clcj82; ua_checkmutilogin=toAlPLwX3y; CNZZDATA3231392=cnzz_eid%3D737261554-1537853978-https%253A%252F%252Fdayo1982.ctfile.com%252F%26ntime%3D1537943359; Hm_lvt_74590c71164d9fba556697bee04ad65c=1537859317,1537945883; Hm_lpvt_74590c71164d9fba556697bee04ad65c=1537948406');
        $data=$curl->get();

        preg_match_all("/sAjaxSource.*\/(.*)\"/",$data,$matchs);
        $queryurl=$matchs[1][0];

        preg_match_all("/(https:\/\/.*\/)u\/(\d+)\/(\d+)/",$url,$matchs);
        $domain=$matchs[1][0];


        $need=[];
        $url=$domain.$queryurl;
        $curl->setUrl($url);
        $data = $curl->get();
        $data=json_decode($data,true);
        foreach ($data['aaData'] as $item) {
            preg_match("/\/(i\/.*)\"/",$item[1],$matchs);
            if($matchs){
                $url=$domain.$matchs[1];
                $data=self::actionDown($url);
                $need[]=$data;
            }
        }
        return json_encode($need,320);
    }

    public function actionDownload(){
        $data=Yii::$app->request->post();
        $url=$data['url'];
        $dir=$data['dir'];
        $filename=$data['filename'];
        $bool=self::download($url,$dir,$filename);
//        $bool=1;
        return $bool?1:0;
    }

    public function actionAutodownload(){
        set_time_limit(0);
        $task=Download::find()->where(['download_status'=>0])->limit(2)->all();


        foreach ($task as $t) {
            $url=$t->download_url;
            $dir=$t->dirname;
            $filename=$t->filename;
            $curl=new curl();
            $curl->setOption(CURLOPT_TIMEOUT,1);
            $downurl='http://crm.com/api/site/download';
            $curl->setUrl($downurl);
            $post=[
                'url'=>$url,
                'dir'=>$dir.'/',
                'filename'=>$filename,
            ];
            $data=$curl->post($post,'x-form-urlencode');
            if($data){
                $t->download_status=1;
                $t->save();
                return $filename.'下载成功';
            }
        }


    }
}