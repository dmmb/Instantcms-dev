<?php

// ========================================================================== //

    function info_component_actions(){

        $_component['title']        = 'Лента активности';
        $_component['description']  = 'Управление лентой событий на сайте';
        $_component['link']         = 'actions';
        $_component['author']       = 'InstantCMS Team';
        $_component['internal']     = '0';
        $_component['version']      = '1.9';

		$inCore = cmsCore::getInstance();
		$inCore->loadModel('actions');

		$_component['config'] = cms_model_actions::getConfig();

        return $_component;

    }

// ========================================================================== //

    function install_component_actions(){

        return true;

    }

// ========================================================================== //

    function upgrade_component_actions(){

        return true;
        
    }

// ========================================================================== //

?>