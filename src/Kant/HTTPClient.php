<?php
namespace Kant 
{
	require_once "Kant/Proxy/Feeder.php";
	require_once "Kant/Proxy/IDataSource.php";

	class HTTPClient {

		const HTTP_METHOD_GET = "GET";
		const HTTP_METHOD_POST = "POST";
		const HTTP_METHOD_PUT = "PUT";
		const HTTP_METHOD_DELETE = "DELETE";

		/**
		 * Frequency at which we rotate the proxies
		 * @var int $proxyRotationFrequency
		 */
		public $proxyRotationFrequency = 5;
		/**
		 * Number of retry for a given url
		 * @var int $retryCount
		 */
		public $retryCount = 5;

		private $_responseHttpCode = 0;
		private $_noProxy = false;
		private $_proxyFeeder;
		private $_currentProxy;
		private $_rotationCount = 0;
		private $_numRetries = 0;
		private $_httpMethod;
		private $_userAgent;
		private $_cookies;
		private $_referer;
		private $_timeout = 5;
		private $_curlErrorMsg;
		private $_curlErrorNo;

		public function __construct(Proxy\IDataSource $dataSource) {
			$this->_proxyFeeder = new Proxy\Feeder();
			$this->_proxyFeeder->setDataSource($dataSource);
			$this->_currentProxy = $this->_proxyFeeder->getProxy();
		}
		/**
		 * Set the HTTP Method to use for the calls
		 * @param String $method The moethod to use
		 * @return Kant\HTTPClient
		 */
		public function setMethod($method) {
			$this->_httpMethod = $method;
			return $this;
		}	
		public function setNoProxy($value) {
			$this->_noProxy = $value;
			return $this;
		}
		public function setUserAgent($userAgent) {
			$this->_userAgent = $userAgent;
			return $this;
		}
		public function setCookies($cookies) {
			$this->_cookies = $cookies;
			return $this;
		}
		public function setReferer($referer) {
			$this->_referer = $referer;
			return $this;
		}
		public function setTimeout(int $timeout) {
			$this->_timeout = $timeout;
			return $this;
		}
		/**
		 * Load the specified URL
		 * @param String $url The url to load
		 * @return String
		 */
		public function load($url) {
			$ch = curl_init($url);

			if(isset($this->_userAgent)) {
				curl_setopt($ch, CURLOPT_USERAGENT, $this->_userAgent);
			}		
			if(isset($this->_cookies)) {
				curl_setopt($ch, CURLOPT_COOKIE, $this->_cookies);
			}
			if(isset($this->_referer)) {
				curl_setopt($ch, CURLOPT_REFERER, $this->_referer);
			}
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->_timeout);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

			if(!$this->_noProxy) {
				if($this->_rotationCount >= $this->proxyRotationFrequency) {
					$this->_currentProxy = $this->_proxyFeeder->getProxy();
					$this->_rotationCount = 0;
				}
				curl_setopt($ch, CURLOPT_PROXY, $this->_currentProxy['ip']);
				curl_setopt($ch, CURLOPT_PROXYPORT, $this->_currentProxy['port']); 
				$this->_rotationCount++;
			}

			$output = curl_exec($ch);
			$this->_responseHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			if(curl_errno($ch) > 0) {
				$this->_curlErrorMsg = curl_error($ch);
				$this->_curlErrorNo = curl_errno($ch);

				if(!$this->noProxy) {
					$this->_rotationCount = 10;
				}
				$this->_numRetries++;
				if($this->_numRetries < $this->retryCount) {
					$this->load($url);
				}

				return false;
			}
			return $output;
		}
		/**
		 * Return the HTTPCode for the last call
		 * @return int
		 */
		public function getHTTPResponseCode() {
			return $this->_responseHttpCode;	
		}
		public function getErrorMessage() {
			return $this->_curlErrorMsg;
		}	
		public function getErrorNo() {
			return $this->_curlErrorNo;
		}
	} 
}
