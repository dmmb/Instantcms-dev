<?php

    function rewrite_rules(){

        $rules[] = array(
                            'source'  => '/^login$/i',
                            'target'  => 'registration/login'
                         );

        $rules[] = array(
                            'source'  => '/^logout$/i',
                            'target'  => 'registration/logout'
                         );

        return $rules;

    }

?>
