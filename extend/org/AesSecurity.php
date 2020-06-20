<?php

namespace org;

/**
 * [AesSecurity aes加密，支持PHP7.1]
 */
class AesSecurity
{
    /**
     * [encrypt aes加密]
     * @param    [type]                   $input [要加密的数据]
     * @param    [type]                   $key   [加密key]
     * @return   [type]                          [加密后的数据]
     */
    public static function encrypt($input, $key)
    {
        $data = openssl_encrypt($input, 'AES-128-ECB', $key, OPENSSL_RAW_DATA);
        $data = base64_encode($data);
        return $data;
    }

    /**
     * [decrypt aes解密]
     * @param    [type]                   $sStr [要解密的数据]
     * @param    [type]                   $sKey [加密key]
     * @return   [type]                         [解密后的数据]
     */
    public static function decrypt($sStr, $sKey)
    {
        $decrypted = openssl_decrypt(base64_decode($sStr), 'AES-128-ECB', $sKey, OPENSSL_RAW_DATA);
        return $decrypted;
    }


    /**
     * 加密aeskey
     * @return string 加密后的的aeskey
     */
    public static function encryptAesKey()
    {
        $public_key = '-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCvR8H9mhPg6n0A950JSIXJKdLF
msrnEtuOTzDPoB2x53eCyLReGFqEW5AXJH4/Gn0Onz5rVnQI2eZlci3On/sW67zD
s8Vz6in6FzBDoQFyJ+P85QjJJ9xYZhCoHFxd8ihSVnKgSG2nU5bPixQkzXlvleWH
EvYgoSM2HXYp53QcKQIDAQAB
-----END PUBLIC KEY-----';

        $pu_key = openssl_pkey_get_public($public_key);

        openssl_public_encrypt('888888',$encrypted,$pu_key);//公钥加密
        $encrypted = base64_encode($encrypted);

        return $encrypted;
    }
}