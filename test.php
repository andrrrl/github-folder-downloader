<?php

include 'gdownload.php';

$download = new LibUpdater( $argv[1], $argv[2] );
echo 'Building directory tree...' . PHP_EOL;
$download->buildFolders($download->parseHTML());

if ( $download->downloadFiles() ) {
    echo 'Download complete!' . PHP_EOL;
}
