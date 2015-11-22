<?php

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
