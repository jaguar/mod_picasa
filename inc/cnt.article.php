<?php 
/*************************************************************************************

	PICASA Webalbum
	
	PICASA PHP API by David Gilbert ( http://solidgone.org/pmGallery )
	PHPWCMS integration by Marcus Obst, 2010

 *************************************************************************************/
 
// ----------------------------------------------------------------
// obligate check for phpwcms constants
if (!defined('PHPWCMS_ROOT')) {
    die("You Cannot Access This Script Directly, Have a Nice Day.");
}
// ----------------------------------------------------------------

$content['picasa'] = @unserialize($crow["acontent_form"]);

if(empty($content['picasa']['picasa_select'])){
	$content['picasa']['picasa_select'] = array();
}
if(empty($content['picasa']['picasa_sort'])){
	$content['picasa']['picasa_sort'] = array();
}
if(empty($content['picasa']['picasa_maxresult'])){
	$content['picasa']['picasa_maxresult'] = 50;
}

require_once ('picasaAPI.php');

// check for template and load default in case of error
if ( empty($content['picasa']['picasa_template']) && is_file(PHPWCMS_TEMPLATE.'inc_default/images.tmpl')) {

    // load default template
    $content['picasa']['picasa_template'] = file_get_contents(PHPWCMS_TEMPLATE.'inc_default/images.tmpl');
    
} elseif (file_exists(PHPWCMS_TEMPLATE.'inc_cntpart/images/'.$content['picasa']['picasa_template'])) {

    // load custom template
    $content['picasa']['picasa_template'] = file_get_contents(PHPWCMS_TEMPLATE.'inc_cntpart/images/'.$content['picasa']['picasa_template']);
    
} else {

    // again load default template
    $content['picasa']['picasa_template'] = 'No Template!';
    
}

$content['picasa']['tmpl_header'] = get_tmpl_section('IMAGES_HEADER', $content['picasa']['picasa_template']);
$content['picasa']['tmpl_footer'] = get_tmpl_section('IMAGES_FOOTER', $content['picasa']['picasa_template']);
$content['picasa']['tmpl_entry'] = get_tmpl_section('IMAGES_ENTRY', $content['picasa']['picasa_template']);
$content['picasa']['tmpl_entry_space'] = get_tmpl_section('IMAGES_ENTRY_SPACER', $content['picasa']['picasa_template']);
$content['picasa']['tmpl_row_space'] = get_tmpl_section('IMAGES_ROW_SPACER', $content['picasa']['picasa_template']);


$myPicasaParser = new picasaAPI();

$myPicasaParser->updateOption('user', $content['picasa']['picasa_userid']); // user id
#$myPicasaParser->updateOption('authkey', 'Gv1sRgCOa-g_TJ2OWJWQ');
$myPicasaParser->updateOption('authkey', $content['picasa']['picasa_authkey']);
$thumb = ! empty($content['picasa']['picasa_thumbcrop']) ? "c" : "u";
$myPicasaParser->updateOption('thumbsize', $content['picasa']['picasa_thumbsize'].$thumb);
$myPicasaParser->updateOption('imagesize', $content['picasa']['picasa_largesize']);
$myPicasaParser->updateOption('maxresults', $content['picasa']['picasa_maxresult']);
$myPicasaParser->updateOption('cachedir', PHPWCMS_RSS);

$feedUrl = $myPicasaParser->createFeedUrl($content['picasa']['picasa_albumid'], ctype_digit($content['picasa']['picasa_albumid'])); //album id
$picasa = $myPicasaParser->parseFeed($feedUrl);



$content['picasa']['picasa_template'] = $content['picasa']['tmpl_header'];
$content['picasa']['tmpl_images'] = array();

