#!/usr/bin/python
# -*- coding: utf-8 -*- 

import sys
import os
import re
from lxml.html import parse
from urllib import urlopen, URLopener, urlretrieve

"""
    - ~ by Andrrr <andresin@gmail.com>
    - Created: 20161007
    - Revised: 20161009, 20161010
    - Downloads any project (sub)folder from github, to avoid downloading tests, demos, readme, etc, etc
    - Includes all the tree structure (and files) from the selected folder
    - It defaults to php-maxcdn library (/MaxCDN/php-maxcdn)
    - Download happens in current local directory by default (TODO: allow target folder)
    - Example, download only the /src folder of MaxCDN/php-maxcdn:
    $ cd my-project-folder/libraries/php-maxcdn/
    $ python updatelib.py "/MaxCDN/php-maxcdn/tree/master/src"
    - Tip: copy the full github folder path from the address bar, without the https://github.com part
    
"""
class getSrcFromGit():
    
    github = "https://github.com"
    githubraw = "https://raw.githubusercontent.com"
    localfolder = ""
    phpfile = ""
    
    def __init__(self, gitlib):
        
        if len(gitlib) > 1: 
            self.library = gitlib[1] 
        else: 
            self.library = "/MaxCDN/php-maxcdn/tree/master/src"
        
        self.url = self.github + self.library
        
        try:
            matched = re.search(r'(\/)[a-zA-Z0-9\-\_\.]+(\/)', self.library, re.M|re.I)
            print "Loading %s source from github... (%s)" % (matched.group().replace('/',''), self.url)
            parse(urlopen(self.url)).getroot()
        except IOError:
            print "Invalid github URL, check"
            exit()
    

    def load_source(self):
        git = parse(urlopen(self.url)).getroot()
        return git

    def process_source(self, url=None):

        if url is not None:
            self.url = url

        source = self.load_source()

        body = source.cssselect(".css-truncate .js-navigation-open")

        file_list = []
        dir_list = []

        for f in body:
        
            php = f.text_content().split('.')

            if len(php) > 1:
                file_list.append(f.text_content())
                print 'Downloading %s to ./%s' % (f.text_content(), (self.localfolder + f.text_content()))
                urlretrieve (self.githubraw + self.library.replace('/tree', '') + '/' + self.localfolder + f.text_content(), './' + self.localfolder + f.text_content())
            else:
                dir_list.append(f.text_content())

        for item in sorted(dir_list):

            self.localfolder = self.localfolder + item + '/'
            print "Creating subfolder: %s" % (self.localfolder)

            if not os.path.exists(self.localfolder):
                os.makedirs(self.localfolder)

            self.process_source(self.url + '/' + item)

if __name__ == '__main__':
    getSrcFromGit(sys.argv).process_source()
    print "Download complete!"
