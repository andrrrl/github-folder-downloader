# Github folder downloader #

### Description: ###

- Downloads any project (sub)folder from github, to avoid downloading tests, demos, readme, etc, etc

- Includes all the tree structure (and files) from the selected folder

- It defaults to php-maxcdn library (/MaxCDN/php-maxcdn)

- Download happens in current local directory by default (TODO: allow target folder)

### Usage example: ###

Example project is https://github.com/MaxCDN/php-maxcdn/

Let's say you want to download only the `/src` folder:

1. Place this script inside the folder where to download and `cd` into it:     
`$ cd my-local-folder/libraries/php-maxcdn/`

2. Copy the folder path from the address bar, without the https://github.com part and without the trailing slash:    
    - Given the URL: https://github.com/MaxCDN/php-maxcdn/tree/master/src/
    - Extract: `/MaxCDN/php-maxcdn/tree/master/src`


3. Pass it to the script:    
`$ python updatelib.py "/MaxCDN/php-maxcdn/tree/master/src"`


### TODOs: ###

[ ] Allow passing a target folder
