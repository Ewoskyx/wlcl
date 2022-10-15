<?php  
/**
 * Aes Encrypt and Decrypt
 */
class Aes
{
    /**
     Verilerin AES ile şifrelenmesi için kullanılan library
     */
	protected $ci;
	protected $iv;
	protected $key;
	protected $method;
	public function __construct()
	{
		$this->iv="LG5RJNeuo5o72Jll";
		$this->key = "03asVlpWOBksrWLMtY6uk4IQ5w8SZHyD";
		$this->method="aes-256-cbc";
	}
	
	function decrypt($plaintext)
	{
		$decrypted = openssl_decrypt(base64_decode($plaintext), $this->method, $this->key, OPENSSL_RAW_DATA, $this->iv);

		return $decrypted;
	}
	function encrypt($plaintext)
	{
		return base64_encode(openssl_encrypt($plaintext, $this->method, $this->key, OPENSSL_RAW_DATA, $this->iv));
	}
}
?>