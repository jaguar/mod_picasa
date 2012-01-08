<?php 
/*************************************************************************************
 Copyright notice
 
 (c) 2002-2007 Oliver Georgi (oliver@phpwcms.de) // All rights reserved.
 
 This script is part of PHPWCMS. The PHPWCMS web content management system is
 free software; you can redistribute it and/or modify it under the terms of
 the GNU General Public License as published by the Free Software Foundation;
 either version 2 of the License, or (at your option) any later version.
 
 The GNU General Public License can be found at http://www.gnu.org/copyleft/gpl.html
 A copy is found in the textfile GPL.txt and important notices to the license
 from the author is found in LICENSE.txt distributed with these scripts.
 
 This script is distributed in the hope that it will be useful, but WITHOUT ANY
 WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
 PARTICULAR PURPOSE.  See the GNU General Public License for more details.
 
 This copyright notice MUST APPEAR in all copies of the script!
 *************************************************************************************/
 
// ----------------------------------------------------------------
// obligate check for phpwcms constants
if (!defined('PHPWCMS_ROOT')) {
    die("You Cannot Access This Script Directly, Have a Nice Day.");
}
// ----------------------------------------------------------------


// Glossary module content part form fields

// it's typically implemented in a 2 column table

// -> a spacer table row
//	<tr><td colspan="2"><img src="img/lines/l538_70.gif" alt="" width="538" height="1" /></td></tr>

// -> this can be used as spaceholfer
//	<tr><td colspan="2"><img src="img/leer.gif" alt="" width="1" height="6" /></td></tr>

// -> this is the tyical way to format rows with label and input
//	<tr>
//		<td align="right" class="chatlist">Field label</td>
//		<td><input type="text" value="" /></td>
//	</tr>

// current module vars are stored in $phpwcms['modules'][$content["module"]]
// var to modules path: $phpwcms['modules'][$content["module"]]['path']

// before you can use module content part vars check if value is valid and what you are expect
// when defining modules vars it is always recommend to name t "modulename_varname".

$module = $content["module"];

if ( empty($content['picasa']['picasa_template'])) {
    $content['picasa']['picasa_template'] = '';
}
if ( empty($content['picasa']['picasa_userid'])) {
    $content['picasa']['picasa_userid'] = '';
}
if ( empty($content['picasa']['picasa_albumid'])) {
    $content['picasa']['picasa_albumid'] = '';
}
if ( empty($content['picasa']['picasa_authkey'])) {
    $content['picasa']['picasa_authkey'] = '';
}
if ( empty($content['picasa']['picasa_albumdesc'])) {
    $content['picasa']['picasa_albumdesc'] = '';
}
if ( empty($content['picasa']['picasa_thumbsize'])) {
    $content['picasa']['picasa_thumbsize'] = '';
}
if ( empty($content['picasa']['picasa_largesize'])) {
    $content['picasa']['picasa_largesize'] = '640';
}

if ( empty($content['picasa']['picasa_thumbcrop'])) {
    $content['picasa']['picasa_thumbcrop'] = '';
}
if ( empty($content['picasa']['picasa_nocaption'])) {
    $content['picasa']['picasa_nocaption'] = 0;
}
if ( empty($content['picasa']['picasa_lightbox'])) {
    $content['picasa']['picasa_lightbox'] = 0;
}
if ( empty($content['picasa']['picasa_select'])) {
    $content['picasa']['picasa_select'] = array();
}
if ( empty($content['picasa']['picasa_sort'])) {
    $content['picasa']['picasa_sort'] = array();
}
if ( empty($content['picasa']['picasa_maxresult'])) {
    $content['picasa']['picasa_maxresult'] = "50";
}
$out = array();

