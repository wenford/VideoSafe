<?php
class Aes{
    const KEY="625202f9149e061d";
    const IV ="5efd3f6060e20330";
    /**
     * pkcs7����
     * @param string $string  ����
     * @param int $blocksize Blocksize , �� byte Ϊ��λ
     * @return String
     */ 
    private function addPkcs7Padding($string, $blocksize = 32) {
        $len = strlen($string); //ȡ���ַ�������
        $pad = $blocksize - ($len % $blocksize); //ȡ�ò���ĳ���
        $string .= str_repeat(chr($pad), $pad); //��ASCII��Ϊ���볤�ȵ��ַ��� �������һ��
        return $string;
    }

    /**
     * ����Ȼ��base64ת��
     * 
     * @param String ����
     * @param ���ܵĳ�ʼ������IV�ĳ��ȱ����Blocksizeһ���� �Ҽ��ܺͽ���һ��Ҫ����ͬ��IV��
     * @param $key ��Կ
     */
    function aes256cbcEncrypt($str, $iv, $key ) {   
        return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $this->addPkcs7Padding($str) , MCRYPT_MODE_CBC, $iv));
    }

    /**
     * ��ȥpkcs7 padding
     * 
     * @param String ���ܺ�Ľ��
     * 
     * @return String
     */
    private function stripPkcs7Padding($string){
        $slast = ord(substr($string, -1));
        $slastc = chr($slast);
        $pcheck = substr($string, -$slast);

        if(preg_match("/$slastc{".$slast."}/", $string)){
            $string = substr($string, 0, strlen($string)-$slast);
            return $string;
        } else {
            return false;
        }
    }
    /**
     * ����
     * 
     * @param String $encryptedText �����Ƶ����� 
     * @param String $iv ����ʱ���IV
     * @param String $key ��Կ
     * @return String
     */
    function aes256cbcDecrypt($encryptedText, $iv, $key) {
        $encryptedText =base64_decode($encryptedText);
        return $this->stripPkcs7Padding(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $encryptedText, MCRYPT_MODE_CBC, $iv));
    }

    function aes128cbcDecrypt($encryptedText, $iv=self::IV, $key=self::KEY) {
        $encryptedText =base64_decode($encryptedText);
        return $this->stripPkcs7Padding(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $encryptedText, MCRYPT_MODE_CBC, $iv));
    }

    function hexToStr($hex)//ʮ������ת�ַ���
    {   
        $string=""; 
        for($i=0;$i<strlen($hex)-1;$i+=2)
        $string.=chr(hexdec($hex[$i].$hex[$i+1]));
        return  $string;
    }
    function strToHex($string)//�ַ���תʮ������
    { 
        $hex="";
        $tmp="";
        for($i=0;$i<strlen($string);$i++)
        {
            $tmp = dechex(ord($string[$i]));
            $hex.= strlen($tmp) == 1 ? "0".$tmp : $tmp;
        }
        $hex=strtoupper($hex);
        return $hex;
    }
    function aes128cbcHexDecrypt($encryptedText, $iv=self::IV, $key=self::KEY) {
        $str = $this->hexToStr($encryptedText);
        return $this->stripPkcs7Padding(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $str, MCRYPT_MODE_CBC, $iv));
    }

    function aes128cbcEncrypt($str, $iv=self::IV, $key=self::KEY ) {    // $this->addPkcs7Padding($str,16)
        $base = (mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key,$this->addPkcs7Padding($str,16) , MCRYPT_MODE_CBC, $iv));
        return $this->strToHex($base);
    }
}

/*$aes = new Aes;
echo $aes->aes128cbcEncrypt("11122222");
echo "\n";
echo $aes->aes128cbcEncrypt("����aes��ʾ");
echo "\n";*/
