<?php

class rTorrentDownload
{
    private $server;
    private $name;
    private $path;
    private $hash;
    private $filename;
    private $localid;
    private $ratio;

    public function __construct($server, $arr)
    {
        $this->server = $server;
        $this->filename = basename($arr['d.get_base_filename=']);
        $this->path = dirname($arr['d.get_base_path=']);
        $this->name = $arr['d.get_name='];
        $this->hash = $arr['d.get_hash='];
        $this->localid = $arr['d.get_local_id='];
        $this->ratio = $arr['d.get_ratio='];
    }

    private function removeDirectory($directory)
    {
        if (is_dir($directory)) {
            $objects = scandir($directory);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (is_dir($directory."/".$object)) {
                        $this->removeDirectory($directory."/".$object);
                    } else {
                        unlink($directory."/".$object);
                    }
                }
            }
            $this->removeDirectory($directory);
        }
    }

    private function _command($command)
    {
        $this->server->request($command, array($this->hash));
    }

    public function getRatio()
    {
        return $this->ratio;
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
        $this->removeDirectory($this->getPathAndFilename());
    }

    public function delete()
    {
        $this->erase();
    }
}