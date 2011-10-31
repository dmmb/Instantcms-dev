<?php
/******************************************************************************/
//                                                                            //
//                             InstantCMS v1.9                                //
//                        http://www.instantcms.ru/                           //
//                                                                            //
//                   written by InstantCMS Team, 2007-2011                    //
//                produced by InstantSoft, (www.instantsoft.ru)               //
//                                                                            //
//                        LICENSED BY GNU/GPL v2                              //
//                                                                            //
/******************************************************************************/
	function f_banners(&$text){

        $inCore = cmsCore::getInstance();

		//REPLACE FILE DOWNLOAD LINKS
 		$regex      = '/{(аюммеп=)\s*(.*?)}/i';
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

			$position = @$args['аюммеп'];

			if ($position){
				$output = $model->getBannerHTML($position);
			} else { 
                $output = '';
            }

			$text = str_replace('{аюммеп='.$position.'}', $output, $text );

		}

		return true;

	}
?>