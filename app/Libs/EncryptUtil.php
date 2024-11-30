<?php

namespace App\Libs;

class EncryptUtil
{
    /**
     * Encrypt string use AES 256
     *
     * @param string $str
     * @return string|null
     */
    public static function encryptAes256($str) {
        if (! isset($str) || strlen($str) <= 0) {
            return null;
        }

        // Get constant value
        $keyLen = ValueUtil::get('common.aes_256_key');
        $method = ValueUtil::get('common.method_aes_256');
        $hashMethod = ValueUtil::get('common.method_hash');

        // Encrypt string
        $key = hash($hashMethod, $keyLen, true);
        $iv = base64_decode(ValueUtil::get('common.aes_256_iv'));
        $cipherText = openssl_encrypt($str, $method, $key, OPENSSL_RAW_DATA, $iv);

        return base64_encode($cipherText);
    }

    /**
     * Decrypt string use AES 256
     *
     * @param string $str
     * @return string|null
     */
    public static function decryptAes256($str) {
        if (! isset($str) || strlen($str) <= 0) {
            return null;
        }

        // Get constant value
        $decryptStr = base64_decode($str);
        $keyLen = ValueUtil::get('common.aes_256_key');
        $method = ValueUtil::get('common.method_aes_256');
        $hashMethod = ValueUtil::get('common.method_hash');
        $iv = base64_decode(ValueUtil::get('common.aes_256_iv'));

        // Parse key to decrypt
        $key = hash($hashMethod, $keyLen, true);

        return openssl_decrypt($decryptStr, $method, $key, OPENSSL_RAW_DATA, $iv);
    }

    /**
     * Encrypt string use SHA 256
     *
     * @param string $str
     * @return string|null
     */
    public static function encryptSha256($str) {
        if (! isset($str) || strlen($str) <= 0) {
            return null;
        }

        $salt = ValueUtil::get('common.sha256_salt');

        return hash('sha256', $salt . $str);
    }

    /**
     * Check string is same as sha-256 hashed string
     *
     * @param string $str
     * @param string $hashedStr
     * @return bool
     */
    public static function checkSha256($str, $hashedStr) {
        return self::encryptSha256($str) === $hashedStr;
    }

    /**
     * Encrypt string use urlencode and base64
     *
     * @param string $str
     * @return string|null
     */
    public static function encryptUrlBase64($str) {
        if (! isset($str) || strlen($str) <= 0) {
            return null;
        }

        $data = base64_encode($str);
        $data = str_replace(['+', '/', '='], ['-', '_', ''], $data);

        return $data;
    }

    /**
     * Decrypt string use urlencode and base64
     *
     * @param string $str
     * @return string|null
     */
    public static function decryptUrlBase64($str) {
        if (! isset($str) || strlen($str) <= 0) {
            return null;
        }

        $data = str_replace(['-', '_'], ['+', '/'], $str);
        $mod4 = strlen($data) % 4;
        if ($mod4) {
            $data .= substr('====', $mod4);
        }

        return base64_decode($data);
    }
}