// include api class
if (! empty($content['picasa']['picasa_albumid']) && ! empty($content['picasa']['picasa_userid'])) {

    require_once ('picasaAPI.php');
    
    $myPicasaParser = new picasaAPI();
    
    $myPicasaParser->updateOption('user', $content['picasa']['picasa_userid']); // user id
    #$myPicasaParser->updateOption('authkey', 'Gv1sRgCOa-g_TJ2OWJWQ');
    $myPicasaParser->updateOption('authkey', $content['picasa']['picasa_authkey']);
    $myPicasaParser->updateOption('thumbsize', '110c');
    $myPicasaParser->updateOption('cachedir', PHPWCMS_RSS);
    $myPicasaParser->updateOption('maxresults', '1000');
    $myPicasaParser->updateOption('prettyprint', 'true');
    
    $feedUrl = $myPicasaParser->createFeedUrl($content['picasa']['picasa_albumid'], ctype_digit($content['picasa']['picasa_albumid'])); //album id
    $picasa = $myPicasaParser->parseFeed($feedUrl);
    
    if (is_array($picasa)) {
		
		$albumlink = '<a target="_blank" href="http://picasaweb.google.de/'.$content['picasa']['picasa_userid'].'/'.$content['picasa']['picasa_albumid'].'?authkey='.$content['picasa']['picasa_authkey'].'">'.$BL['modules'][$content["module"]]['albumlinklabel'].'</a>';
    	$out[] = '<p><input name="toggle" type="button" id="toggle" value="'.$BL['modules'][$content["module"]]['select'].'" /><p>';
    	$out[] = '<p class="chatlist">'.$BL['modules'][$content["module"]]['albuminfo'].'<p>';
        $out[] = '<div id="albumcontent">';
		for ($i = 0; $i < count($picasa['main']); $i++) {
        
            $img = $picasa['main'][$i];
            $entry = $picasa['entry'][$i];
            $gphoto = $picasa['gphoto'][$i];
            $exif = $picasa['exif'][$i];
            
			$ik = array('title','published','updated','author', 'rights');
			$ek = array('description','keywords');
			$gk = array();
			$xk = array();
			
			$info = 'Infos:<dl>';
			foreach($img as $key => $value){
				if(in_array($key,$ik))
				$info .= '<dt>'.$key.'</dt><dd>'.$value.'</dd>';
			}
			foreach($entry as $key => $value){
				if(in_array($key,$ek))
				$info .= '<dt>'.$key.'</dt><dd>'.$value.'</dd>';
			}
			foreach($gphoto as $key => $value){
				if(in_array($key,$gk))
				$info .= '<dt>'.$key.'</dt><dd>'.$value.'</dd>';
			}
			foreach($exif as $key => $value){
				if(in_array($key,$xk))
				$info .= '<dt>'.$key.'</dt><dd>'.$value.'</dd>';
			}
			$info .= '</dl>';
			// search for sortkey and assign it as array key
			$sortkey = array_search($gphoto['id'], $content['picasa']['picasa_sort']);
			if($sortkey === false) $sortkey = $i;
			
            $eout[$sortkey] = '<div class="imageEntry image_gallery_thumb">';
            $eout[$sortkey] .= '<span class="imageInfo">'.$info.'</span>';
			$eout[$sortkey] .= '<span class="imagewrap">';
            $eout[$sortkey] .= '<label for="picasa_select_'.$gphoto['id'].'">';
            $eout[$sortkey] .= '<img src="'.$img['thumbSrc'].'" alt="'.$entry['title'].'" border="0" />';
            $eout[$sortkey] .= '</label>';
            $eout[$sortkey] .= '<input type="checkbox" class="picasa_select checkBox" name="picasa_select['.$gphoto['id'].']" id="picasa_select_'.$gphoto['id'].'" value="1" '.is_checked(1, $content['picasa']['picasa_select'][$gphoto['id']], 1, 0).' />';
            $eout[$sortkey] .= '<span class="imginfo">[i] '. cut_string($img['title'],"&hellip;", 8).'</span>';
            $eout[$sortkey] .= '<input type="hidden" name="picasa_sort[]" value="'.$gphoto['id'].'" />'; // send unique id for later sort
            $eout[$sortkey] .= '</span>';
            $eout[$sortkey] .= '</div>';
            
        }
		
		ksort($eout);
		
		$out[] = implode("",$eout);
        $out[] = '<div style="clear:both;"></div></div>';
        $out['end1'] = '<p style="clear:both;"><a target="_blank" href="http://picasaweb.google.de/'.$content['picasa']['picasa_userid'].'/'.$content['picasa']['picasa_albumid'].'?authkey='.$content['picasa']['picasa_authkey'].'">See Online Album</a></p>';
        $out['end2'] = '<div class="f11" rows="5" style="width: 440px;height:500px;white-space:pre;overflow:auto">'.print_r($picasa, 1).'</div>';
    } else {
        $out['error'] = $picasa;
    }

    
}
$BE['HEADER']['backend.css'] = '	<link href="'.$phpwcms['modules'][$module]['dir'].'template/css/backend.css" rel="stylesheet" type="text/css">';
$BE['BODY_CLOSE'][] = '<script language="javascript" type="text/javascript">document.getElementById("target_ctype").disabled = true;</script>';
$BE['HEADER']['jquery.js'] = '<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>';
$BE['HEADER']['backend.js'] = '<script type="text/javascript" src="'.$phpwcms['modules'][$module]['dir'].'template/js/backend.js"></script>';

?>
<!-- top spacer - seperate from title/subtitle section -->
<tr>
    <td colspan="2" class="rowspacer0x7"></td>
