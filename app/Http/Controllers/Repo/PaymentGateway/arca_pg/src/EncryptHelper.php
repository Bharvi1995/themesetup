<?php
namespace arca_pg\checkout\enc;

include_once __DIR__ . '/Crypt/RSA.php';

use Exception;
use arca_pg\checkout\enc\Crypt\Crypt_RSA;



class EncryptHelper
{
    private $encryption_key;
    private $key_size;
    private $public_xml;

    public function __construct($encryption_key)
    {
        $this->encryption_key = $encryption_key;
        $this->GetKeyFromEncyptionString();
    }

    public function EncryptData($plaintext)
    {
        try {

            $encrypted = $this->Encrypt($plaintext);
            return base64_encode($encrypted);
        } catch (\Throwable $th) {
            throw $th;
        }
    }


    private function Encrypt($data)
    {
        try {

            if (is_null($data) || strlen($data) < 1) {
                throw new Exception("Data sent for encryption is empty");
            }
            if (strlen($data) > $this->GetMaximumDataLength($this->key_size)) {
                throw new Exception("Max data length is " . $this->GetMaximumDataLength($this->key_size));
            }
            if ($this->IsKeySizeValid($this->key_size) == false) {
                throw new Exception("Key size is invalid");
            }
            if (empty($this->public_xml) || is_null($this->public_xml)) {
                throw new Exception("Key is either null or invalid");
            }


            $rsa = new Crypt_RSA();
            $rsa->loadKey($this->public_xml);
            $rsa->setPublicKey();
            $pub_key = $rsa->getPublicKey();


            openssl_public_encrypt($data, $encryptedData, $pub_key);

            return $encryptedData;

        } catch (\Throwable $th) {
            throw $th;
        }
    }

    private function GetKeyFromEncyptionString()
    {

        try {
            $decrypted_key = base64_decode($this->encryption_key);
            if (preg_match('/!/', $decrypted_key)) {
                $decrypted_items = explode("!", $decrypted_key, 2);
                $this->key_size = $decrypted_items[0];

                $this->public_xml = $decrypted_items[1];
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    private function GetMaximumDataLength($keysize)
    {
        return (($keysize - 384) / 8) * 37;
    }
    private function IsKeySizeValid($keysize)
    {
        return $keysize >= 384 && $keysize <= 32768 && $keysize % 8 == 0;
    }
}