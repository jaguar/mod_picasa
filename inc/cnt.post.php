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


// picasa module handle content part POST values

$content['picasa'] = array();
$content['picasa']['picasa_template']	= clean_slweg($_POST['picasa_template']);
$content['picasa']['picasa_albumid']	= clean_slweg($_POST['picasa_albumid']);
$content['picasa']['picasa_userid']		= clean_slweg($_POST['picasa_userid']);
$content['picasa']['picasa_authkey']	= clean_slweg($_POST['picasa_authkey']);
$content['picasa']['picasa_albumdesc']	= clean_slweg($_POST['picasa_albumdesc']);
$content['picasa']['picasa_thumbsize']	= intval($_POST['picasa_thumbsize']);
$content['picasa']['picasa_thumbcrop']	= empty($_POST['picasa_thumbcrop'])? 0 : 1 ;
$content['picasa']['picasa_nocaption']	= empty($_POST['picasa_nocaption'])? 0 : 1 ;
$content['picasa']['picasa_lightbox']	= empty($_POST['picasa_lightbox'])? 0 : 1 ;
$content['picasa']['picasa_largesize']	= intval($_POST['picasa_largesize']);
$content['picasa']['picasa_select']		= is_array($_POST['picasa_select'])?$_POST['picasa_select']:array();
$content['picasa']['picasa_maxresult']	= intval($_POST['picasa_maxresult']);
$content['picasa']['picasa_sort']		= $_POST['picasa_sort'];
?>