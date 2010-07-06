<?php

    function routes_comments(){

        //RewriteRule ^comments/add$ /index.php?view=comments&do=add
        $routes[] = array(
                            '_uri'  => '/^comments\/add$/i',
                            'do'    => 'add'
                         );

        //RewriteRule ^comments/delete/([0-9]*)$ /index.php?view=comments&do=delete&id=$1
        $routes[] = array(
                            '_uri'  => '/^comments\/delete\/([0-9]+)$/i',
                            'do'    => 'delete',
                            1       => 'id'
                         );

        return $routes;

    }

?>
