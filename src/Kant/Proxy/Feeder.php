<?php
namespace Kant\Proxy;
class Feeder
{
	private $_dataSource;

	public function __construct() {
	}

	public function setDataSource(\Kant\Proxy\IDataSource $dataSource) {
		$this->_dataSource = $dataSource;
	}
	
	/**
	 * Return the proxy information
	 * @return array [ip => ip_adress, port=>server_port]
	 */
	public function getProxy() {
		$proxy = $this->_dataSource->getProxy();
		if(!$proxy) {
			throw new ProxyException("No more proxy available."); 
		}
		return $proxy;
	}
}

class ProxyException extends Exception
{
	
}
