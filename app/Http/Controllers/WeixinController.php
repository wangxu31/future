<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Monolog\Logger;
use Illuminate\Log;
define('TOKEN','clive31');

class WeixinController extends Controller
{
    public function index()
    {
        //如果相等，验证成功就返回echostr
        if ($this->checkSignature()) {
            //返回echostr
            $echoStr = $_GET['echostr'];
            if ($echoStr) {
                echo $echoStr;
                exit;
            }
        }
    }

    /*public function handleMsg()
    {
        //signature=e5dcaf8f1f56dec02822f23bb265a5e4829affd4
        //&timestamp=1553937344
        //&nonce=1287316443
        //&openid=o9O7PwKec9bZRRejpiyFlAL_Sgwk
        //&encrypt_type=aes
        //&msg_signature=1d88d14cf818662f2e9e5430b4a09655d29ecdc0
        var_dump($_REQUEST);
        log("info", json_encode($_REQUEST));
    }*/

    public function handleMsg()
    {
		//php7.0只能用这种方式获取数据，之前的$GLOBALS['HTTP_RAW_POST_DATA']7.0版本不可用
		$postArr = file_get_contents("php://input");
        if (!empty($postArr)) {
            $postObj = simplexml_load_string($postArr);
			$fromUsername = $postObj->FromUserName;
            $toUsername = $postObj->ToUserName;
            $keyword = trim($postObj->Content);
            $time = time();
            $textTpl = "<xml>
            <ToUserName><![CDATA[%s]]></ToUserName>
            <FromUserName><![CDATA[%s]]></FromUserName>
            <CreateTime>%s</CreateTime>
            <MsgType><![CDATA[%s]]></MsgType>
            <Content><![CDATA[%s]]></Content>
            <FuncFlag>0<FuncFlag>
            </xml>";
            if (!empty($keyword)) {
                $msgType = "text";
                $contentStr = '你好啊<br/>^_^<br/>快发语音记录吧~';
                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                echo $resultStr;
            } else {
                echo '不太懂你说的哦~';
            }
        } else {
            echo '你说了些什么哦?';
            exit;
        }
    }

    //检查标签
    private function checkSignature()
    {
        //先获取到这三个参数
        $signature = $_GET['signature'];
        $nonce = $_GET['nonce'];
        $timestamp = $_GET['timestamp'];

        //把这三个参数存到一个数组里面
        $tmpArr = array($timestamp,$nonce,TOKEN);
        //进行字典排序
        sort($tmpArr);

        //把数组中的元素合并成字符串，impode()函数是用来将一个数组合并成字符串的
        $tmpStr = implode($tmpArr);

        //sha1加密，调用sha1函数
         $tmpStr = sha1($tmpStr);
        //判断加密后的字符串是否和signature相等
        if ($tmpStr == $signature) {
            return true;
        }
        return false;
    }
}
