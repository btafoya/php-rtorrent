<?php

require 'xmlrpc.class.php';
class rTorrent extends rpcClient
{
    private $_downloadscache = false;
    public function loadStarted($string)
    {
        var_dump($this->request('load.start', array($string)));
    }
    public function loadStopped($string)
    {
        var_dump($this->request('load', array($string)));
    }
    public function addTorrent($s)
    {
        return $this->loadStarted($s);
    }
    public function getDownloads($view = '')
    {
        $download_arr = array();
        $downloads = $this->multirequest('d.multicall', array($view, 'd.get_base_filename=', 'd.get_name=', 'd.get_base_path=', 'd.get_complete=', 'd.get_hash=', 'd.get_local_id='));
        foreach ($downloads as $download) {
            $download_arr[] = new rTorrentDownload($this, $download);
        }

        return $download_arr;
    }
    public function getDownloadByFilename($filename)
    {
        if ($this->_downloadscache) {
            $downloads = &$this->_downloadscache;
        } else {
            $this->_downloadscache = $downloads = $this->getDownloads();
        }
        foreach ($downloads as $download) {
            if (strtolower($download->getFilename()) == strtolower($filename)) {
                return $download;
            }
        }

        return false;
    }
}
class rTorrentDownload
{
    private $server;
    private $name;
    private $path;
    private $hash;
    private $filename;
    private $localid;

    public function __construct($server, $arr)
    {
        $this->server = $server;
        $this->filename = basename($arr['d.get_base_filename=']);
        $this->path = dirname($arr['d.get_base_path=']);
        $this->name = $arr['d.get_name='];
        $this->hash = $arr['d.get_hash='];
        $this->localid = $arr['d.get_local_id='];
    }
    private function _command($command)
    {
        $this->server->request($command, array($this->hash));
    }
    public function getName()
    {
        return $this->name;
    }
    public function getHash()
    {
        return $this->hash;
    }
    public function getFilename()
    {
        return $this->filename;
    }
    public function getPath()
    {
        return $this->path;
    }
    public function getPathAndFilename()
    {
        return $this->path.'/'.$this->filename;
    }
    public function close()
    {
        $this->_command('d.close');
    }
    public function open()
    {
        $this->_command('d.open');
    }
    public function hashcheck()
    {
        $this->_command('d.check_hash');
    }
    public function start()
    {
        $this->close();
        $this->open();
        $this->_command('d.start');
    }
    public function stop()
    {
        $this->close();
        $this->_command('d.stop');
    }
    public function erase()
    {
        $this->close();
        $this->_command('d.erase');
    }
    public function delete()
    {
        $this->erase();
    }
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
*/;
