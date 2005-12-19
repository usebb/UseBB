<?php

$usebb_module_info = array(
	'short_name' => 'translate',
	'long_name' => 'Translation Helper',
	'acp_category' => 'various',
);

if ( defined('RUN_MODULE') ) {
	
	class usebb_module {
		
		function load_translation($name) {
			
			require(ROOT_PATH.'languages/lang_'.$name.'.php');
			return $lang;
			
		}
		
		function run_module() {
			
			global $functions;
			
			if ( !in_array('English', $functions->get_language_packs()) )
				return '<h2>Error</h2><p>No English language pack found. This is necessary to check translations.</p>';
			
			if ( !empty($_GET['check']) && in_array($_GET['check'], $functions->get_language_packs()) ) {
				
				$English = array_keys($this->load_translation('English'));
				$translation = array_keys($this->load_translation($_GET['check']));
				$changes = array_diff($English, $translation);
				sort($changes);
				
				$out = '<h2>Missing keys in <em>'.$_GET['check'].'</em>: '.count($changes).'</h2><ul>';
				foreach ( $changes as $key )
					$out .= '<li>'.$key.'</li>';
				$out .= '</ul>';
				
				return $out;
				
			} else {
				
				$out = '<h2>Select translation to check</h2>';
				$out .= '<form action="'.$functions->make_url('admin.php', array('act' => 'mod_translate')).'" method="get"><p><input type="hidden" name="act" value="mod_translate" /><select name="check">';
				foreach ( $functions->get_language_packs() as $translation ) {
					
					if ( $translation == 'English' )
						continue;
					
					$out .= '<option value="'.$translation.'">'.$translation.'</option>';
					
				}
				$out .= '</select> <input type="submit" value="Check" /></p></form>';
				
				return $out;
				
			}
			
		}
		
	}
	
	$usebb_module = new usebb_module;
	
}

?>