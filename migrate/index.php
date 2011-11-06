<?php
/******************************************************************************/
//                                                                            //
//                             InstantCMS v1.9                                //
//                        http://www.instantcms.ru/                           //
//                                                                            //
//                   written by InstantCMS Team, 2007-2011                    //
//                produced by InstantSoft, (www.instantsoft.ru)               //
//                                                                            //
//                        LICENSED BY GNU/GPL v2                              //
//                                                                            //
/******************************************************************************/

    session_start();

    define('VALID_CMS', 1);
    
    define('PATH', $_SERVER['DOCUMENT_ROOT']);

    include(PATH.'/core/cms.php');
    include(PATH.'/includes/config.inc.php');

    $inCore     = cmsCore::getInstance();

    define('HOST', 'http://' . $inCore->getHost());

    $inCore->loadClass('config');       //������������
    $inCore->loadClass('db');           //���� ������
    $inCore->loadClass('user');
	$inCore->loadClass('cron');

    $inConf     = cmsConfig::getInstance();
    $inDB       = cmsDatabase::getInstance();

    $version_prev = '1.8';
    $version_next = '1.9';
	
	$is_was_migrate = false;

// ========================================================================== //
// ========================================================================== //

    echo '<style type="text/css">
            body { font-family:Arial; font-size:14px; }

            a { color: #0099CC; }
            a:hover { color: #375E93; }
            h2 { color: #375E93; }

            #wrapper { padding:10px 30px; }
            #wrapper p{ line-height: 20px; }

            .migrate p { 
                           line-height:16px;
                           padding-left:20px;
                           margin:2px;
                           margin-left:20px;                           
                           background:url(/admin/images/actions/on.gif) no-repeat;
                       }
            .important {
                           margin:20px;
                           margin-left:0px;
                           border:solid 1px silver;
                           padding:15px;
                           padding-left:65px;
                           background:url(important.png) no-repeat 15px 15px;
                       }
             .nextlink {
                           margin-top:15px;
                           font-size:18px;
             }
          </style>';

    echo '<div id="wrapper">';

    echo "<h2>�������� InstantCMS {$version_prev} &rarr; {$version_next}</h2>";

// ========================================================================== //
// ========================================================================== //
	if(!$inDB->get_field('cms_components', "link='actions'", 'id')){
		$sql = "INSERT INTO `cms_components` (`title`, `link`, `config`, `internal`, `author`, `published`, `version`, `system`) VALUES
		('����� ����������', 'actions', '---\r\nshow_target: 1\r\nperpage: 10\r\nperpage_tab: 15\r\nis_all: 1\r\nact_type: \r\n  add_quest: 16\r\n  add_club_user: 15\r\n  vote_movie: 31\r\n  add_movie: 30\r\n  add_friend: 20\r\n  add_post: 10\r\n  add_post_club: 25\r\n  add_catalog: 13\r\n  add_wall_my: 29\r\n  add_wall: 23\r\n  add_wall_club: 24\r\n  add_comment: 2\r\n  add_user_photo_multi: 27\r\n  add_board: 12\r\n  add_fpost: 17\r\n  add_article: 8\r\n  add_thread: 18\r\n  add_photo: 7\r\n  add_user_photo: 26\r\n  add_avatar: 19\r\n  add_file: 22\r\n  set_status: 11\r\n  add_award: 21\r\n  add_user: 28\r\n  add_blog: 9\r\n  add_club: 14\r\n', 0, 'InstantCMS Team', 1, '1.9', 1);";
		$inDB->query($sql);
	
		echo '<p>��������� <strong>����� ����������</strong> ����������.</p>';
		$is_was_migrate = true;
	}
// ========================================================================== //
// ========================================================================== //	
	$inDB->query("DROP TABLE IF EXISTS `cms_search`");

	$sql = "CREATE TABLE `cms_search` (
			  `id` int(11) NOT NULL auto_increment,
			  `session_id` varchar(100) NOT NULL,
			  `date` timestamp NOT NULL default CURRENT_TIMESTAMP,
			  `pubdate` datetime default NULL,
			  `title` varchar(250) NOT NULL,
			  `description` varchar(500) NOT NULL,
			  `link` varchar(200) NOT NULL,
			  `place` varchar(100) NOT NULL,
			  `placelink` varchar(200) NOT NULL,
			  PRIMARY KEY  (`id`),
			  KEY `session_id` (`session_id`),
			  KEY `date` (`date`)
			) ENGINE=MyISAM DEFAULT CHARSET=cp1251;";

	$inDB->query($sql);

	echo '<p>������� <strong>cms_search</strong> �����������.</p>';
	$is_was_migrate = true;

// ========================================================================== //
// ========================================================================== //
	if(!$inDB->get_field('cms_cron_jobs', "job_name='deleteOldResults'", 'id')){
		cmsCron::registerJob('deleteOldResults', array(
										'interval' => 24,
										'component' => 'search',
										'model_method' => 'deleteOldResults',
										'comment' => '������� ������ � ���� ������ ������ 1 ���.',
										'custom_file' => '',
										'enabled' => 1,
										'class_name' => '',
										'class_method' => ''
								  ));
		echo '<p>������� CRON deleteOldResults ��� ������� ���������� ���� �������.</p>';
		$is_was_migrate = true;
	}
// ========================================================================== //
// ========================================================================== //
	if(!$inDB->get_field('cms_cron_jobs', "job_name='deleteOldNotification'", 'id')){
		cmsCron::registerJob('deleteOldNotification', array(
										'interval' => 48,
										'component' => 'users',
										'model_method' => 'deleteOldNotification',
										'comment' => '������� ��������� ������ ���������� � �������� ������ 1 ������',
										'custom_file' => '',
										'enabled' => 1,
										'class_name' => '',
										'class_method' => ''
								  ));
		echo '<p>������� CRON deleteOldNotification ��� ������� ��������� ������ ���������� � �������� �������.</p>';
		$is_was_migrate = true;
	}
// ========================================================================== //
// ========================================================================== //
	addFultextIndex('cms_blog_posts');
	addFultextIndex('cms_board_items');
	addFultextIndex('cms_faq_quests', 'quest');
	addFultextIndex('cms_faq_quests', 'answer');
	echo '<p>������� fulltext ��� ������ cms_blog_posts, cms_faq_quests � cms_board_items �������.</p>';
	$is_was_migrate = true;
// ========================================================================== //
// ========================================================================== //
    if (!$inDB->isFieldExists('cms_comments', 'is_hidden')){
        $inDB->query("ALTER TABLE `cms_comments` ADD `is_hidden` TINYINT( 1 ) NOT NULL DEFAULT '0' AFTER `ip`");
        echo '<p>���� <strong>is_hidden</strong> ��������� � ������� <strong>cms_comments</strong></p>';
		$is_was_migrate = true;
    }
// ========================================================================== //
// ========================================================================== //
    if (!$inDB->isFieldExists('cms_forum_cats', 'seolink')){
        $inDB->query("ALTER TABLE `cms_forum_cats`  ADD `seolink` VARCHAR(200) NOT NULL AFTER `ordering`,  ADD INDEX (`seolink`) ");
        echo '<p>���� <strong>seolink</strong> ��������� � ������� <strong>cms_forum_cats</strong></p>';
		$is_was_migrate = true;
    }
// ========================================================================================== //
// ========================================================================================== //
    if ($inDB->isFieldExists('cms_forum_cats', 'seolink')){
		$sql = "SELECT * FROM cms_forum_cats";
	
		$result = $inDB->query($sql);
	
		if ($inDB->num_rows($result)){
	
			$inCore->loadModel('forum');
			$model = new cms_model_forum();
	
			while($cat = $inDB->fetch_assoc($result)){
	
				$seolink = $model->getCatSeoLink($cat['title']);
	
				$inDB->query("UPDATE cms_forum_cats SET seolink='{$seolink}' WHERE id='{$cat['id']}'");
	
			}
	
		}
	
		echo '<p>��������� SEO-��� ��� ��������� ������ ���������...</p>';
		$is_was_migrate = true;
	}
// ========================================================================== //
// ========================================================================== //
    if (!$inDB->isFieldExists('cms_forums', 'icon')){
        $inDB->query("ALTER TABLE `cms_forums` ADD `icon` VARCHAR( 200 ) NOT NULL AFTER `NSLevel`");
        echo '<p>���� <strong>icon</strong> ��������� � ������� <strong>cms_forums</strong></p>';
		$is_was_migrate = true;
    }
// ========================================================================== //
// ========================================================================== //
    if (!$inDB->isFieldExists('cms_forums', 'access_list')){
        $inDB->query("ALTER TABLE `cms_forums` ADD `access_list` TINYTEXT NOT NULL AFTER `description`");
        echo '<p>���� <strong>access_list</strong> ��������� � ������� <strong>cms_forums</strong></p>';
		$is_was_migrate = true;
    }
// ========================================================================== //
// ========================================================================== //
    if ($inDB->isFieldExists('cms_forums', 'auth_group') && $inDB->isFieldExists('cms_forums', 'access_list')){
		$sql    = "SELECT id, auth_group
				   FROM cms_forums";
	
		$result = $inDB->query($sql);
	
		if ($inDB->num_rows($result)){
			while($mod = $inDB->fetch_assoc($result)){
				if($mod['auth_group'] && $mod['auth_group'] != -1) {
	
					$access_list[]  = $mod['auth_group'];
					$access_list_ya = $inCore->arrayToYaml($access_list);
					$inDB->query("UPDATE cms_forums SET `access_list` = '{$access_list_ya}' WHERE id = '{$mod['id']}'");
					unset ($access_list);
	
				}
			}
		}
	
		echo '<p>������������ ����� � ������� ��������.</p>';
		$is_was_migrate = true;
	}
// ========================================================================== //
// ========================================================================== //
    $forum_cfg = $inCore->loadComponentConfig('forum');
	if($forum_cfg['fa_allow'] && $forum_cfg['fa_allow']!=-1){

		$access_list[] = $forum_cfg['fa_allow'];
		$forum_cfg['group_access'] = $access_list ? $inCore->arrayToYaml($access_list) : '';
		$inCore->saveComponentConfig('forum', $forum_cfg);

	} elseif(!$forum_cfg['group_access']) { $forum_cfg['group_access'] = ''; $inCore->saveComponentConfig('forum', $forum_cfg); }

	echo '<p>������������ ����� � ������������ �������� �� ������ ��������.</p>';
	$is_was_migrate = true;

// ========================================================================== //
// ========================================================================== //
    if ($inDB->isFieldExists('cms_forums', 'auth_group')){
        $inDB->query("ALTER TABLE `cms_forums` DROP `auth_group`");
        echo '<p>���� <strong>auth_group</strong> ������� �� ������� <strong>cms_forums</strong></p>';
		$is_was_migrate = true;
    }
// ========================================================================== //
// ========================================================================== //
	if ($is_was_migrate) {
	    echo '<div style="margin:15px 0px 15px 0px;font-weight:bold">�������� ���������. ������� ����� /migrate/ ������ ��� ����������!</div>';
	} else {
		echo '<div style="margin:15px 0px 15px 0px;font-weight:bold">�� ��� ������ ��������.</div>';
	}
    echo '<div class="nextlink"><a href="/">������� �� ����</a></div>';
    echo '</div>';

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