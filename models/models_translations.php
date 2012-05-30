<?php

$model['translation_user']=new Webmodel('translation_user');

$model['translation_user']->components['iduser']=new ForeignKeyField('user');

$model['translation']=new Webmodel('translation');

$model['translation']->components['name']=new CharField(255);
$model['translation']->components['name']->required=1;

$model['translation']->components['module']=new CharField(255);
//$model['translation']->components['module']->required=1;

$model['translation']->components['translation']=new SerializeField();
$model['translation']->components['translation']->required=1;

$model['translation']->components['lang']=new CharField(255);
$model['translation']->components['lang']->required=1;

$arr_module_insert['translations']=array('name' => 'translations', 'admin' => 1, 'admin_script' => array('translations', 'translations'), 'load_module' => '', 'order_module' => 0, 'required' => 0, 'app_index' => 1);

$arr_module_remove['translations']=array('translation_user');

?>