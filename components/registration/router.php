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

        return $routes;

    }

?>
