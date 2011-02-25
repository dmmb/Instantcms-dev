<?php

    if (!$locker){

        $sql = "SELECT id, content
                FROM cms_user_wall";

        $result = $inDB->query($sql);

        if($inDB->num_rows($result)){

            while($msg = $inDB->fetch_assoc($result)){

                $html = $inCore->parseSmiles($msg['content'], true);
                $html = mysql_escape_string($html);

                $inDB->query("UPDATE cms_user_wall SET content = '{$html}' WHERE id = '{$msg['id']}'");

            }

            echo '<p>«аписи на стенах пользователей оптимизированы.</p>';

        }

    }

// ========================================================================== //
// ========================================================================== //

    $sql = "DELETE FROM cms_uc_ratings WHERE points = 0";

    $result = $inDB->query($sql);

    echo '<p>√олоса с нулевым рейтингом универсального каталога удалены.</p>';

// ========================================================================== //
// ========================================================================== //

    if (!$inDB->isFieldExists('cms_forum_threads', 'is_hidden')){
        $inDB->query("ALTER TABLE `cms_forum_threads` ADD `is_hidden` INT( 11 ) NOT NULL DEFAULT '0' AFTER `pinned`");
    }

	$sql = "SELECT t.id
			FROM cms_forum_threads t, cms_forums f
			WHERE t.forum_id = f.id AND f.auth_group<>0";
	$result = $inDB->query($sql);

    if($inDB->num_rows($result)){

        while($msg = $inDB->fetch_assoc($result)){

            $inDB->query("UPDATE cms_forum_threads SET is_hidden = 1 WHERE id = '{$msg['id']}'");

        }

        echo '<p>ѕрава доступа тем к закрытым разделам форума установлены.</p>';

    }

    file_put_contents('locker', '1');