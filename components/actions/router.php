<?php

    function routes_actions(){

        $routes[] = array(
                            '_uri'  => '/^actions\/delete\/([0-9]+)$/i',
                            'do'    => 'delete',
                            1       => 'id'
                         );

        return $routes;

    }

?>
