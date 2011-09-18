<?php

// ========================================================================== //

    function info_component_search(){

        $_component['title']        = 'Поиск';
        $_component['description']  = 'Поиск на сайте';
        $_component['link']         = 'search';
        $_component['author']       = 'InstantCMS Team';
        $_component['internal']     = '0';
        $_component['version']      = '1.8.1';

		$inCore = cmsCore::getInstance();
		$inCore->loadModel('search');

		$_component['config'] = cms_model_search::getConfig();

        return $_component;

    }

// ========================================================================== //

    function install_component_search(){

        return true;

    }

// ========================================================================== //

    function upgrade_component_search(){

        $inCore = cmsCore::getInstance();
		$inDB   = cmsDatabase::getInstance();
		$inCore->loadClass('cron');

		if(!$inDB->get_field('cms_cron_jobs', "job_name='deleteOldResults'", 'id')){
			cmsCron::registerJob('deleteOldResults', array(
											'interval' => 24,
											'component' => 'search',
											'model_method' => 'deleteOldResults',
											'comment' => 'Удаляет записи в кеше поиска старее 1 дня.',
											'custom_file' => '',
											'enabled' => 1,
											'class_name' => '',
											'class_method' => ''
									  ));
		}

		$inDB->query("DROP TABLE IF EXISTS `cms_search`;");

		$sql = "CREATE TABLE IF NOT EXISTS `cms_search` (
				  `id` int(11) NOT NULL auto_increment,
				  `session_id` varchar(100) NOT NULL,
				  `date` timestamp NOT NULL default CURRENT_TIMESTAMP,
				  `title` varchar(250) NOT NULL,
				  `description` varchar(500) NOT NULL,
				  `link` varchar(200) NOT NULL,
				  `place` varchar(100) NOT NULL,
				  `placelink` varchar(200) NOT NULL,
				  PRIMARY KEY  (`id`),
				  KEY `session_id` (`session_id`),
				  KEY `date` (`date`)
				) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;";

		$inDB->query($sql);

		addFultextIndex('cms_blog_posts');
		addFultextIndex('cms_board_items');

        return true;
        
    }

// ========================================================================== //
    function addFultextIndex($table, $pole = 'title'){

		$inDB = cmsDatabase::getInstance();

		$result = $inDB->query("SHOW INDEX FROM `$table`");
		if ($inDB->num_rows($result)){
			$is = false;
			while($index = $inDB->fetch_assoc($result)){
				if($index['Key_name'] == $pole.'_full') { $is = true; };
			}
			if(!$is) { $inDB->query("ALTER TABLE `$table` ADD FULLTEXT `{$pole}_full` (`{$pole}`)"); }
		} else {
			$inDB->query("ALTER TABLE `$table` ADD FULLTEXT `{$pole}_full` (`{$pole}`)");
		}

        return true;

    }
// ========================================================================== //

?>