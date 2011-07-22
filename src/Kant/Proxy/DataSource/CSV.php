<?php
namespace Kant\Proxy\DataSource;
class CSV implements \Kant\Proxy\IDataSource
{
	private $_csvFile;
	private $_csvHandler;
	private $_proxies = array();

	public function __construct($pathToCsv) {
		if(is_null($pathToCsv)) {
			throw new CSVException("No CSV file provided.");
		}
		if(!file_exists($pathToCsv)) {
			throw new CSVException("No CSV file found at " . $pathToCsv . " .");		
		} 
		$this->_csvFile = $pathToCsv;
		$this->_loadCsv();
	}

	public function __destruct() {
		fclose($this->_csvHandler);
	}
 
	private function _loadCsv() {
		$this->_csvHandler = fopen($this->_csvFile, "r");
	}
	/**
	 * Retrieve a proxy from the CSV
	 * @return mixed Array if found. false if not found
	 */
	public function getProxy() {
		$info = fgetcsv($this->_csvHandler);
		if($info) {
			return array("ip" => $info[0], "port" => $info[1]);
		}
		return false;
	}
}

class CSVException extends \Exception
{
}