</tr>

<tr>
    <td>&nbsp;</td>
    <td valign="top" class="chatlist tdbottom5 tdtop3"><b><?php echo $BL['modules'][$content["module"]]['section_basic']?></b></td>
</tr>

<tr>
    <td align="right" class="chatlist"><?php echo $BL['modules'][$content["module"]]['userid']?>:&nbsp;</td>
    <td class="tdbottom3 tdtop3"><input type="text" name="picasa_userid" id="picasa_userid" value="<?php echo html_specialchars($content['picasa']['picasa_userid']) ?>" class="f11b" style="width: 440px" maxlength="1000" /></td>
</tr>
<tr>
    <td align="right" class="chatlist"><?php echo $BL['modules'][$content["module"]]['albumid']?>:&nbsp;</td>
    <td class="tdbottom3 tdtop3"><input type="text" name="picasa_albumid" id="picasa_albumid" value="<?php echo html_specialchars($content['picasa']['picasa_albumid']) ?>" class="f11b" style="width: 440px" maxlength="1000" /></td>
</tr><!-- end field -->
<tr>
    <td align="right" class="chatlist"><?php echo $BL['modules'][$content["module"]]['authkey']?>:&nbsp;</td>
    <td class="tdbottom3 tdtop3"><input name="picasa_authkey" type="text" class="f11b" id="picasa_authkey" style="width: 440px;" size="5" maxlength="255" value="<?php echo html_specialchars($content['picasa']['picasa_authkey']) ?>" /></td>
</tr>
<tr>
    <td colspan="2" class="rowspacer10x10"></td>
</tr>
<tr>
    <td>&nbsp;</td>
    <td valign="top" class="chatlist tdbottom5 tdtop3"><b><?php echo $BL['modules'][$content["module"]]['album']?></b></td>
</tr>
<!-- retrieve templates -->
<tr>
    <td align="right" class="chatlist"><?php echo $BL['be_admin_struct_template']?>:&nbsp;</td>
    <td class="tdbottom3 tdtop3"><select name="picasa_template" id="picasa_template" class="f11b">
            <?php 
            echo '<option value="">'.$BL['be_admin_tmpl_default'].'</option>'.LF;
            
            // templates from image <div> CP
            $tmpllist = get_tmpl_files(PHPWCMS_TEMPLATE.'inc_cntpart/images');
            if (is_array($tmpllist) && count($tmpllist)) {
                foreach ($tmpllist as $val) {
                    $selected_val = (isset($content['picasa']['picasa_template']) && $val == $content['picasa']['picasa_template']) ? ' selected="selected"' : '';
                    $val = html_specialchars($val);
                    echo '	<option value="'.$val.'"'.$selected_val.'>'.$val.'</option>'.LF;
                }
            }
            
            ?>
           </select>
	</td>
</tr>
<tr>
    <td align="right" class="chatlist"><?php echo $BL['modules'][$content["module"]]['thumbsize']?>:&nbsp;</td>
    <td class="tdbottom3 tdtop3">
    	<!--
		<select name="picasa_thumbsize" id="picasa_thumbsize" class="f11b">
            <?php 
            $sizes = array(32, 48, 64, 72, 104, 144, 150, 160);
            foreach ($sizes as $val) {
                $selected_val = (isset($content['picasa']['picasa_thumbsize']) && $val == $content['picasa']['picasa_thumbsize']) ? ' selected="selected"' : '';
                $val = html_specialchars($val);
                echo '	<option value="'.$val.'"'.$selected_val.'>'.$val.'</option> '.LF;
            }
            
            
            ?>
         </select> px &nbsp;
		 -->
		 <input name="picasa_thumbsize" type="text" class="f11b" id="picasa_thumbsize" style="width: 50px;" size="5" maxlength="5" value="<?php echo html_specialchars($content['picasa']['picasa_thumbsize']) ?>" />
		 px width		 
		 <input type="checkbox" name="picasa_thumbcrop" id="picasa_thumbcrop" value="1"<?php is_checked(1, $content['picasa']['picasa_thumbcrop']); ?>/> 
		 <label for="picasa_thumbcrop" class="chatlist"><?php echo $BL['modules'][$content["module"]]['thumbcrop']?></label></td>
</tr>
<tr>
    <td align="right" class="chatlist"></td>
    <td class="tdbottom3 tdtop3">		 
		 <input type="checkbox" name="picasa_nocaption" id="picasa_nocaption" value="1"<?php is_checked(1, $content['picasa']['picasa_nocaption']); ?>/> 
		 <label for="picasa_nocaption" class="chatlist"><?php echo $BL['be_cnt_imglist_nocaption'] ?></label></td>
