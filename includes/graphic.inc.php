<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.7   (c) 2010 FREEWARE                          //
//				  http://www.instantcms.ru/, info@instantcms.ru                              //
//                                                                                           //
/*********************************************************************************************/

if(!defined('VALID_CMS')) { die('ACCESS DENIED'); }

function img_add_watermark($src){
  $size = getimagesize($src);

  if ($size === false) return false;

  $format = strtolower(substr($size['mime'], strpos($size['mime'], '/')+1));
  $icfunc = "imagecreatefrom" . $format;
  if (!function_exists($icfunc)) return false;

  $isrc = $icfunc($src);

  img_watermark($isrc, $size[0], $size[1]);
  
   // вывод картинки и очистка памяти
  imagejpeg($isrc,$src,80); 

}

function img_watermark(&$img, $w, $h){

    $inConf = cmsConfig::getInstance();

    if (!$inConf->wmark) { return; }

	$wm_file = 	$_SERVER['DOCUMENT_ROOT'].'/images/'.$inConf->wmark;

    if (!file_exists($wm_file)) { return; }

    $size = getimagesize($wm_file);		
	
	$wm = imagecreatefrompng($wm_file);
	
	$wm_w = $size[0];
	$wm_h = $size[1];
	
	$wm_x = $w - $wm_w;
	$wm_y = $h - $wm_h;
	
	imagecopyresampled($img, $wm, $wm_x, $wm_y, 0, 0, $wm_w, $wm_h, $wm_w, $wm_h);
				
}

/***********************************************************************************
Функция img_resize(): генерация thumbnails
Параметры:
  $src             - имя исходного файла
  $dest            - имя генерируемого файла
  $width, $height  - ширина и высота генерируемого изображения, в пикселях
Необязательные параметры:
  $rgb             - цвет фона, по умолчанию - белый
  $quality         - качество генерируемого JPEG, по умолчанию - максимальное (100)
***********************************************************************************/
function img_resize($src, $dest, $maxwidth, $maxheight=160, $is_square=false, $watermark=false, $rgb=0xFFFFFF, $quality=80)
{
  if (!file_exists($src)) return false;

  $upload_dir = dirname($dest);		
  if (!is_writable($upload_dir)){ @chmod($dest, 0755); }

  $size = getimagesize($src);

  if ($size === false) return false;

  $new_width   = $size[0];
  $new_height  = $size[1];

  if (($new_height <= $maxheight) && ($new_width <= $maxwidth)){
      @copy($src, $dest);
      return true;
  }

  // Определяем исходный формат по MIME-информации, предоставленной
  // функцией getimagesize, и выбираем соответствующую формату
  // imagecreatefrom-функцию.
  $format = strtolower(substr($size['mime'], strpos($size['mime'], '/')+1));
  $icfunc = "imagecreatefrom" . $format;
  if (!function_exists($icfunc)) return false;

  $isrc = $icfunc($src);

  if($is_square){
	  $idest = imagecreatetruecolor($maxwidth,$maxwidth);
	  imagefill($idest, 0, 0, $rgb);	   
	  // вырезаем квадратную серединку по x, если фото горизонтальное
	  if ($new_width>$new_height)
	  imagecopyresampled($idest, $isrc, 0, 0, round((max($new_width,$new_height)-min($new_width,$new_height))/2), 0, $maxwidth, $maxwidth, min($new_width,$new_height), min($new_width,$new_height));	
	  // вырезаем квадратную верхушку по y,
	  if ($new_width<$new_height)
	  imagecopyresampled($idest, $isrc, 0, 0, 0, 0, $maxwidth, $maxwidth, min($new_width,$new_height), min($new_width,$new_height));
	  // квадратная картинка масштабируется без вырезок
	  if ($new_width==$new_height)
	   imagecopyresampled($idest, $isrc, 0, 0, 0, 0, $maxwidth, $maxwidth, $new_width, $new_width);
  } else {
  	  while ($new_width>$maxwidth) { $new_width *= 0.99; $new_height *= 0.99; }	
	  while ($new_height>$maxheight) { $new_width *= 0.99; $new_height *= 0.99; }	
	  $idest = imagecreatetruecolor($new_width, $new_height);
	  imagefill($idest, 0, 0, $rgb);
	  imagecopyresampled($idest, $isrc, 0, 0, 0, 0, $new_width, $new_height, $size[0], $size[1]);
 	  
  }
  
  if ($watermark) { img_watermark($idest, $new_width, $new_height); }         

  imageinterlace($idest,1);

  // вывод картинки и очистка памяти
  imagejpeg($idest,$dest,$quality);

  imagedestroy($isrc);
  imagedestroy($idest);
  return true;
}
?>