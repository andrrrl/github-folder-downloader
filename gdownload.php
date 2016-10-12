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
    
    const GITHUB_URL = 'https://github.com';
    const GITHUB_RAW_URL = 'https://raw.githubusercontent.com';
    
    public function __construct( ...$params )
    {
        
        // /MaxCDN/php-maxcdn/tree/master/src
        
        if ( count( $params ) == 1 ){
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
            mkdir( $this->targetfolder, 0775, true );
        }
        
        // Allow HTML5
        libxml_use_internal_errors(true);
        $this->libpath = $libpath;
        $this->path = $this->libpath;
        $this->url = self::GITHUB_URL . $this->libpath;
        echo 'Loading ' . substr( $this->path, 1, strpos( $this->path, '/', 2 ) - 1 ) . ' source from github... (' . self::GITHUB_URL . $this->path . ')' . PHP_EOL;
        
    }

    /**
     * [parseHTML description]
     * @method parseHTML
     * @return [type]    [description]
     */
    public function parseHTML()
    {
        
        $this->lib = file_get_contents( $this->url );
        
        $doc = new DOMDocument();
        $doc->loadHTML( $this->lib );

        $this->html = simplexml_import_dom( $doc );
        
        $container = $this->html->body->xpath( '//*[starts-with(@class, "css-truncate")]' );
        
        $this->urls = $container[0]->xpath( '//*[starts-with(@class, "js-navigation-open")]' );
        
        return $this->urls;
        
    }

    /**
     * [downloadLib description]
     * @method downloadLib
     * @param  string      $url [description]
     * @return [type]           [description]
     */
    public function downloadLib( $url = '' )
    {
        
        $folders = [];
        
        $this->url = !empty( $url ) ? $url : $this->url;
        
        $this->urls = $this->parseHTML();
        
        echo 'Crawling ' . self::GITHUB_URL . $this->path . PHP_EOL;
        
        foreach ( $this->urls as $file ) {
            
            // Skip .. folder
            if ( $file[0] != '..' ) {
                
                if ( strpos( $file['href'], '.' ) !== false ) {
                    
                    echo 'Downloading... ' . $this->targetfolder . $this->localfolder . '/' . $file[0] . PHP_EOL;
                    
                    // Fix URL 1
                    if ( strpos( $this->libpath, '/master' ) === false ) {
                        $this->libpath .= '/master';
                    }
                    
                    // Fix URL 2
                    $raw_file_dir = str_replace( [ 'blob/', 'tree/' ], '', $this->libpath );
                    
                    file_put_contents( 
                        $this->targetfolder . $this->localfolder . '/' . $file[0], 
                        fopen( self::GITHUB_RAW_URL . $raw_file_dir . $this->localfolder . '/' . $file[0], 'r' ) 
                    );
                    
                } else {
                    $folders[] = $file[0];
                }
                
            } // endif
            
        } // endforeach
        
        // Sort folders
        asort( $folders );
        
        foreach( $folders as $folder ){
            
            echo "Creating subfolder: " . $this->targetfolder . $this->localfolder . PHP_EOL;
            
            $this->localfolder = $this->localfolder . '/' . $folder['title'];
            
            if ( !is_dir( $this->targetfolder . $this->localfolder ) ) {
                mkdir( $this->targetfolder . $this->localfolder, 0775, true );
            }
            
            $this->downloadLib( $this->url . '/' . $folder['title'] );
            
        }
        
        return true;
    }

}

$updater = new LibUpdater( $argv[1], $argv[2] );

if ( $updater->downloadLib() ) {
    echo 'Download complete!' . PHP_EOL;
}