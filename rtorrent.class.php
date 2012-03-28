<?php
require("xmlrpc.class.php");
class rTorrent extends rpcClient {
	private $_downloadscache = false;
	function loadURL($string,$start = false) {
		if ($start) { 
			var_dump($this->request("load_start", array($string)));
		} else {
			var_dump($this->request("load", array($string)));
		}
	}
	function getDownloads($view = "") {
		$download_arr = array();
		//$downloads = $this->multirequest("d.multicall",array($view,"d.get_base_filename=","d.get_name=","d.get_base_path=","d.get_complete=","d.get_hash=","d.get_local_id="));
		$downloads = $this->multirequest("d.multicall",array($view,"d.get_base_filename=","d.get_base_path=","d.get_bytes_done=","d.get_chunk_size=","d.get_chunks_hashed=","d.get_complete=","d.get_completed_bytes=","d.get_completed_chunks=","d.get_connection_current=","d.get_connection_leech=","d.get_connection_seed=","d.get_creation_date=","d.get_directory=","d.get_down_rate=","d.get_down_total=","d.get_free_diskspace=","d.get_hash=","d.get_hashing=","d.get_ignore_commands=","d.get_left_bytes=","d.get_local_id=","d.get_local_id_html=","d.get_max_file_size=","d.get_message=","d.get_peers_min=","d.get_name=","d.get_peer_exchange=","d.get_peers_accounted=","d.get_peers_complete=","d.get_peers_connected=","d.get_peers_max=","d.get_peers_not_connected=","d.get_priority=","d.get_priority_str=","d.get_ratio=","d.get_size_bytes=","d.get_size_chunks=","d.get_size_files=","d.get_skip_rate=","d.get_skip_total=","d.get_state=","d.get_state_changed=","d.get_tied_to_file=","d.get_tracker_focus=","d.get_tracker_numwant=","d.get_tracker_size=","d.get_up_rate=","d.get_up_total=","d.get_uploads_max=","d.is_active=","d.is_hash_checked=","d.is_hash_checking=","d.is_multi_file=","d.is_open=","d.is_private="));
		foreach ($downloads as $download) {
			$download_arr[] = new rTorrentDownload($this,$download);
		}
		return $download_arr;
	}
	function getDownloadByFilename($filename) {
		if ($this->_downloadscache) {
			$downloads = &$this->_downloadscache;
		} else {
			$this->_downloadscache = $downloads = $this->getDownloads();
		}
		foreach ($downloads as $download) {
			if (strtolower($download->getFilename()) == strtolower($filename)) return $download;
		}
		return false;
	}
}
class rTorrentDownload {
	private $server;
	private $name;
	private	$path;
	private $hash;
	private $filename;
	private $localid;

	function __construct($server,$arr) {
		$this->server = $server;
		$this->filename = basename($arr["d.get_base_filename="]);
		$this->path = dirname($arr["d.get_base_path="]);
		$this->name = $arr['d.get_name='];
		$this->hash = $arr['d.get_hash='];
		$this->localid = $arr['d.get_local_id='];
	}
	private function _command($command) {
		$this->server->request($command,array($this->hash));
	}
	function getName() {
		return $this->name;
	}
	function getHash() {
		return $this->hash;
	}
	function getFilename() {
		return $this->filename;
	}
	function getPath() {
		return $this->path;
	}
	function getPathAndFilename() {
		return $this->path."/".$this->filename;
	}
	function close() {
		$this->_command("d.close");
	}
	function open() {
		$this->_command("d.open");	
	}
	function hashcheck() {
		$this->_command("d.check_hash");
	}
	function start() {
		$this->close();
		$this->open();
		$this->_command("d.start");
	}
	function stop() {
		$this->close();
		$this->_command("d.stop");
	}
	function erase() {
		$this->close();
		$this->_command("d.erase");
	}
	function delete() { $this->erase(); }
}
/*
$response = $c->multirequest(
	"d.multicall",
	array(
		"leeching",
		"d.get_base_filename=",
		"d.get_base_path=",
		"d.get_bytes_done=",
		"d.get_chunk_size=",
		"d.get_chunks_hashed=",
		"d.get_complete=",
		"d.get_directory=",
		"d.get_down_rate=",
		"d.get_down_total=",
		"d.get_free_diskspace=",
		"d.get_hash=",
		"d.get_hashing=",
		"d.get_ignore_commands=",
		"d.get_left_bytes=",
		"d.get_local_id=",
		"d.get_message=",
		"d.get_name=",
		"d.get_ratio=",
		"d.get_size_bytes=",
		"d.get_size_files=",
		"d.get_state=",
		"d.get_tied_to_file=",
		"d.get_up_rate=",
		"d.get_up_total=",
		"d.is_active=",
		"d.is_multi_file=",
		"d.is_open=",
	)
	);
*/
?>
