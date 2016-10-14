<?php

/**
 * Package LibUpdater
 * Updates github libraries
 */
Class LibUpdater {
    
    public $lib;
    public $path;
    public $libpath;
    public $html;
    public $urls;
    public $localfolder = '';
    public $targetfolder = './tests';
    public $foldertree = [];

    const GITHUB_URL = 'https://github.com';
    const GITHUB_RAW_URL = 'https://raw.githubusercontent.com';

    public function __construct( ...$params )
    {

        // /MaxCDN/php-maxcdn/tree/master/src

        if ( count( $params ) == 0 ){
            echo "Github project required!" . PHP_EOL;
            echo "Usage: $ php gdownload.php \"/githubuser/project/tree/master/some/folder\" /my/target/dir/" . PHP_EOL;
            exit;
        }

        if ( count( $params ) > 0 ) {
            $libpath = $params[0];
        }

        if ( count( $params ) > 1 ) {
            $this->targetfolder = $params[1];
        }

        if ( !is_dir( $this->targetfolder ) ) {
            echo 'Target folder is: [' . $this->targetfolder . ']' . PHP_EOL;
            mkdir( $this->targetfolder, 0775, true );
        } else {
            echo 'Target folder [ '. $this->targetfolder . ']  already exists...' . PHP_EOL;
        }

        // Allow HTML5
        libxml_use_internal_errors(true);
        $this->libpath = $libpath;
        $this->path = $this->libpath;
        $this->url = self::GITHUB_URL . $this->libpath;
        echo 'Loading [' . substr( $this->path, 1, strpos( $this->path, '/', 2 ) - 1 ) . '] from github... (' . self::GITHUB_URL . $this->path . ')' . PHP_EOL;

    }
    
    public function parseHTML( $url = null )
    {

        $url = !empty( $url ) ? $url : $this->url;
        $this->lib = file_get_contents( $url );

        $doc = new DOMDocument();
        $doc->loadHTML( $this->lib );

        $this->html = simplexml_import_dom( $doc );

        $container = $this->html->body->xpath( '//*[starts-with(@class, "css-truncate")]' );

        $this->urls = $container[0]->xpath( '//*[starts-with(@class, "js-navigation-open")]' );

        return $this->urls;

    }
    
    public function buildFolders( $root_folder )
    {
        
        $this->foldertree['remote'][] = $this->url;
        $this->foldertree['local'][] = $this->targetfolder . $this->localfolder;
        
        foreach( $root_folder as $key => $folder ) {
            
            if ( ( strpos( $folder[0], '.' ) !== false ) || $folder[0] == '..' ) {
                continue;
            }
            
            if ( $folder['title'] == 'This path skips through empty directories' ) {
                $folder_clean = trim( str_replace( $this->libpath, '', $folder['href'] ), '/' );
                $subfolder = explode( '/', $folder_clean );
            } else {
                $subfolder = (array) $folder['title'][0];
            }
            
            $this->foldertree['remote'][] = self::GITHUB_URL . $folder['href'];
            
            $this->localfolder = str_replace( $this->libpath, '', $folder['href'] );
            
            $this->foldertree['local'][] = $this->targetfolder . $this->localfolder;
            
            if ( !is_dir( $this->targetfolder . $this->localfolder ) ) {
                echo 'Creating dir ' . $this->targetfolder . $this->localfolder . PHP_EOL;
                if ( !mkdir( $this->targetfolder . $this->localfolder, 0775, true ) ) {
                    echo 'Can\'t mkdir: ' . $this->targetfolder . $this->localfolder . PHP_EOL;
                };
            } else {
                echo 'Directory ' . $this->targetfolder . $this->localfolder . ' already exists, skipping' . PHP_EOL;
            }
            
            $this->buildFolders( $this->parseHTML( self::GITHUB_URL . $folder['href'] ) );
            
        }
        
    }
    
    public function downloadFiles()
    {
        
        foreach( $this->foldertree['remote'] as $key => $folder ) {
            
            $subfolders = $this->parseHTML( $folder );
            
            foreach( $subfolders as $sub ) {
                
                
                if ( $sub != '..' ) {
                    if( strpos( $sub, '.' ) !== false ) {
                        
                        // Fix URL 1
                        if ( strpos( $this->libpath, '/master' ) === false ) {
                            $this->libpath .= '/master';
                        }

                        // Fix URL 2
                        $this->foldertree['remote'][$key] = str_replace( self::GITHUB_URL, self::GITHUB_RAW_URL, $this->foldertree['remote'][$key] );
                        $this->foldertree['remote'][$key] = str_replace( [ 'blob/', 'tree/' ], '', $this->foldertree['remote'][$key] );
                        
                        // Download file
                        echo 'Downloading ' . $this->foldertree['remote'][$key] . '/' . $sub . ' to ' . $this->foldertree['local'][$key] . '/' . $sub . PHP_EOL;
                        $file_contents = file_get_contents( $this->foldertree['remote'][$key] . '/' . $sub );
                        file_put_contents( $this->foldertree['local'][$key] . '/' . $sub, $file_contents );
                        
                    } 
                } 
                
            }
            
        }
        
        return true;
    }

}