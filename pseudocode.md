#### Crawl: ####    
lib    
│   ├── gengo-php-master    
│   │   ├── config.ini    
│   │   ├── init.php    
│   │   └── libs    
│   │       ├── Gengo    
│   │       │   ├── Api    
│   │       │   │   ├── Account.php    
│   │       │   │   └── Service.php    
│   │       │   ├── Api.php    
│   │       │   ├── Crypto.php    
│   │       │   └── Exception.php    
│   │       └── Zend    
│   │           ├── Exception.php    
│   │           ├── Http    
│   │           │   ├── Client    
│   │           │   │   ├── Adapter    
│   │           │   │   │   ├── Curl.php    
│   │           │   │   │   ├── Exception.php    

1.  enter folder lib
2.  enter folder gengo-php-master
3.  enter or download? ==> download config.ini
4.  enter or download? ==> download init.php
5.  enter libs
6.  enter Gengo
7.  enter or download? ==> download Api.php
8.  download Crypto.php
9.  download Exception.php
10. nothing to enter? ==> go up 1
11. enter Zend
12. download Exception.php
13. enter Http
14. enter Client
15. enter Adapter
16. download Curl.php
17. download Exception.php

#### Pseudo-code: ####

folders = [
    lib: [
        gengo-php-master: [
            libs: [
                Gengo: [
                    APi
                ],
                Zend: [
                    Http: [
                        Client: [
                            Adapter
                        ]
                    ]
                ]
            ]
        ]
    ]
]


function crawl( folders ):
    
    foreach folders as folder:

        if has_files( folder ):
        
            foreach files as file:
                download file
            endforeach
            
        endif
        
        if has_folders( folder ):
        
            crawl( folder )
        
        endif
        
    endforeach

endfunction


