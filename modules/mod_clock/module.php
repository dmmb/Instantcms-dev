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

function mod_clock(){
        $inCore = cmsCore::getInstance();
        $inDB = cmsDatabase::getInstance();
	
            ?>
            <table cellpadding="0" cellspacing="0" border="0" align="center"><tr><td align="center" valign="middle">
            <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0" width="184" height="184">
              <param name="movie" value="/images/swf/clock.swf" />
              <param name="quality" value="high" />
              <embed src="/images/swf/clock.swf" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="184" height="184"></embed>
            </object>
            </td></tr></table>
            <?php
				
		return true;
	
}
?>
