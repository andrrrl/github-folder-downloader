# Github folder downloader #

### Description: ###

- Downloads any project (sub)folder from github, to avoid downloading tests, demos, readme, etc, etc

- Includes full tree structure (and files) from the selected folder as starting point

- Download happens in current local directory by default

### Usage example: ###

Example project is https://github.com/MaxCDN/php-maxcdn/

Let's say you want to download only the `/src` folder:


1. Copy the folder path from the address bar, without the https://github.com part and without the trailing slash:    
    - Given the URL: https://github.com/MaxCDN/php-maxcdn/tree/master/src/
    - Extract: `/MaxCDN/php-maxcdn/tree/master/src`


2. Pass it to the script, along with the target folder:    
`$ python updatelib.py "/MaxCDN/php-maxcdn/tree/master/src" /my/target/dir/`


### TODOs: ###

- [X] Allow passing a target dir/folder
