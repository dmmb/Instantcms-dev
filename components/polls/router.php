<?php

    function routes_polls(){

        //RewriteRule ^polls/vote$ /index.php?view=polls&do=vote
        $routes[] = array(
                            '_uri'  => '/^polls\/vote$/i',
                            'do'    => 'vote'
                         );

        return $routes;

    }

?>
