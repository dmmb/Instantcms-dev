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

        $routes[] = array(
                            '_uri'  => '/^registration\/activate\/(.+)$/i',
                            'do'    => 'activate',
                            1       => 'code'
                         );

        $routes[] = array(
                            '_uri'  => '/^registration\/remind\/(.+)$/i',
                            'do'    => 'remind',
                            1       => 'code'
                         );

        $routes[] = array(
                            '_uri'  => '/^registration\/passremind$/i',
                            'do'    => 'sendremind'
                         );

        $routes[] = array(
                            '_uri'  => '/^registration\/autherror$/i',
                            'do'    => 'autherror'
                         );

        return $routes;

    }

?>
