<?php

global $lang;

load_lang('translations');

$select_page[]=$lang['translations']['translations'];
$select_page[]='optgroup';

$select_page[]=$lang['translations']['translations'];
$select_page[]=make_fancy_url($base_url, 'translations', 'index', 'translations', $arr_data=array());

$select_page[]=''; 
$select_page[]='end_optgroup';

?>
