<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Monolog\Logger;

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
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        Log::info($postStr);
        if (!empty($postStr)){
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
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
            if(!empty( $keyword ))
            {
                $msgType = "text";
                $contentStr = '你好啊，屌丝';
                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                echo $resultStr;
            }else{
                echo '咋不说哈呢';
            }
        }else {
            echo '咋不说哈呢';
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