if (is_array($picasa)) {
    for ($i = 0; $i < count($picasa['main']); $i++) {
        $img = $picasa['main'][$i];
        $entry = $picasa['entry'][$i];
        $gphoto = $picasa['gphoto'][$i];
        $exif = $picasa['exif'][$i];
        
		if(array_key_exists($gphoto['id'], $content['picasa']['picasa_select']))
		{
	        $img_a = $content['picasa']['tmpl_entry'];
	        
	        $img_a = render_cnt_template($img_a, 'ZOOM', ($content['picasa']['picasa_lightbox'] ? '<!-- Zoomed -->' : ''));
	        #$img_a = render_cnt_template($img_a, 'CAPTION', $entry['description']);

	        $img_a = str_replace('{THUMB_ABS}', $img['thumbSrc'], $img_a);
	        $img_a = str_replace('{THUMB_REL}', $img['thumbSrc'], $img_a);
	        $img_a = str_replace('{THUMB_WIDTH}', $entry['thumbnail_w'], $img_a);
	        $img_a = str_replace('{THUMB_HEIGHT}', $entry['thumbnail_h'], $img_a);
	        $img_a = str_replace('{IMAGE_HEIGHT}', $entry['height'], $img_a);
	        $img_a = str_replace('{IMAGE_WIDTH}', $entry['width'], $img_a);
	        $img_a = str_replace('{IMAGE_ABS}', $img['largeSrc'], $img_a);
	        $img_a = str_replace('{IMAGE_REL}', $img['largeSrc'], $img_a);
	        $img_a = str_replace('{THUMB_NAME}', $entry['title'], $img_a);
	       # $img_a = str_replace('{ID}', $authkey, $img_a);
			if($content['picasa']['picasa_lightbox'])
				$img_a = str_replace('{IMAGE}', '<a href="'.$img['largeSrc'].'" rel="lightbox[picasa_{ID}]" title="[CAPTION]{CAPTION}[/CAPTION][CAPTION_ELSE]{IMGNAME}[/CAPTION_ELSE]"><img src="'.$img['thumbSrc'].'" width="'.$entry['thumbnail_w'].'" height="'.$entry['thumbnail_h'].'" alt="{IMGNAME}" /></a>', $img_a);
			else
				$img_a = str_replace('{IMAGE}', '<img src="'.$img['thumbSrc'].'" width="'.$entry['thumbnail_w'].'" height="'.$entry['thumbnail_h'].'" alt="{IMGNAME}" />', $img_a);
			
			$img_a = str_replace('{IMGID}', $gphoto['id'], $img_a);
			$img_a = str_replace('{IMAGE_ID}', $gphoto['id'], $img_a);
			$img_a = str_replace('{IMGNAME}', $img['title'], $img_a);
			$img_a = str_replace('{IMAGE_NAME}', $img['title'], $img_a);
			
			if($content['picasa']['picasa_nocaption']) {
				$img_a = render_cnt_template($img_a, 'CAPTION_ELSE', '');
				$img_a = render_cnt_template($img_a, 'CAPTION', '');
			} else {
				$img_a = render_cnt_template($img_a, 'CAPTION', $img['summary']);
				#$img_a = render_cnt_template($img_a, 'CAPTION_ELSE', $img['title']);
			}
			
			// search for sortkey and assign it as array key
			$sortkey = array_search($gphoto['id'], $content['picasa']['picasa_sort']);
			if($sortkey === false) $sortkey = $i;
						       
	        $content['picasa']['tmpl_images'][$sortkey] = $img_a;			
		}

        ksort($content['picasa']['tmpl_images']);
    }
    
    $content['picasa']['picasa_template'] .= implode("", $content['picasa']['tmpl_images']);
} else {
    $content['picasa']['picasa_template'] .= $picasa;
}

$content['picasa']['picasa_template'] .= $content['picasa']['tmpl_footer'];

$content['picasa']['picasa_template'] = str_replace('{ID}', $crow['acontent_id'], $content['picasa']['picasa_template']);
#$content['picasa']['picasa_template']  = str_replace('{SPACE}', $image['space'], $content['picasa']['picasa_template']);
#$content['picasa']['picasa_template']  = str_replace('{THUMB_WIDTH_MAX}', $image['tmpl_thumb_width_max'], $content['picasa']['picasa_template']);
#$content['picasa']['picasa_template']  = str_replace('{THUMB_HEIGHT_MAX}', $image['tmpl_thumb_height_max'], $content['picasa']['picasa_template']);
#$content['picasa']['picasa_template']  = str_replace('{THUMB_COLUMNS}', $image['col'], $content['picasa']['picasa_template']);
$content['picasa']['picasa_template'] = render_cnt_template($content['picasa']['picasa_template'], 'TITLE', html_specialchars($crow['acontent_title']));
$content['picasa']['picasa_template'] = render_cnt_template($content['picasa']['picasa_template'], 'SUBTITLE', html_specialchars($crow['acontent_subtitle']));
$content['picasa']['picasa_template'] = render_cnt_template($content['picasa']['picasa_template'], 'TEXT', plaintext_htmlencode($content['picasa']['picasa_albumdesc']));


$CNT_TMP .= $content['picasa']['picasa_template'];
#$CNT_TMP .= print_r(htmlspecialchars($content['picasa']['picasa_template']),1);

initSlimbox();

?>
