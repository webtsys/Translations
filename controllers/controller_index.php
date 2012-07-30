<?php

function Index()
{

	global $user_data, $model, $ip, $lang, $config_data, $base_path, $base_url, $cookie_path, $arr_block, $prefix_key, $block_title, $block_content, $block_urls, $block_type, $block_id, $config_data, $language, $arr_i18n;

	//You need configure two lang for use translations...

	load_lang('translations');
	load_model('translations');
	load_libraries(array('utilities/menu_selected'));
	load_libraries(array('check_admin'));

	$arr_block=select_view(array('translations'));

	$arr_block='/none';

	$yes_user=0;

	$num_user=$model['translation_user']->select_count('where IdUser='.$user_data['IdUser'], 'IdTranslation_user');

	if($num_user>0)
	{

		$yes_user=1;

	}

	if(check_admin($user_data['IdUser']))
	{

		$yes_user=1;

	}

	if($yes_user==1)
	{
	
		settype($_GET['module'], 'string');
		settype($_GET['op'], 'integer');
		
		$_GET['module']=basename(form_text($_GET['module']));

		?>
		<p><a href="<?php echo make_fancy_url($base_url, 'translations', 'index', 'download_translations', array('op' => 2)); ?>"><?php echo $lang['translations']['download_translations']; ?></a></p>
		<?php

		$directory=$base_path.'modules/';

		$arr_modules['']['text']=$lang['translations']['index'];
		$arr_modules['']['link']= make_fancy_url($base_url, 'translations', 'index', 'translations', array());

		if($_GET['op']>1)
		{

			$_GET['module']='none';

		}

		if( false !== ($handledir=opendir($directory)) ) 
		{

			while (false !== ($file = readdir($handledir))) 
			{

				$path_file=$directory.$file;

				//echo $file.'<p>';
				if( is_dir($path_file) && !preg_match("/^(.*)\.$/", $path_file) && !preg_match("/^\.(.*)$/", $path_file) ) 
				{

					//echo '<li><a href="'.make_fancy_url($base_url, 'translations', 'index', 'translations', array('module' => $file)).'">'.$file.'</a></li>';

					$arr_modules[$file]['text']=$file;
					$arr_modules[$file]['link']= make_fancy_url($base_url, 'translations', 'index', 'translations', array('module' => $file));

				}

			}

		}

		menu_selected($_GET['module'], $arr_modules);

		//Two forms: translation from (if not translate), translation to, lang to translate.
		
		$directory_i18n=$directory.$_GET['module'].'/i18n/'.$language.'/';

		if(!file_exists($directory_i18n) && ($_GET['module']=='' || $_GET['module']=='none'))
		{

			$directory_i18n=$base_path.'/i18n/'.$language.'/';

		}
		
		$arr_files=array();

		if(file_exists($directory_i18n))
		{

			switch($_GET['op'])
			{

			default:
			
				//Include lang...

				if( false !== ( $handledir=opendir($directory_i18n) ) ) 
				{

					while (false !== ($file = readdir($handledir))) 
					{
						$base_file=str_replace('.php', '', $file);

						$path_file=$directory_i18n.$file;

						if( !is_dir($path_file) && !preg_match("/^(.*)~$/", $path_file)) 
						{
						
							$arr_files[$base_file]['text']=$base_file;
							$arr_files[$base_file]['link']=make_fancy_url($base_url, 'translations', 'index', 'translations', array('module' => $_GET['module'], 'file_lang' => $base_file));
							$arr_files[$base_file]['path']=$path_file;
			
						}

					}

				}

				echo '<h3>'.$lang['translations']['files_to_translate'].'</h3>';

				settype($_GET['file_lang'], 'string');

				$_GET['file_lang']=basename(form_text($_GET['file_lang']));

				$file_lang=$_GET['file_lang'];

				menu_selected($file_lang, $arr_files);

				if(isset($arr_files[$file_lang]))
				{
					//Language selection..

					//Here begin the translation...

					settype($_SESSION['translate_from'], 'string');
					settype($_SESSION['translate_to'], 'string');

					$_POST['translate_from']=@check_name_lang($_POST['translate_from']);
					$_POST['translate_to']=@check_name_lang($_POST['translate_to']);

					if(in_array($_POST['translate_from'], $arr_i18n))
					{

						$_SESSION['translate_from']=$_POST['translate_from'];

					}

					if(!in_array($_SESSION['translate_from'], $arr_i18n))
					{

						$_SESSION['translate_from']=$language;

					}

					if(in_array($_POST['translate_to'], $arr_i18n))
					{

						$_SESSION['translate_to']=$_POST['translate_to'];

					}

					if(!in_array($_SESSION['translate_to'], $arr_i18n) )
					{

						//Need next to choose lang...
						//Next or prev lang to $language in arr_i18n

						$key_default=array_search($language, $arr_i18n);

						$key_default++;

						if(!isset($arr_i18n[$key_default]))
						{

							$key_default-=2;
							//$arr_i18n[$key_default]-=2;

						}

						$_SESSION['translate_to']=$arr_i18n[$key_default];

					}

					$arr_lang_selected=array($_SESSION['translate_from']);
					
					foreach($arr_i18n as $i18n)
					{

						$arr_lang_selected[]=$i18n;
						$arr_lang_selected[]=$i18n;

					}

					/*$_SESSION['translate_from']=$_POST['translate_from'];
					$_SESSION['translate_to']=$_POST['translate_to'];*/

					/*if(!in_array($_POST['translate_from'], $arr_i18n))
					{
						
						$_SESSION['translate_from']=$language;

					}
					else
					{

						$_SESSION['translate_from']=$_POST['translate_from'];

					}

					if(!in_array($_POST['translate_to'], $arr_i18n))
					{

						//Need next to choose lang...
						//Next or prev lang to $language in arr_i18n

						$_SESSION['translate_to']=$arr_lang_selected[3];

					}
					else
					{

						$_SESSION['translate_to']=$_POST['translate_to'];

					}

					if($_SESSION['translate_to']==$_SESSION['translate_from'])
					{

						$_SESSION['translate_to']=$arr_lang_selected[3];

					}*/

					?>
					<form method="post" action="<?php echo make_fancy_url($base_url, 'translations', 'index', 'translate_post', array('translate_to' => $_SESSION['translate_to'], 'module' => $_GET['module'], 'file_lang' => $_GET['file_lang'])); ?>">
					<?php echo $lang['translations']['translate_from']; ?>: <?php echo SelectForm('translate_from', '', $arr_lang_selected); ?> <?php echo $lang['translations']['translate_to']; ?>: 
					<?php 
						$arr_lang_selected[0]=$_SESSION['translate_to'];
						echo SelectForm('translate_to', '', $arr_lang_selected); 
					?>
					<?php set_csrf_key(); ?>
					<input type="submit" value="<?php echo $lang['common']['send']; ?>"/>
					</form>	
					<?php

					//Check if exists in database...
					//Load row from database using translate_to

					/*$query=$model['translation']->select('where module="'.$_GET['module'].'" and name="'.$file_lang.'" and lang="'.$_POST['translate_to'].'"', array('IdTranslation', 'translation'));

					list($idtranslation, $ser_translation)=webtsys_fetch_row($query);
			
					settype($idtranslation, 'integer');

					if($idtranslation==0)
					{

						//Load file lang by default for put in database...
						//Change language a one moment...

						$lang_process[$file_lang]=eval_lang($_GET['module'], $file_lang, $_POST['translate_to']);

						//$_SESSION['language']=$language_old;

						$ser_translation=serialize($lang_process[$file_lang]);

						//Insert into database

						$post_lang=array('module' => $_GET['module'], 'name' => $file_lang, 'lang' => $_POST['translate_to'], 'translation' => $lang_process[$file_lang]);

						$query=$model['translation']->insert($post_lang);

					}

					$lang_file[$file_lang]=unserialize($ser_translation);*/

					$lang_file=load_lang_db($_GET['module'], $file_lang, $_SESSION['translate_to']);
					
					//Load lang_file of translate_from

					//$lang_from[$file_lang]=eval_lang($_GET['module'], $file_lang, $_POST['translate_from']);

					$lang_from=load_lang_db($_GET['module'], $file_lang, $_SESSION['translate_from']);

					//Begin translation

					echo '<h3>'.$lang['translations']['translate'].' '.$file_lang.'</h3>';
					
					if(count($lang_file)==0)
					{

						echo '<p>'.$lang['translations']['no_lang_variable_here'].'</p>';

					}
					else
					{
					?>	
					<form method="post" class="form" action="<?php echo make_fancy_url($base_url, 'translations', 'index', 'translate_post', array('op' => 1, 'translate_to' => $_SESSION['translate_to'], 'module' => $_GET['module'], 'file_lang' => $_GET['file_lang'])); ?>">
						<?php

						$z=0;
						
						foreach(array_keys($lang_file) as $key_file_lang)
						{
							
							foreach($lang_file[$key_file_lang] as $name => $text)
							{
								

								$danger='';

								if($name==$text )
								{

									if(isset($lang_from[$key_file_lang][$name]))
									{

										$text=$lang_from[$key_file_lang][$name];

									}
									else
									{

										$danger='<span class="error">'.$lang['translations']['error_field_probably_is_trash'].'</span>';

									}

								}

								echo '<p><label for="'.$name.'">'.$name.' '.$danger.':</label>'.TextAreaForm('lang['.$key_file_lang.']['.$name.']', '', $text).'</p>';

								$z++;

							}
							
						}
						?>
						<?php set_csrf_key(); ?>
						<p><input type="submit" value="<?php echo $lang['common']['send']; ?>"/>
					</form>
					<?php
					}
				}

			break;

			case 1:

				$get['name']=check_name_lang($_GET['file_lang']);
				$get['module']=check_name_lang($_GET['module']);
				$get['lang']=check_name_lang($_GET['translate_to']);
				
				$url=make_fancy_url($base_url, 'translations', 'index', 'translate_post', array('translate_to' => $get['lang'], 'module' => $get['module'], 'file_lang' => $get['name']));

				settype($_POST['lang'], 'array');

				$post['translation']=$_POST['lang'];
				
				$model['translation']->reset_require();

				if($model['translation']->update($post, 'where name="'.$get['name'].'" and module="'.$get['module'].'" and lang="'.$get['lang'].'"'))
				{

					ob_end_clean();

					load_libraries(array('redirect'));

					die( redirect_webtsys( $url, $lang['common']['redirect'], $lang['common']['success'], $lang['common']['press_here_redirecting'] , $arr_block) );

				}

			break;

			case 2:

				$arr_lang_selected[]=$language;
					
				foreach($arr_i18n as $i18n)
				{

					$arr_lang_selected[]=$i18n;
					$arr_lang_selected[]=$i18n;

				}

				?>
				<form method="post" action="<?php echo make_fancy_url($base_url, 'translations', 'index', 'translate_post', array( 'op' => 3)); ?>">
				<?php echo $lang['translations']['translation']; ?>: <?php echo SelectForm('translation', '', $arr_lang_selected); ?> 
				<?php
				
					$module_defined=array();
				
					$query=$model['module']->select('', array('name', 'admin_script'));
					
					while(list($module_name, $ser_admin_script)=webtsys_fetch_row($query))
					{
					
						$arr_admin_script=unserialize($ser_admin_script);
						
						if( !isset($module_defined[$arr_admin_script[0]]) )
						{
					
							?><p>
							<?php echo $arr_admin_script[0]; ?>: <?php echo CheckBoxForm('module_selected['.$arr_admin_script[0].']', $class='', 1); ?>
							</p>
							<?php
							
							$module_defined[$arr_admin_script[0]]=1;
						
						}
					
					}
					
				
				?>
				<?php set_csrf_key(); ?>
				<input type="submit" value="<?php echo $lang['common']['send']; ?>"/>
				</form>	
				<?php

			break;

			case 3:

				$lang_translate=@check_name_lang($_POST['translation']);

				echo '<h3>'.$lang['translations']['download_translations'].'</h3>';

				if(in_array($lang_translate, $arr_i18n))
				{

					//First, copy all files from lang..., after rewrite.
				
					//download translate...
					
					//Copy 
					
					//print_r($_POST['module_selected']);
					
					$arr_modules_selected_in_db=array();
					
					$arr_modules_selected=array_keys($_POST['module_selected']);
					
					$arr_modules_selected[]='\'\'';
					
					//print_r($arr_modules_selected);

					$query=$model['translation']->select('where lang="'.$lang_translate.'" and module IN (\''.implode('\', \'', $arr_modules_selected).'\')');

					while($arr_fields=webtsys_fetch_array($query))
					{

						/*
						$model['translation']->components['name']=new CharField(255);
						$model['translation']->components['name']->required=1;

						$model['translation']->components['module']=new CharField(255);
						$model['translation']->components['module']->required=1;

						$model['translation']->components['translation']=new SerializeField();
						$model['translation']->components['translation']->required=1;

						$model['translation']->components['lang']=new CharField(255);
						$model['translation']->components['lang']->required=1;
						*/

						$arr_trans=unserialize($arr_fields['translation']);

						$arr_cont_file=array();

						$dir_path=$base_path.'modules/translations/backup/modules/'.$arr_fields['module'].'/i18n/'.$arr_fields['lang'].'/';

						if($arr_fields['module']=='')
						{

							$dir_path=$base_path.'modules/translations/backup/i18n/'.$arr_fields['lang'].'/';

						}

						$file_path=$dir_path.$arr_fields['name'].'.php';

						foreach(array_keys($arr_trans) as $key_file_lang)
						{
							foreach($arr_trans[$key_file_lang] as $key_trans => $value_trans)
							{

								$arr_cont_file[]='$lang[\''.$key_file_lang.'\'][\''.$key_trans.'\']=\''.$value_trans.'\';'."\n";
								
							}

						}

						$cont_file="<?php\n\n".implode("\n", $arr_cont_file)."\n?>\n";
						
						
						$yes_dir=1;

						if(!file_exists($dir_path))
						{

							$yes_dir=mkdir($dir_path, 0755, true);

			
						}
						
						if(!$yes_dir)
						{
							
							echo '<p>'.$lang['translations']['error_cannot_mkdir'].' '.$dir_path.'</p>';
							
							break;
						
						}

						$file=fopen ($file_path, 'w');
					
						if($file!==false) 
						{
						
							echo "<p>--->".$lang['translations']['write_in_lang_file'].": ".$file_path."...</p>\n";
						
							if(fwrite($file, $cont_file)==false) 
							{
							
								echo $lang['translations']['error_cannot_write_file'].": $path_lang_file\n";
								die;
							
							}
						
							fclose($file);
						
						}
						
						$arr_modules_selected_in_db[$arr_fields['module']]=1;

					}
					
					//print_r($arr_modules_selected_in_db);
					
					//Know, need copy the rest of files translation...
					
					$arr_modules_final=array_diff($arr_modules_selected, array_keys($arr_modules_selected_in_db));
					
					unset($arr_modules_final[count($arr_modules_final)]);

					foreach($arr_modules_final as $module_copy)
					{
					
						$dir_path_base=$base_path.'modules/'.$module_copy.'/i18n/'.$lang_translate.'/';
						$dir_path_copy=$base_path.'modules/translations/backup/modules/'.$module_copy.'/i18n/'.$lang_translate.'/';
						
						$yes_dir=mkdir($dir_path_copy, 0755, true);
						
						ob_start();
						
						passthru ( 'cp -r '.$dir_path_base.'/* '.$dir_path_copy, $return_var );
						
						$result_copy=ob_get_contents();
						
						ob_end_clean();
						echo '<p>Copying '.$module_copy.'...</p>';
					
					}
					
					//Copy base files...
					
					/*$dir_path_base=$base_path.'/i18n/'.$lang_translate.'/';
					$dir_path_copy=$base_path.'modules/translations/backup/i18n/'.$lang_translate.'/';
					
					$yes_dir=mkdir($dir_path_copy, 0755, true);
						
					ob_start();
					
					passthru ( 'cp -r '.$dir_path_base.'/* '.$dir_path_copy, $return_var );
					
					$result_copy=ob_get_contents();
					
					ob_end_clean();
					echo '<p>Copying base files...</p>';*/
					
					//Know compressing directory...

					//Delete old tars...

					@unlink($base_path.'/application/media/translations/translations_'.$lang_translate.'.tar.gz');

					//Create tar.gz
					
					if(!class_exists('PharData', false))
					{
					
						echo '<p>'.$lang['translations']['you_need_phardata_class'].'</p>';
					
						break;
					
					}
					
					function check_error_phar()
					{
					
						global $lang, $base_path;
					
						//echo '<p>'.$lang['translations']['error_cannot_write_translations_tar'].' '.$base_path.'/application/media/translations/'.'</p>';
						show_error('<p>'.$lang['translations']['error_cannot_write_translations_tar'].'</p>', '<p>'.$lang['translations']['error_cannot_write_translations_tar'].' '.$base_path.'/application/media/translations/'.'</p>', $output_external='');
					
						die;
					
					}
					
					set_exception_handler('check_error_phar');
						
					$phar = new PharData($base_path.'/application/media/translations/translations_'.$lang_translate.'.tar');

					// add all files in the project

					$phar->buildFromDirectory($base_path.'modules/translations/backup/');

					$phar_bz2 = $phar->compress(Phar::GZ);

					@unlink($base_path.'/application/media/translations/translations_'.$lang_translate.'.tar');

					//Clean work directory...
					
					clean_directory($base_path.'modules/translations/backup/i18n/');
					clean_directory($base_path.'modules/translations/backup/modules/');

					//echo '<pre>'.htmlentities($cont_file).'</pre>';

					/*echo $file_path;

					echo '<p>';*/

					//Link to download file...

					echo '<p>'.$lang['translations']['if_error_you_have_put_permissions'].'</p>';

					echo '<p><a href="'.$base_url.'/media/translations/translations_'.$lang_translate.'.tar.gz">'.$lang['translations']['download_lang_file'].'</a></p>';

				}

			break;

			}

		}
		else
		{

			echo '<p>'.$lang['translations']['no_exists_i18n_here'].'</p>';

		}

		$cont_trans.=ob_get_contents();

		ob_clean();

		echo load_view(array($lang['translations']['translations'], $cont_trans), 'content');
		
		$cont_index.=ob_get_contents();

		ob_end_clean();

		echo load_view(array($lang['translations']['translations'], $cont_index, $block_title, $block_content, $block_urls, $block_type, $block_id, $config_data, ''), $arr_block);

	}
	else
	{

		die( header ('Location: '.make_fancy_url( $base_url, 'user', 'index', 'index', array('register_page' => 'translations') ) ) );

	}

}

