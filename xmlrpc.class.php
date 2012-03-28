<?php
class rpcClient {
	protected $server;
	function __construct($server) {
		if (!empty($server)) {
			$this->server = $server;
		} else {
			throw new Exception("Invalid Parameter");
		}
	}
	function multirequest($method,$parameters = array()) {
		$responses =  $this->request($method,$parameters);
		$method = array_shift($parameters);
		$retarr = array();
		foreach ($responses as $akey => $response) {
			foreach ($response as $bkey => $parameter) {
				$retarr[$akey][$parameters[$bkey]] = $parameter;
			}
		}
		return $retarr;
	}
	function request($method,$parameters = array()) {
		return $this->_call(xmlrpc_encode_request($method,$parameters));
	}
	private function _call($request) {
		$context = stream_context_create(array('http' => array('method' => "POST",'header' =>"Content-Type: text/xml",'content' => $request))); 
		if ($file = @file_get_contents($this->server, false, $context)) {
			$file=str_replace("i8","double",$file); 
			$file = utf8_encode($file); 
			$ret = xmlrpc_decode($file);
			if (is_array($ret) && xmlrpc_is_fault($ret)) {
				throw new Exception("request failed:".$ret['faultCode'].": ".$ret['faultString']);
			}
		} else { throw new Exception("request failed (".$this->server."):\n\n".$request."\n");}
		return $ret;
   	}
}
