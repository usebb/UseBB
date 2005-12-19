<?php

$usebb_module_info = array(
	'short_name' => 'translate',
	'long_name' => 'Translation Helper',
	'acp_category' => 'various',
);

if ( defined('RUN_MODULE') ) {
	
	class usebb_module {
		
		function load_translation($name, $section) {
			
			if ( file_exists(ROOT_PATH.'languages/'.$section.'_'.$name.'.php') )
				require(ROOT_PATH.'languages/'.$section.'_'.$name.'.php');
			else
				$lang = array();
			return $lang;
			
		}
		
		function list_keys($name, $section) {
			
			$English = array_keys($this->load_translation('English', $section));
			$translation = array_keys($this->load_translation($name, $section));
			$changes = array_diff($English, $translation);
			sort($changes);
			
			$out = '<h2>Missing keys in <em>'.$section.'_'.$name.'.php</em>: '.count($changes).'</h2><ul>';
			foreach ( $changes as $key )
				$out .= '<li>'.$key.'</li>';
			$out .= '</ul>';
			
			return $out;
			
		}
		
		function run_module() {
			
			global $functions;
			
			if ( !in_array('English', $functions->get_language_packs()) )
				return '<h2>Error</h2><p>No English language pack found. This is necessary to check translations.</p>';
			
			if ( !empty($_GET['check']) && in_array($_GET['check'], $functions->get_language_packs()) ) {
				
				$out = '';
				$out .= $this->list_keys($_GET['check'], 'lang');
				$out .= $this->list_keys($_GET['check'], 'admin');
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