function check_name_lang($name)
{

	settype($name, 'string');

	return basename(form_text($name));

}

function eval_lang($module, $name, $language)
{

	global $base_path;

	$file_path=$base_path.'modules/'.$module.'/i18n/'.$language.'/'.$name.'.php';

	if(!file_exists($file_path))
	{

		$file_path=$base_path.'/i18n/'.$language.'/'.$name.'.php';

	}

	$file_cont=file_get_contents($file_path);

	$file_cont=preg_replace('/^<\?php/', '$1', $file_cont);
	$file_cont=preg_replace('/\?>$/', '', $file_cont);

	eval($file_cont);

	settype($lang, 'array');

	return $lang;

}

function load_lang_db($module, $file_lang, $language)
{

	global $model;

	$query=$model['translation']->select('where module="'.$module.'" and name="'.$file_lang.'" and lang="'.$language.'"', array('IdTranslation', 'translation'));

	list($idtranslation, $ser_translation)=webtsys_fetch_row($query);

	settype($idtranslation, 'integer');

	if($idtranslation==0)
	{

		//Load file lang by default for put in database...
		//Change language a one moment...

		$lang_process[$file_lang]=eval_lang($module, $file_lang, $language);

		$ser_translation=serialize($lang_process[$file_lang]);

		//Insert into database

		$post_lang=array('module' => $module, 'name' => $file_lang, 'lang' => $language, 'translation' => $lang_process[$file_lang]);

		$query=$model['translation']->insert($post_lang);

	}

	//Ugly hack why php serialize and cannot unserialize a valid string with '.

	$lang_file=unserialize(str_replace("'", "\'", $ser_translation));
	
	//Upgrade translations with new addings via check_language.php...
	
	$lang_adding=eval_lang($module, $file_lang, $language);
	
	foreach(array_keys($lang_adding) as $key_file_lang)
	{
		
		foreach($lang_adding[$key_file_lang] as $key_lang => $value_lang)
		{
			
			if(!isset($lang_file[$key_file_lang][$key_lang]))
			{
				
				$lang_file[$key_file_lang][$key_lang]=$value_lang;

			}

		}
		
	}
	
	return $lang_file;

}

function clean_directory($dir_path_clean)
{

	foreach(glob($dir_path_clean . '/*') as $file) 
	{
		if(is_dir($file))
		{
			//rmdir($file);
			clean_directory($file);

		}
		else	
		{
			unlink($file);
		}
	}

	@rmdir($dir_path_clean);

}

?>