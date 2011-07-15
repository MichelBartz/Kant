<?php
namespace Kant\Proxy\DataSource;
class Redis
{
	const PROXIES = "proxies";

	private $_redis;

	/**
	 * @param String $host The host address for Redis
	 * @param int $port The port for the Redis server
	 */
	public function __construct($host, $port) {
		if(is_null($host)) {
			throw new RedisException("No Host provided for the Redis server.");
		}
		if(is_null($port)) {
			throw new RedisException("No Port provided for the Redis server.");
		}
		$this->_redis = new \Redis();
		$this->_redis->connect($host, $port);
	}

	/**
	 * Retrieve a proxy from Redis
	 * @return mixed Array if found. false if not found
	 */
	public function getProxy() {
		$hashKey = $this->_redis->lPop(self::PROXIES);
		if($hashKey) {
			$data = $this->_redis->hGetAll($hashKey);
		   	if($data) {
				return $data;
			}	
		}
		return false;
	}
}

class RedisException extends \Exception
{
}
