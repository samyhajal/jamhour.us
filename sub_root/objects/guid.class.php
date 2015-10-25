<?php
/**
 *	Creates Globally Unique IDs (GUID)
 *	@return guid A Globally Unique ID
 */
class GUID{

	private static $m_pInstance;

	/**
	 * Get the singleton dictionary
	 * @param bool $dbname Database name.
	 * @return db/bool
	 */
	public static function getguid( )
	{
		if (!self::$m_pInstance)
		{
			self::$m_pInstance = new GUID( );
		}
		return self::$m_pInstance;
	}

	/**
	 * Private constructor for singleton pattern
	 */
	private function  __construct() {

	}

	public function create_guid(){
		$microTime = microtime();
		list($a_dec, $a_sec) = explode(" ", $microTime);
		$dec_hex = sprintf("%x", $a_dec* 1000000);
		$sec_hex = sprintf("%x", $a_sec);
		$this->ensure_length($dec_hex, 5);
		$this->ensure_length($sec_hex, 6);
		$guid = "";
		$guid .= $dec_hex;
		$guid .= $this->create_guid_section(3);
		$guid .= '-';
		$guid .= $this->create_guid_section(4);
		$guid .= '-';
		$guid .= $this->create_guid_section(4);
		$guid .= '-';
		$guid .= $this->create_guid_section(4);
		$guid .= '-';
		$guid .= $sec_hex;
		$guid .= $this->create_guid_section(6);
		return $guid;
	}

	private function create_guid_section($characters){
		$return = "";
		for($i=0; $i<$characters; $i++){
			$return .= sprintf("%x", mt_rand(0,15));
		}
		return $return;
	}

	private function ensure_length(&$string, $length){
		$strlen = strlen($string);
		if($strlen < $length){
			$string = str_pad($string,$length,"0");
		}
		elseif($strlen > $length){
			$string = substr($string, 0, $length);
		}
	}

	private function microtime_diff($a, $b){
		list($a_dec, $a_sec) = explode(" ", $a);
		list($b_dec, $b_sec) = explode(" ", $b);
		return $b_sec - $a_sec + $b_dec - $a_dec;
	}
}

?>