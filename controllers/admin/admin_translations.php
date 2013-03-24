<?php

function TranslationsAdmin()
{

	global $lang, $base_url, $model, $base_path;

	settype($_GET['op'], 'integer');

	load_libraries(array('generate_admin_ng', 'forms/selectmodelform'));
	load_model('translations');
	//load_lang('translat');

	$arr_fields=array('iduser');
	$arr_fields_edit=array('iduser');
	$url_options=make_fancy_url($base_url, 'admin', 'index', 'blogs', array('IdModule' => $_GET['IdModule']));

	$model['translation_user']->create_form();

	$model['translation_user']->forms['iduser']->label=$lang['common']['user'];

	$model['translation_user']->forms['iduser']->form='SelectModelForm';
	$model['translation_user']->forms['iduser']->parameters=array('iduser', '', '', 'user', 'private_nick', $where='where privileges_user=1 order by private_nick ASC');

	//$model['translation_user']->components['iduser']->fields_related_model=array('private_nick');

	$model['translation_user']->components['iduser']->name_field_to_field='private_nick';
	
	generate_admin_model_ng('translation_user', $arr_fields, $arr_fields_edit, $url_options, $options_func='BasicOptionsListModel', $where_sql='', $arr_fields_form=array(), $type_list='Basic');

}

?>