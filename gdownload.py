#!/usr/bin/python
# -*- coding: utf-8 -*- 

import sys
import os
import re
from lxml.html import parse
from urllib import urlopen, URLopener, urlretrieve

"""
    - Downloads any project (sub)folder from github, to avoid downloading tests, demos, readme, etc, etc
    - Includes all the tree structure (and files) from the selected folder
    - It defaults to php-maxcdn library (/MaxCDN/php-maxcdn) and target folder defaults to ./ (current dir)
    - Download happens in current local directory by default 
    - Example, download only the /src folder of MaxCDN/php-maxcdn:
    $ cd my-project-folder/libraries/php-maxcdn/
    $ python updatelib.py "/MaxCDN/php-maxcdn/tree/master/src" /my/target/dir/
    - Tip: copy the full github folder path from the address bar, without the https://github.com part
    
"""
class getSrcFromGit():
    
    github = "https://github.com"
    githubraw = "https://raw.githubusercontent.com"
    localfolder = ""
    
    def __init__(self, args):
        
        if len(args) == 1:
            print "Github project required!"
            print "Usage: $ python gdownload.py \"/githubuser/project/tree/master/some/folder\" /my/target/dir/"
            exit()
        
        if len(args) > 1: 
            self.library = args[1]
        
        if len(args) > 2:
            self.target_dir = args[2]
        else: 
            self.target_dir = "./"

        if not os.path.exists(self.target_dir):
            os.makedirs(self.target_dir)
        
        self.url = self.github + self.library
        
        try:
            matched = re.search(r'(\/)[a-zA-Z0-9\-\_\.]+(\/)', self.library, re.M|re.I)
            print "Loading %s source from github... (%s)" % (matched.group().replace('/',''), self.url)
            parse(urlopen(self.url)).getroot()
        except IOError:
            print "Invalid github URL, please check"
            exit()
    
        exit()


    def load_source(self):
        git = parse(urlopen(self.url)).getroot()
        return git

    def process_source(self, url=None):

        if url is not None:
            self.url = url

        source = self.load_source()

        # This comes from github.com site, it could change in the future
        body = source.cssselect(".css-truncate .js-navigation-open")

        dir_list = []

        for f in body:
            
            current_item = f.text_content().split('.')

            # Quick and dirty way to see if is a file or folder
            if len(current_item) > 1:
                print 'Downloading %s to %s' % (f.text_content(), (self.target_dir + self.localfolder + f.text_content()))
                urlretrieve (self.githubraw + self.library.replace('/tree', '') + '/' + self.localfolder + f.text_content(), self.target_dir + self.localfolder + f.text_content())
            else:
                dir_list.append(f.text_content())

        for item in sorted(dir_list):

            self.localfolder = self.localfolder + item + '/'
            print "Creating subfolder: %s" % (self.target_dir + self.localfolder)

            if not os.path.exists(self.target_dir + self.localfolder):
                os.makedirs(self.target_dir + self.localfolder)

            self.process_source(self.url + '/' + item)


if __name__ == '__main__':
    getSrcFromGit(sys.argv).process_source()
    print "Download complete!"
