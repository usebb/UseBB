<?php

$usebb_module_info = array( # required
	'short_name' => 'test', # required
	'long_name' => 'Module test', # required
	'acp_category' => 'test', # required
	'new_acp_category_long_name' => 'Module test', # required if the above category does not exist yet
);

if ( defined('RUN_MODULE') ) { # required
	
	class usebb_module { # required
		
		function lets_run_fortune() { # just define anything you want
			
			return ( file_exists('/usr/bin/fortune') ) ? '<p>This is a random quote from <code>fortune</code>:</p><p><em>'.nl2br(unhtml(shell_exec('/usr/bin/fortune'))).'</em></p>' : '<p>Oops, no <code>/usr/bin/fortune</code> found. :(</p>';
			
		}
		
		function run_module() { # required
			
			global $functions, $admin_functions;
			
			$content = $this->lets_run_fortune();
			
			$content .= '<h2>Friendly URL\'s</h2>';
			$content .= ( !$functions->get_config('friendly_urls') ) ? '<p>Hey, why don\'t you use our friendly URL\'s?</p>' : '<p>Thanks for using our neat friendly URL\'s feature!</p>';
			
			$content .= '<h2>Debug</h2>';
			if ( !empty($_GET['do']) && $_GET['do'] == 'nodebug' )
				$admin_functions->set_config(array('debug' => 0));
			$content .= '<p><a href="'.$functions->make_url('admin.php', array('act' => 'mod_test', 'do' => 'nodebug')).'">Clicking this link will disable debug info.</a></p>';
			
			return $content;
			
		}
		
	}
	
	$usebb_module = &new usebb_module; # required
	
}

?>