<?php

/**
 * Kiaan framework
 *
 * @author Hassan Kerdash kerdashhassan@gmail.com
 **/

/*
|---------------------------------------------------
| Namespace
|---------------------------------------------------
*/
namespace Kiaan\Security;

/*
|---------------------------------------------------
| Crypt
|---------------------------------------------------
*/
class Crypt {
    
    /*
    * Global traits
    *
    */
    use \Kiaan\Application\Resources\Global\ExtendingTrait;
     
    /**
     * Encrypt
     *
     */
    public function encrypt($value) {
        // Store the cipher method 
        $ciphering = "AES-128-CTR"; 
        
        // Use OpenSSl Encryption method 
        $iv_length = openssl_cipher_iv_length($ciphering); 
        $options = 0; 
        
        // Non-NULL Initialization Vector for encryption 
        $encryption_iv = '1234567891011121'; 
        
        // Store the encryption key 
        $encryption_key = "FrameWork"; 

        $encryption = openssl_encrypt($value, $ciphering, $encryption_key, $options, $encryption_iv); 

        return $encryption;
    }

    /**
     * Decrypt
     *
     */
    public function decrypt($value) {
        // Store the cipher method 
        $ciphering = "AES-128-CTR"; 

        // Non-NULL Initialization Vector for decryption 
        $decryption_iv = '1234567891011121'; 
        
        // Store the decryption key 
        $decryption_key = "FrameWork"; 
        $options = 0; 

        // Use openssl_decrypt() function to decrypt the data 
        $decryption = openssl_decrypt($value, $ciphering, $decryption_key, $options, $decryption_iv); 

        return $decryption;
    }

    /**
     * Verify
    */
    public function verify($value, $hash)
    {
        $hash = $this->decrypt($value);

        if($value==$hash){
            return true;
        }else{
            return false;
        }
    }

}