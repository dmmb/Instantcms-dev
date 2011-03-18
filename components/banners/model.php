<?php
if(!defined('VALID_CMS')) { die('ACCESS DENIED'); }

class cms_model_banners{

    private $inDB;

	function __construct(){
        $this->inDB = cmsDatabase::getInstance();
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

	public function getBanner($id){
        $sql    = "SELECT * FROM cms_banners WHERE id = $id LIMIT 1";
        $result = $this->inDB->query($sql);

        if ($this->inDB->num_rows($result)){
            $banner     = $this->inDB->fetch_assoc($result);
            $banner     = cmsCore::callEvent('GET_BANNER', $banner);
            return $banner;
        } else {
            return false;
        }
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getBannerHTML($position) {

        if (!$position) { return false; }

        $html = '';

        //get active banners with enough hits
        $sql = "SELECT *
                FROM cms_banners
                WHERE position = '$position' AND published = 1 AND ((maxhits > hits) OR (maxhits = 0))
                ORDER BY RAND()
                LIMIT 1";

        $rs = $this->inDB->query($sql);

        if ($this->inDB->num_rows($rs)==1){

            $banner = $this->inDB->fetch_assoc($rs);

            if ($banner['typeimg']=='image'){
                $html = '<a href="/gobanner'.$banner['id'].'" title="'.$banner['title'].'" target="_blank"><img src="/images/banners/'.$banner['fileurl'].'" border="0" alt="'.$banner['title'].'"/></a>';
            }

            if ($banner['typeimg']=='swf'){
                $html = '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0" width="468" height="60">'."\n".
                            '<param name="movie" value="/images/banners/'.$banner['fileurl'].'?banner_id='.$banner['id'].'" />'."\n".
                            '<param name="quality" value="high" />'."\n".
                            '<param name="FlashVars" value="banner_id='.$banner['id'].'" />'."\n".
                            '<embed src="/images/banners/'.$banner['fileurl'].'?banner_id='.$banner['id'].'" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="468" height="60">'."\n".
                            '</embed>'."\n".
                        '</object>';
            }

            if ($html) { $this->inDB->query("UPDATE cms_banners SET hits = hits + 1 WHERE id=".$banner['id']);	}

        }

        return $html;

    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * Возвращает элементы <option> для списка баннерных позиций
     * @param int $selected
     * @return html
     */
    public function getBannersListHTML($selected=0){
        $html = '';
        for($bp=1; $bp<=10; $bp++){
            if (@$selected==$bp){
                $s = 'selected';
            } else {
                $s = '';
            }
            $html .= '<option value="banner'.$bp.'" '.$s.'>banner'.$bp.'</option>'."\n";
        }
        return $html;
    }
    
/* ==================================================================================================== */
/* ==================================================================================================== */

	public function clickBanner($id){
        $update_sql = "UPDATE cms_banners SET clicks = clicks + 1 WHERE id=$id";
        $this->inDB->query($update_sql);
        cmsCore::callEvent('CLICK_BANNER', $id);
        return true;
    }

}