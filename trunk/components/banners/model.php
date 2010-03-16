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

	public function clickBanner($id){
        $update_sql = "UPDATE cms_banners SET clicks = clicks + 1 WHERE id=$id";
        $this->inDB->query($update_sql);
        cmsCore::callEvent('CLICK_BANNER', $id);
        return true;
    }

}