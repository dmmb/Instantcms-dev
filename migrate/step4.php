<?php

    if (!$inDB->isFieldExists('cms_user_photos', 'album_id')){
        $inDB->query("ALTER TABLE `cms_user_photos` ADD `album_id` INT NOT NULL AFTER `user_id`, ADD INDEX (album_id)");
    }

    if (!$inDB->isTableExists('cms_user_albums')){

        $sql = "CREATE TABLE `cms_user_albums` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `user_id` int(11) NOT NULL,
                  `title` varchar(100) NOT NULL,
                  `pubdate` datetime NOT NULL,
                  `allow_who` varchar(10) NOT NULL,
                  `description` varchar(200) NOT NULL DEFAULT '',
                  PRIMARY KEY (`id`),
                  KEY `user_id` (`user_id`),
                  KEY `allow_who` (`allow_who`)
                ) ENGINE=MyISAM DEFAULT CHARSET=cp1251";

        $inDB->query($sql);
        echo '<p>Таблица <strong>cms_user_albums</strong> создана</p>';

        $sql = "SELECT user_id as id
            FROM cms_user_photos
            GROUP BY user_id";

        $result = $inDB->query($sql);

        if($inDB->num_rows($result)){

            $inCore->loadModel('users');
            $model = new cms_model_users();

            while($user = $inDB->fetch_assoc($result)){

                $album_id = $model->addPhotoAlbum(array('user_id'=>$user['id'], 'title'=>'Мой фотоальбом'));

                $inDB->query("UPDATE cms_user_photos SET album_id = '{$album_id}' WHERE user_id = '{$user['id']}'");

            }

            echo '<p>Личные фотоальбомы созданы</p>';

        }

    }

// ========================================================================== //
// ========================================================================== //

    if (!$locker){

        if (!$inDB->isFieldExists('cms_blog_posts', 'content_html')){

            $inDB->query("ALTER TABLE `cms_blog_posts` ADD `content_html` TEXT NOT NULL AFTER `content`");

            $sql = "SELECT id, content
                    FROM cms_blog_posts";

            $result = $inDB->query($sql);

            if($inDB->num_rows($result)){

                $inCore->loadModel('blogs');
                $model = new cms_model_blogs();

                while($post = $inDB->fetch_assoc($result)){
                    // Парсим по отдельности части текста, если есть тег [cut
                    if (strstr($post['content'], '[cut')){
                        $msg_to 	= $model->getPostShort($post['content']);
                        $msg_to 	= $inCore->parseSmiles($msg_to, true);
                        $msg_after 	= $model->getPostShort($post['content'], false, true);
                        $msg_after 	= $inCore->parseSmiles($msg_after, true);
                    $cut        = $model->getPostCut($post['content']);
                    $html		= $msg_to . $cut . $msg_after;
                    } else {
                    $html = $inCore->parseSmiles($post['content'], true);
                    }

                    $html = $inDB->escape_string($html);

                    $inDB->query("UPDATE cms_blog_posts SET content_html = '{$html}' WHERE id = '{$post['id']}'");

                }

                echo '<p>Записи блогов оптимизированы</p>';

            }

        }

    }
