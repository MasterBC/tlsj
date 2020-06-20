<?php

class Aes
{
    public static function encrypt($input, $key)
    {
        $size = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
        $input = Aes::pkcs5_pad($input, $size);
        $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_ECB, '');
        $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        mcrypt_generic_init($td, $key, $iv);
        $data = mcrypt_generic($td, $input);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        $data = base64_encode($data);
        return $data;
    }

    private static function pkcs5_pad($text, $blocksize)
    {
        $pad = $blocksize - (strlen($text) % $blocksize);
        return $text . str_repeat(chr($pad), $pad);
    }

    public static function decrypt($sStr, $sKey)
    {
        $decrypted = mcrypt_decrypt(
            MCRYPT_RIJNDAEL_128,
            $sKey,
            base64_decode($sStr),
            MCRYPT_MODE_ECB
        );

        $dec_s = strlen($decrypted);
        $padding = ord($decrypted[$dec_s - 1]);
        $decrypted = substr($decrypted, 0, -$padding);
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