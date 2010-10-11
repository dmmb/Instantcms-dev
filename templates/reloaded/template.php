<?php
    if(!defined('VALID_CMS')) { die('ACCESS DENIED'); }
    $inUser = cmsUser::getInstance();
    $inCore = cmsCore::getInstance();

    $mod_count['top']   = cmsCountModules('top');
    $mod_count['sidebar']  = cmsCountModules('sidebar');

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <!-- HEAD !-->
    <?php cmsPrintHead(); ?>
    <meta http-equiv="Content-Type" content="text/html; charset=windows-1251"/>
    <link href="/templates/reloaded/css/reset.css" rel="stylesheet" type="text/css" />
    <link href="/templates/reloaded/css/text.css" rel="stylesheet" type="text/css" />
    <link href="/templates/reloaded/css/960.css" rel="stylesheet" type="text/css" />
    <link href="/templates/reloaded/css/styles.css" rel="stylesheet" type="text/css" />
</head>

<body>

    <div id="header">
        <div class="container_12">            
            <div class="grid_8">
                <div id="sitename"><?php cmsPrintSitename(); ?></div>
            </div>
            <div class="grid_4">
                <div id="authblock">
                    <a href="#">Регистрация</a>
                    <a href="#">Вход</a>
                </div>
            </div>
        </div>
    </div>

    <div id="page">

        <div class="container_12">
            <div id="topmenu" class="grid_12">
                <?php cmsModule('topmenu'); ?>
            </div>
        </div>

        <?php if ($mod_count['top']){ ?>
        <div class="clear"></div>

        <div id="topwide" class="container_12">
            <div class="grid_12" id="topleft"><?php cmsModule('top'); ?></div>
        </div>
        <?php } ?>

        <div class="clear"></div>

        <div id="mainbody" class="container_12">
            <div id="main" class="<?php if ($mod_count['sidebar']) { ?>grid_8<?php } else { ?>grid_12<?php } ?>">
                <?php cmsModule('maintop'); ?>
                <?php cmsBody(); ?>
                <?php cmsModule('mainbottom'); ?>
            </div>
            <?php if ($mod_count['sidebar']) { ?>
                <div class="grid_4" id="sidebar"><?php cmsModule('sidebar'); ?></div>
            <?php } ?>            
        </div>

    </div>

    <div class="clear"></div>

    <div id="footer">
        <div class="container_12">
            <div class="grid_12">
                <div id="copyright"><?php cmsPrintSitename(); ?> &copy; <?php echo date('Y'); ?></div>
            </div>
            <div class="grid_4">
            </div>
        </div>
    </div>

</body>

</html>