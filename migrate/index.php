<?php
/******************************************************************************/
//                                                                            //
//                             InstantCMS v1.8                                //
//                        http://www.instantcms.ru/                           //
//                                                                            //
//                   written by InstantCMS Team, 2007-2010                    //
//                produced by InstantSoft, (www.instantsoft.ru)               //
//                                                                            //
//                        LICENSED BY GNU/GPL v2                              //
//                                                                            //
/******************************************************************************/

    session_start();

    define('VALID_CMS', 1);
    
    define('PATH', $_SERVER['DOCUMENT_ROOT']);

    require(PATH."/core/cms.php");
    include(PATH."/includes/config.inc.php");

    $inCore     = cmsCore::getInstance();

    define('HOST', 'http://' . $inCore->getHost());

    $inCore->loadClass('config');       //������������
    $inCore->loadClass('db');           //���� ������
    $inCore->loadClass('user');
	$inCore->loadClass('cron');

    $inConf     = cmsConfig::getInstance();
    $inDB       = cmsDatabase::getInstance();

    $version_prev = '1.8';
    $version_next = '1.8.1';
	
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
	$inDB->query("DROP TABLE IF EXISTS `cms_search`");

	$sql = "CREATE TABLE `cms_search` (
			  `id` int(11) NOT NULL auto_increment,
			  `session_id` varchar(100) NOT NULL,
			  `date` timestamp NOT NULL default CURRENT_TIMESTAMP,
			  `pubdate` datetime NOT NULL,
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
	}
	echo '<p>������� CRON deleteOldResults ��� ������� ���������� ���� �������.</p>';
	$is_was_migrate = true;
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