</tr>
<tr>
    <td align="right" class="chatlist"></td>
    <td class="tdbottom3 tdtop3">		 
		 <input type="checkbox" name="picasa_lightbox" id="picasa_lightbox" value="1"<?php is_checked(1, $content['picasa']['picasa_lightbox']); ?>/> 
		 <label for="picasa_lightbox" class="chatlist"><?php echo $BL['be_cnt_lightbox'] ?></label></td>
</tr>

<tr>
    <td align="right" class="chatlist"><?php echo $BL['modules'][$content["module"]]['largesize']?>:&nbsp;</td>
    <td class="tdbottom3 tdtop3">
    	<!--
    	<select name="picasa_largesize" id="picasa_largesize" class="f11b">
            <?php 
            $sizes = array(94, 110, 128, 200, 220, 288, 320, 400, 512, 576, 640, 720, 800, 912, 1024, 1152, 1280, 1440, 1600);
            foreach ($sizes as $val) {
                $selected_val = (isset($content['picasa']['picasa_largesize']) && $val == $content['picasa']['picasa_largesize']) ? ' selected="selected"' : '';
                $val = html_specialchars($val);
                echo '	<option value="'.$val.'"'.$selected_val.'>'.$val.'</option> '.LF;
            }
            
            
            ?>
         </select> 
		 -->
		 <input name="picasa_largesize" type="text" class="f11b" id="picasa_largesize" style="width: 50px;" size="5" maxlength="5" value="<?php echo html_specialchars($content['picasa']['picasa_largesize']) ?>" />
		 px width
	</td>
</tr>
<!-- end templates -->
<tr>
    <td align="right" class="chatlist" style="padding:3px 5px 0 0" valign="top"><?php echo $BL['modules'][$content["module"]]['albumdesc']?>:</td>
    <td class="tdbottom3 tdtop3">
        <textarea name="picasa_albumdesc" id="picasa_albumdesc" class="f11" rows="5" style="width: 440px"><?php echo html_specialchars($content['picasa']['picasa_albumdesc'])?></textarea>
    </td>
</tr>
<tr>
    <td align="right" class="chatlist" style="padding:3px 5px 0 0" valign="top"><?php echo $BL['modules'][$content["module"]]['maxresult']?>:</td>
    <td class="tdbottom3 tdtop3"><input name="picasa_maxresult" type="text" class="f11b" id="picasa_maxresult" style="width: 50px;" size="5" maxlength="5" value="<?php echo html_specialchars($content['picasa']['picasa_maxresult']) ?>" />
	<label for="picasa_maxresult" class="chatlist"> <?php echo $BL['modules'][$content["module"]]['numphotos']." ".$picasa['feed_info']['numphotos']?></label>
    </td>
</tr>

<!-- line spacer -->
<tr><td colspan="2" class="rowspacer10x10"></td></tr>
<!-- section headline -->
<tr>
    <td>&nbsp;</td>
    <td valign="top" class="chatlist tdbottom5 tdtop3"><b><?php echo $BL['modules'][$content["module"]]['section_albumcontent']?></b></td>
</tr>

<?php if(is_array($out) || !isset($out['error'])): ?>

<tr>
    <td align="right" class="chatlist"></td>
    <td class="tdbottom3 tdtop3">
    		<?php echo (! empty($albumlink))?$albumlink:'no desc'; ?>
    </td>
</tr>
<tr>
    <td align="right" class="chatlist"><?php echo $BL['modules'][$content["module"]]['desclabel'];?>:&nbsp;</td>
    <td class="tdbottom3 tdtop3">
    	<p class="v10">
    		<?php echo (! empty($picasa['feed_info']['subtitle']))?$picasa['feed_info']['subtitle']:'no desc'; ?>
		</p>
    </td>
</tr>
<tr><td colspan="2" class="rowspacer7x7"></td></tr>
<tr>
    <td align="right" class="chatlist  tdbottom5 tdtop3"></td>
    <td class="tdbottom3 tdtop3">
        <?php 
        echo implode("", $out);
        ?>
    </td>
</tr>

<?php else: ?>
<tr>
    <td align="right" class="chatlist  tdbottom5 tdtop3"></td>
    <td class="tdbottom3 tdtop3">
    	
        <?php 
        echo implode("", $out);
        ?>
		
    </td>
</tr>
<?php endif; ?>

<!-- end custom fields --><!-- bottom spacer - is followed by status "visible" checkbox -->
<tr>
    <td colspan="2" style="padding-top:8px"><img src="img/lines/l538_70.gif" alt="" width="538" height="1" /></td>
</tr>