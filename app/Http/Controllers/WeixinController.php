<?php

namespace App\Http\Controllers;

use App\Models\Record;
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
		info($postArr);
        if (!empty($postArr)) {
            $postObj = simplexml_load_string($postArr);
			$fromUsername = $postObj->FromUserName;
            $toUsername = $postObj->ToUserName;
            $content = trim($postObj->Content);
            $userMsgType = $postObj->MsgType;
            $time = time();
            $textTpl = "<xml>
            <ToUserName><![CDATA[%s]]></ToUserName>
            <FromUserName><![CDATA[%s]]></FromUserName>
            <CreateTime>%s</CreateTime>
            <MsgType><![CDATA[%s]]></MsgType>
            <Content><![CDATA[%s]]></Content>
            <FuncFlag>0<FuncFlag>
            </xml>";

            switch ($userMsgType) {
				case "text":
					$msgType = "text";
					if ($content == "纸尿裤") {
						$contentStr = json_encode($this->getRecord($fromUsername, 5));
					} else {
						$contentStr = '你好啊^_^快发语音记录吧~';
					}
					$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
					echo $resultStr;
					break;
				case "image":
					$msgType = "text";
					$contentStr = '收到你发的图片了，等我回复哦~';
					$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
					echo $resultStr;
					break;
				case "voice":
					$resultStr = $this->responseVoice($postObj, $textTpl);
					echo $resultStr;
					break;
				default:
					$msgType = "text";
					$contentStr = '不太懂你说的哦~';
					$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
					echo $resultStr;
			}
        } else {
            echo '你说了些什么哦?';
            exit;
        }
    }

    public function test()
	{

	}

    private function responseVoice(\SimpleXMLElement $xmlObj, $textTpl)
	{
		$msgType = "text";
		$mediaId = $xmlObj->MediaId;
		$format = $xmlObj->Format;
		$msgId = $xmlObj->MsgID;
		$recognition = $xmlObj->Recognition;

		$this->parseContent($xmlObj->FromUserName, 3, $msgId, $recognition);

		$contentStr = sprintf('收到你说的语音了(%s)，等我回复哦~', $recognition);
		$resultStr = sprintf($textTpl, $xmlObj->FromUserName, $xmlObj->ToUserName, time(), $msgType, $contentStr);
		return $resultStr;
	}

	private function parseContent($wxUid, $msgType, $msgId, string $recognition)
	{
		$record = new Record();
		$possibleSet = ['纸尿裤','尿布'];
		foreach ($possibleSet as $elem) {
			if (strstr($recognition, $elem)){
				$record->wx_uid = $wxUid;
				$record->msg_type = $msgType;
				$record->msg_id = $msgId;
				$record->raw = $recognition;
				$record->type = 5;
				$record->quantity = 1;
				$res = $record->save();
				if ($res) {
					return true;
				}
				return false;
			}
		}
		return false;

	}

	private function getRecord($wxUid, $type)
	{
		return Record::where("wx_uid", $wxUid)
				->where("type", $type);
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
