<?php

    function routes_files(){

        $routes[] = array(
                            '_uri'  => '/^files\/go\/(.+)/i',
                            'do'    => 'redirect',
                            1       => 'url'
                         );

        $routes[] = array(
                            '_uri'  => '/^files\/load\/(.+)/i',
                            'do'    => 'download',
                            1       => 'fileurl'
                         );

        return $routes;

    }

?>
