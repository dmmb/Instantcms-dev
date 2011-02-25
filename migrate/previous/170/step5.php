<?php

    if (!$inDB->isFieldExists('cms_modules_bind', 'position')){

        $inDB->query("ALTER TABLE `cms_modules_bind` ADD `position` VARCHAR( 20 ) NOT NULL, ADD INDEX ( position )");

        $sql = "SELECT id, position
                FROM cms_modules";

        $result = $inDB->query($sql);

        if($inDB->num_rows($result)){

            while($mod = $inDB->fetch_assoc($result)){

                $inDB->query("UPDATE cms_modules_bind SET position = '{$mod['position']}' WHERE module_id = '{$mod['id']}'");

            }

        }

    }

// ========================================================================== //
// ========================================================================== //

    if (!$locker){

        $sql = "SELECT id, message
                FROM cms_user_msg";

        $result = $inDB->query($sql);

        if($inDB->num_rows($result)){

            while($msg = $inDB->fetch_assoc($result)){

                $html = $inCore->parseSmiles($msg['message'], true);
                $html = mysql_escape_string($html);

                $inDB->query("UPDATE cms_user_msg SET message = '{$html}' WHERE id = '{$msg['id']}'");

            }

            echo '<p>Записи личных сообщений оптимизированы.</p>';

        }
        
    }

// ========================================================================== //
// ========================================================================== //

    if (!$locker){

        $sql = "SELECT id, content
                FROM cms_comments";

        $result = $inDB->query($sql);

        if($inDB->num_rows($result)){

            while($msg = $inDB->fetch_assoc($result)){

                $html = $inCore->parseSmiles($msg['content'], true);
                $html = mysql_escape_string($html);

                $inDB->query("UPDATE cms_comments SET content = '{$html}' WHERE id = '{$msg['id']}'");

            }

            echo '<p>Комментарии оптимизированы.</p>';

        }

    }

