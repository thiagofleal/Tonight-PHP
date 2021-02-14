<?php

namespace Tonight\Tools;

class JWT
{
	private $key;

	public function __construct($key)
	{
		$this->key = md5($key);
	}

	public function create(array $content, $exp=0)
	{
		$header = \json_encode(array('typ'=>"JWT", 'alg'=>"HS256"));

		$time = \time();
		$exp = $exp ? ($time + $exp) : NULL;

		$data = array('iat'=>$time, 'exp'=>$exp);

		foreach ($content as $key => $value) {
			$data[$key] = $value;
		}
		$payload = \json_encode($data);

		$header = self::base64url_encode($header);
		$payload = self::base64url_encode($payload);

		$signature = \hash_hmac("sha256", $header.".".$payload, $this->key, true);
		$signature = self::base64url_encode($signature);

		return $header.".".$payload.".".$signature;
	}

	public function verify($jwt)
	{
		$jwt = \explode(".", $jwt);

		if (\count($jwt) !== 3) {
			return false;
		}

		$signature = \hash_hmac("sha256", $jwt[0].".".$jwt[1], $this->key, true);
		$signature = self::base64url_encode($signature);

		if ($signature !== $jwt[2]) {
			return false;
		}

		$payload = \json_decode(self::base64url_decode($jwt[1]));

		if (!empty($payload->exp)) {
			if ($payload->exp < time()) {
				return false;
			}
		}

		return $payload;
	}

	private static function base64url_encode($data)
	{
		return rtrim(
			strtr(base64_encode($data), '+/', '-_'), '='
		);
	}

	private static function base64url_decode($data)
	{
		return base64_decode(
			strtr($data, '-_', '+/').
			str_repeat('=', 3 - (3 + strlen($data)) % 4)
		);
	}
}