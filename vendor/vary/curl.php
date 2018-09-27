<?php
namespace vary;

class curl
{
    public $url;
    public $ch;
    public $msg;
    public $close=true;
    function __construct($url='')
    {
        $this->ch=curl_init();
        $this->url=$url;
    }
    public function setUrl($url){
        $this->url=$url;
        return $this;
    }
    public function setOption($key=CURLOPT_REFERER,$value=''){
        curl_setopt($this->ch, $key, $value);
    }

	public function get($bool = true){
		$ch = $this->ch;
		curl_setopt($ch,CURLOPT_URL,$this->url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, $bool);
        $rs = curl_exec($ch);
		$errno=curl_errno($ch);
		$error=curl_error($ch);
        $errorinfo=curl_getinfo($ch);
        $this->msg=[$error,$errorinfo,$errno];
        if($this->close){
       		curl_close($ch);
        }
		if($errno==0){
			return $rs;
		}else{
            var_dump($errorinfo);
			return 'curl错误,错误代码:'.$errno.';错误信息:'.$error;
		}
	}

	public function post($post ,$type,$bool = true){
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,$this->url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, $bool);
		curl_setopt($ch, CURLOPT_POST, true);
        if ($type=='json'){
            $post=json_encode($post,320);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($post))
            );


        }
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
        curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
        $rs = curl_exec($ch);
		$errno=curl_errno($ch);
		$error=curl_error($ch);
        if($this->close){
            curl_close($ch);
        }
		if($errno==0){
			return $rs;
		}else{
			return 'curl错误,错误代码:'.$errno.';错误信息:'.$error;
		}
	}

    public function postWithPem($post ,$type,$path=[],$bool = true){
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$this->url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, $bool);
        curl_setopt($ch, CURLOPT_POST, true);
        if ($type=='json'){
            $post=json_encode($post,320);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($post))
            );

        }elseif ($type=='pem'){
            curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
            curl_setopt($ch,CURLOPT_SSLCERT, $path['cert']??'');
            curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
            curl_setopt($ch,CURLOPT_SSLKEY, $path['key']??'');
        }
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
        curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);

        $rs = curl_exec($ch);
        $errno=curl_errno($ch);
        $error=curl_error($ch);
        if($this->close){
            curl_close($ch);
        }
        if($errno==0){
            return $rs;
        }else{
            return 'curl错误,错误代码:'.$errno.';错误信息:'.$error;
        }
    }

    function access_token_get(){
        $path=dirname(__FILE__);
        $file=$path.'/token.txt';
        if(!file_exists($file) || time()-filemtime($file)>7000){

            $this->url="http://tv.butel.com/webapi/account/authorize";
            $data=json_encode(['appid'=>'34646396ef8d407a']);
            $rs = $this->post($data);
            file_put_contents($file, $rs);
        }else{
            $rs = file_get_contents($file);
        }
        return  $rs;
    }
}

