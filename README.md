## SimpleDirList ##

SimpleDirList is PHP directory lister. It's small, simple, fast, fully Unicode-compatible, Modern UI styled and uses "pretty URLs".

### Usage ###

To run it, adjust the config part of index.php to your needs as well as the HTML header afterwards and set up a rewrite rule in your HTTP server.

Example for nginx:

    location /foo/ {
        try_files $uri $uri/ @foo_rewrite;
    }
    location @foo_rewrite {
        rewrite ^/foo/(.+)$ /foo/index.php?p=$1 last;
    }

### Notes ###	

* Since PHP cannot interact with multibyte files on Windows properly, this script is limited to Linux.

### Credit ###

* [dAKirby309] for Modern UI icons

[dAKirby309]: http://dakirby309.deviantart.com/