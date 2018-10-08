<?php
/**
 * Created by vary.
 * User: ASUS80
 * Date: 2018/9/29
 * Time: 15:53
 */

namespace api\modules\v1\controllers;


use api\controllers\ApiController;
use vary\curl;
use vary\myfunction;
use Yii;

class BaidubookmarkController extends ApiController
{
    public function actionCreatedir(){
        $redis=Yii::$app->redis;
        $curl=new curl();
        $curl->setOption(CURLOPT_COOKIE,'BAIDUID=842E0E34E118DD7847A155894F7D79AB:FG=1; BIDUPSID=842E0E34E118DD7847A155894F7D79AB; PSTM=1538206840; BDORZ=B490B5EBF6F3CD402E515D22BCDA1598; delPer=0; BDUSS=VpocmYzUjVOZlE2OXczdnlTRHlqRWJRalBwYkNpeUs4flRZTzJlYXlzbVh1OVpiQUFBQUFBJCQAAAAAAAAAAAEAAAAK3JY10M3E0F9zdHlsZQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAJcur1uXLq9bW; BD_HOME=1; H_PS_PSSID=1423_21088_18559_26350; BD_UPN=12314353');
        $curl->setOption(CURLOPT_REFERER,'https://www.baidu.com/');
        $url='http://www.baidu.com/';
        $curl->close=false;
        $curl->setUrl($url);
        $data=$curl->get();

        $pattern="/<span class=name-text.*>((?!<).*)<\/span>.*(<div.*dir-content.*del-dir)/isU";
        preg_match_all($pattern,$data,$matchs);
        $result=[];
        $pattern="/<a.*title=\"(.*)\".*href=\"(.*)\"/isU";
        $create_cate_url='https://www.baidu.com/home/subscribe/submit/manoperation';

        $newcookie=<<<cookie
BAIDUID=F6D138414F56122759FF7970E4352529:FG=1; BIDUPSID=F6D138414F56122759FF7970E4352529; PSTM=1538270297; delPer=0; BDUSS=NvZUZ4SDhiTG96Sk80bk5kWVpqdUR1aDFOUGYzWWNadTdZZ2xweE5UNXZzZGRiQVFBQUFBJCQAAAAAAAAAAAEAAABwjEUFvPvPsM~Ayr8AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAG8ksFtvJLBbND; BD_HOME=1; H_PS_PSSID=1433_21079_26350_20928; BD_UPN=12314353
cookie;

        $curl->setOption(CURLOPT_COOKIE,$newcookie);
        $curl->setUrl($create_cate_url);
        foreach ($matchs[2] as $key=>$match) {
            $need=[];
            preg_match_all($pattern,$match,$item);
            foreach ($item[1] as $i_key=>$val){
                $need[]=$item[1][$i_key].";".$item[2][$i_key];
            }
            $result[$matchs[1][$key]]=$need;
        }
        $error=[];
        foreach ($result as  $key=>&$item) {
            $curl->setOption(CURLOPT_COOKIE,$newcookie);
            $curl->setUrl($create_cate_url);
            $data=[
                'cmd'=>'add_dir',
                'dirName'=>$key,
                'tabid'=>1,
                'indextype'=>'manht',
                'bsToken'=>'53887830be8b71f63282aaff9a7d4bfb',
                '_req_seqid'=>'0xc9e6ddb600040933',
                'sid'=>'1433_21079_26350_20928',
            ];
            $return=$curl->post($data,'build');
            $return=json_decode($return,true);
            if($return['errNo']!=0){
                $error[$key]=$item;
                $redis->set('dir_error',json_encode($error,320));
            }else{
                $dir_id=$return['data']['dirId'];
                $item['dir_id']=$dir_id;
            }
        }
        $redis->set('dir_create',json_encode($result,320));
    }

    public  function actionCreateitem()
    {
        $data=Yii::$app->request->post();
        extract($data);
        $redis = Yii::$app->redis;
        $bookmark = json_decode($redis->get('dir_create'), true);
//        var_dump($bookmark);die();
        $cookie=$cookie??<<<cookie
BAIDUID=F6D138414F56122759FF7970E4352529:FG=1; BIDUPSID=F6D138414F56122759FF7970E4352529; PSTM=1538270297; delPer=0; BDUSS=NvZUZ4SDhiTG96Sk80bk5kWVpqdUR1aDFOUGYzWWNadTdZZ2xweE5UNXZzZGRiQVFBQUFBJCQAAAAAAAAAAAEAAABwjEUFvPvPsM~Ayr8AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAG8ksFtvJLBbND; BD_HOME=1; H_PS_PSSID=1433_21079_26350_20928; BD_UPN=12314353
cookie;

        $curl=new curl();
        $curl->setOption(CURLOPT_COOKIE,$cookie);
        $curl->close=false;

        foreach ($bookmark as $item) {
            if (isset($item['dir_id'])) {
                foreach ($item as $key => $i) {
                    if (is_numeric($key)) {
                        $i_array = explode(';', $i);
                        $name = $i_array[0];
                        $url = $i_array[1];
                        $data['cmd'] = 'add';
                        $data['from'] = 'u_layer';
                        $data['name'] = $name;
                        $data['url'] = $url;
                        $data['customDirId'] = $item['dir_id'];
                        $data['tabid']='1';
                        $data['indextype']='manht';
                        $data['_req_seqid'] = $qid??'0x9d28b81d00011e6a';
                        $data['bsToken']=$token??'53887830be8b71f63282aaff9a7d4bfb';
                        $data['sid'] = $sid??'1433_21079_26350_20928';
                        self::create_item($data, $curl);
                    }
                }
            }
        }
    }

    public static function create_item($data,curl $curl){
        $url='https://www.baidu.com/home/subscribe/submit/manoperation';
        $curl->setUrl($url);
        $result=$curl->post($data,'build');

    }

}