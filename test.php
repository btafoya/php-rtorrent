<?php

require __DIR__ . '/vendor/autoload.php';

// Example usage, list all torrents that are seeding, then check if the ratio is above 1.00, and if so, delete them.
$x = new rTorrent("http://user:password@localhost/RPC2");
foreach ($x->getDownloads('seeding') as $torrent) {
	if ($torrent->getRatio() > 1000) {
		$torrent->erase();
	}
}
