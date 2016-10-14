<?php

$download = new TestCrawl( $argv[1], $argv[2] );
echo 'Building directory tree...' . PHP_EOL;
$download->buildFolders($download->parseHTML());

if ( $download->donwloadFiles() ) {
    echo 'Download complete!' . PHP_EOL;
}
