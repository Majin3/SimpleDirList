## SimpleDirList

SimpleDirList is a PHP directory lister. It's small, simple, fast, fully Unicode-compatible, Modern UI styled and uses "pretty URLs".

![preview](http://s1.directupload.net/images/120915/d3sfqqdx.png "Preview")

### Usage

To run it, adjust the config part of index.php to your needs as well as the HTML header afterwards and set up a rewrite rule in your HTTP server.
Please note that this script is completely based on PHP and doesn't have to be in the same directory as the files you want it to list (actually, it's not even supposed to be in the same dir). Only your PHP service needs access to the files, not the HTTP server.

Example for nginx (/foo/ subdir):

    location /foo/ {
        try_files $uri $uri/ @foo_rewrite;
    }
    location @foo_rewrite {
        rewrite ^/foo/(.+)$ /foo/index.php?p=$1 last;
    }
    
Example for mod_rewrite (root):

    RewriteEngine On
    RewriteCond %{SCRIPT_FILENAME} !-d
    RewriteCond %{SCRIPT_FILENAME} !-f
    RewriteRule ^(.*)$ index.php?p=$1

### Notes

* Since PHP cannot interact with multibyte files on Windows properly, this script is limited to Linux.
* If your files come out corrupted, check whether you added an UTF-8 header to index.php by mistake

### Credit

* [dAKirby309] for Modern UI icons

[dAKirby309]: http://dakirby309.deviantart.com/
