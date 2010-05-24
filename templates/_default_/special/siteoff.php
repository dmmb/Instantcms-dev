<?php
    if(!defined('VALID_CMS')) { die('ACCESS DENIED'); }
    $inConf = cmsConfig::getInstance();
?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
        <title>Сайт временно недоступен</title>
        <meta http-equiv="refresh" content="25;URL=/">
        <style type="text/css">
            * { font-family: Arial; }
            html, body { height:100%; margin:0px; }
            h2 { color: #9F3535; margin:0px; }
            p { margin:0px; margin-top:4px; font-size:14px; }
        </style>
    </head>
    <body>
        <table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">
            <tr>
                <td align="center">
                    <table border="0" cellpadding="0" cellspacing="0" >
                        <tr>
                            <td width="150">
                                <img src="/templates/_default_/special/images/siteoff.png" />
                            </td>
                            <td>
                                <h2>Сайт временно недоступен</h2>
                                <p><?php echo $inConf->offtext; ?></p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
</html>
