<?php

    function routes_registration(){

        $routes[] = array(
                            '_uri'  => '/^registration\/login$/i',
                            'do'    => 'auth'
                         );

        $routes[] = array(
                            '_uri'  => '/^registration\/logout$/i',
                            'do'    => 'auth',
                            'logout' => 1
                         );

        //RewriteRule ^activate/(.*)$ /index.php?view=registration&do=activate&code=$1
        $routes[] = array(
                            '_uri'  => '/^activate\/(.+)$/i',
                            'do'    => 'activate',
                            1       => 'code'
                         );

        //RewriteRule ^passremind.html$ /index.php?view=registration&do=passremind
        $routes[] = array(
                            '_uri'  => '/^passremind$/i',
                            'do'    => 'passremind'
                         );


        return $routes;

    }

?>
