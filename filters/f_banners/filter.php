<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.6   (c) 2010 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2010                        //
//                                                                                           //
/*********************************************************************************************/
	function f_banners(&$text){

        $inCore = cmsCore::getInstance();

		//REPLACE FILE DOWNLOAD LINKS
 		$regex      = '/{(������=)\s*(.*?)}/i';
		$matches    = array();

		preg_match_all( $regex, $text, $matches, PREG_SET_ORDER );

        if ($matches){
            $inCore->loadModel('banners');
            $model = new cms_model_banners();
        }

		foreach ($matches as $elm) {

            $elm[0] = str_replace('{', '', $elm[0]);
			$elm[0] = str_replace('}', '', $elm[0]);

			parse_str( $elm[0], $args );

			$position = @$args['������'];

			if ($position){
				$output = $model->getBannerHTML($position);
			} else { 
                $output = '';
            }

			$text = str_replace('{������='.$position.'}', $output, $text );

		}

		return true;

	}
?>