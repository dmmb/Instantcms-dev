<?php

    function routes_arhive(){

        //RewriteRule ^arhive/([0-9]*)/([0-9]*)/([0-9]*)$ /index.php?view=arhive&y=$1&m=$2&d=$3
        $routes[] = array(
                            '_uri'  => '/^arhive\/([0-9]+)\/([0-9]+)\/([0-9]+)$/i',
                            1       => 'y',
                            2       => 'm',
                            3       => 'd'
                         );

        //RewriteRule ^arhive/([0-9]*)/([0-9]*)$ /index.php?view=arhive&y=$1&m=$2
        $routes[] = array(
                            '_uri'  => '/^arhive\/([0-9]+)\/([0-9]+)$/i',
                            1       => 'y',
                            2       => 'm'
                         );

        //RewriteRule ^arhive/([0-9]*)$ /index.php?view=arhive&y=$1
        $routes[] = array(
                            '_uri'  => '/^arhive\/([0-9]+)$/i',
                            1       => 'y'
                         );

        return $routes;

    }

?>
