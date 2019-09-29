<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class VerifyCodeController extends Controller
{
    private function getCode($length = 4){
        $length = ($length < 4) ? 4 : $length;
        $length = ($length > 8) ? 8 : $length;
        $code = [
            'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
            'a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z',
            '0','1','2','3','4','5','6','7','8','9'
        ];
        $result = '';
        while ($length) {
            $randKey = array_rand($code);
            $result .= $code[$randKey];
            $length--;
        }
        return $result;
    }

    public function genImgCode(){
        $width = 120;
        $height = 50;
        $image = imagecreate($width, $height);
        $background = imagecolorallocate($image, 100, 100, 100);
        $textColour = imagecolorallocate($image, 255, 255, 0);
        $lineColour = imagecolorallocate($image, 128, 255, 0);
//        imagestring($image, 10, 30, 25, $this->getCode(), $textColour);
        @imagefttext($image, 20 , 0, 20, 35, $textColour, 'C:\Users\86150\Desktop\font20900\SF Old Republic Italic.woff.ttf',$this->getCode());
        $snowflakeSize = 1;  //1到5
        //利用循环生成雪花
        for ($i=1; $i<=30; $i++){
            imagechar(
                $image,
                $snowflakeSize,
                mt_rand(0, $width),
                mt_rand(0, $height),
                "'",
                imagecolorallocate($image, mt_rand(200,255), mt_rand(200,255), mt_rand(200,255))
            );
            imagechar(
                $image,
                $snowflakeSize,
                mt_rand(0, $width),
                mt_rand(0, $height),
                "_",
                imagecolorallocate($image, mt_rand(200,255), mt_rand(200,255), mt_rand(200,255))
            );
            imagechar(
                $image,
                $snowflakeSize,
                mt_rand(0, $width),
                mt_rand(0, $height),
                "`",
                imagecolorallocate($image, mt_rand(200,255), mt_rand(200,255), mt_rand(200,255))
            );
        }

        header( "Content-type: image/png" );
        imagepng( $image );
        imagecolordeallocate( $lineColour );
        imagecolordeallocate( $textColour );
        imagecolordeallocate( $background );
        imagedestroy( $image );
    }
}
