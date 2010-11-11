<?php

/*
	Copyright (C) 2003-2010 UseBB Team
	http://www.usebb.net
	
	$Id$
	
	This file is part of UseBB.
	
	UseBB is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.
	
	UseBB is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with UseBB; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

define('INCLUDED', true);
define('ROOT_PATH', '../');

if ( empty($_GET['step']) || intval($_GET['step']) < 2 )
	define('NO_DB', true);

define('IS_INSTALLER', true);

//
// Include usebb engine
//
require(ROOT_PATH.'sources/common.php');

$lang = $functions->fetch_language('English');

require(ROOT_PATH.'sources/functions_admin.php');

$admin_functions = new admin_functions;

$_GET['step'] = ( !empty($_GET['step']) && valid_int($_GET['step']) ) ? intval($_GET['step']) : 1;

$out = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>UseBB Installation</title>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1"/>
<link rel="stylesheet" type="text/css" href="../docs/styles.css" />
</head>
<body>

<div id="wrapper">
	<h1>UseBB Installation</h1>
	<div id="content">
		<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
';

if ( empty($_SESSION['installer_running']) && $functions->get_config('installer_run') ) {
	
	$out .= '		<p>This installer has been run already. To enable it again, delete the <code>installer_run</code> config value from <code>config.php</code>.</p>
';
	
} elseif ( $_GET['step'] === 1 ) {
	
	foreach ( array('db_type', 'db_server', 'db_username', 'db_passwd', 'db_dbname', 'db_prefix', 'admin_username', 'admin_email', 'admin_passwd1', 'admin_passwd2') as $key )
		$_POST[$key] = ( !empty($_POST[$key]) ) ? $_POST[$key] : '';
	
	$db_servers = ( version_compare(phpversion(), '5.0.0', '<') || !extension_loaded('mysqli') ) ? array('mysql' => 'MySQL') : array('mysqli' => 'MySQL 4.1/5.x &mdash; mysqli', 'mysql' => 'MySQL 3.x/4.0 &mdash; mysql');
	
	if ( !empty($_POST['start']) && !is_writable(ROOT_PATH.'config.php') && !empty($_SESSION['installer_running']) ) {
		
		$functions->redirect('index.php', array('step' => 2));
		
	} elseif ( !empty($_POST['db_type']) && array_key_exists($_POST['db_type'], $db_servers) && !empty($_POST['db_server']) && !empty($_POST['db_username']) && !empty($_POST['db_dbname']) && !empty($_POST['admin_username']) && preg_match(USER_PREG, $_POST['admin_username']) && !empty($_POST['admin_email']) && preg_match(EMAIL_PREG, $_POST['admin_email']) && !empty($_POST['admin_passwd1']) && !empty($_POST['admin_passwd2']) && preg_match(PWD_PREG, $_POST['admin_passwd1']) && $_POST['admin_passwd1'] == $_POST['admin_passwd2'] ) {
		
		$_SESSION['installer_running'] = 1;
		
		$_SESSION['admin_username'] = $_POST['admin_username'];
		$_SESSION['admin_email'] = $_POST['admin_email'];
		$_SESSION['admin_passwd'] = md5($_POST['admin_passwd1']);
		
		$admin_functions->set_config(array(
			'type' => $_POST['db_type'],
			'server' => $_POST['db_server'],
			'username' => $_POST['db_username'],
			'passwd' => $_POST['db_passwd'],
			'dbname' => $_POST['db_dbname'],
			'prefix' => $_POST['db_prefix'],
			'admin_email' => $_POST['admin_email'],
			'installer_run' => 1
		));
		
		if ( is_writable(ROOT_PATH.'config.php') )
			$functions->redirect('index.php', array('step' => 2));
		
	} else {
		
		if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
			
			$out .= '		<p class="important"><strong>Important:</strong> some values were missing or filled in incorrectly. Please check them.</p>
		
		<p>Please fill in all the required fields below (marked with <small>*</small>). If you don\'t know what a field means or you don\'t know what to fill in, please ask your web hosting company for the right values.</p>
';
			
		} else {
			
			$config_warning = ( !is_writable(ROOT_PATH.'config.php') ) ? '<p class="important"><strong>Tip:</strong> <code>config.php</code> is at this moment not writable by the webserver. Therefore, you will be asked to download the file after filling in this form. If you would like UseBB to edit the file automatically, make <code>config.php</code> writable (<em>chmod</em> it to 0777) and <a href="index.php">refresh</a> this wizard.</p>' : '';
			
			$out .= '		<p>Hello and welcome to the UseBB installation script. First, thanks for choosing UseBB for your forum needs!</p>
		
		<p>This wizard will install a basic UseBB forum at your website. Therefore, we need some information from you. Please fill in all the required fields below (marked with <small>*</small>). If you don\'t know what a field means or you don\'t know what to fill in, please ask your web hosting company for the right values.</p>
		
		<p class="important"><strong>Important:</strong> this wizard does <strong>not</strong> upgrade an existing installation. Please see the <a href="../docs/index.html"><em>Readme</em> document</a> for upgrading instructions.</p>
		
		<p class="important"><strong>Important:</strong> If you use <strong>MySQL 4.1 or higher</strong>, it is highly recommended to use a <strong><code>latin1</code> collation</strong> instead of a Unicode one. Should your host use Unicode as default database collation, please change this for your database using phpMyAdmin or a similar administration tool or contact your host <em>before</em> installing UseBB. In most cases though, no action will be necessary.</p>
		
		'.$config_warning.'
		
		<p>You can also manually install UseBB. The instructions can be found in the <a href="../docs/index.html"><em>Readme</em> document</a>. Also, check the system requirements found in that file.</p>
';
			
		}
		
		$_POST['db_server'] = ( $_SERVER['REQUEST_METHOD'] == 'GET' ) ? 'localhost' : $_POST['db_server'];
		$_POST['db_prefix'] = ( $_SERVER['REQUEST_METHOD'] == 'GET' ) ? 'usebb_' : $_POST['db_prefix'];
		
		if ( count($db_servers) > 1 ) {
			
			$db_server = '<select name="db_type">';
			foreach ( $db_servers as $key => $val ) {
				
				$selected = ( $_POST['db_type'] == $key ) ? ' selected="selected"' : '';
				$db_server .= '<option value="'.$key.'"'.$selected.'>'.$val.'</option>';
				
			}
			$db_server .= '</select>';
			
		} else {
			
			$db_server = current($db_servers).' <input type="hidden" name="db_type" value="'.key($db_servers).'" />';
			
		}
		
		if ( is_writable(ROOT_PATH.'config.php') ) {
			
			$submit = '<p>Start the installation when you are sure everything is filled in correctly.</p>
		<p id="submit"><input type="submit" value="Start installation" /></p>';
			
		} else {
			
			$submit = '<p>When you are sure everything is filled in correctly, click the button <em>Download config.php</em> to save the configuration file and upload it to your web space. When this is done, click <em>Start Installation</em>.</p>
		<p id="submit"><input type="submit" value="Download config.php" /> <input type="submit" name="start" value="Start installation" /></p>';
			
		}
		
		$out .= '		
		<table>
			<tr>
				<th colspan="2">Database configuration</th>
			</tr>
			<tr>
				<td class="title">Server type <small>*</small></td>
				<td>'.$db_server.'</td>
			</tr>
			<tr>
				<td class="title">Server host <small>*</small></td>
				<td><input type="text" size="35" name="db_server" value="'.unhtml($_POST['db_server']).'" /></td>
			</tr>
			<tr>
				<td class="title">Username <small>*</small></td>
				<td><input type="text" size="35" name="db_username" value="'.unhtml($_POST['db_username']).'" /></td>
			</tr>
			<tr>
				<td class="title">Password</td>
				<td><input type="password" size="35" name="db_passwd" /></td>
			</tr>
			<tr>
				<td class="title">DB name <small>*</small></td>
				<td><input type="text" size="35" name="db_dbname" value="'.unhtml($_POST['db_dbname']).'" /></td>
			</tr>
			<tr>
				<td class="title">Table prefix</td>
				<td><input type="text" size="35" name="db_prefix" value="'.unhtml($_POST['db_prefix']).'" /></td>
			</tr>
		</table>
		
		<p>This will also create an admin account for your forum. Fill in the fields below. Note a username can only contain alphanumeric characters, spaces, _ and -. The password can only contain alphanumeric characters.</p>
		
		<table>
			<tr>
				<th colspan="2">Administrator account</th>
			</tr>
			<tr>
				<td class="title">Username <small>*</small></td>
				<td><input type="text" size="35" name="admin_username" value="'.unhtml($_POST['admin_username']).'" /></td>
			</tr>
			<tr>
				<td class="title">E-mail <small>*</small></td>
				<td><input type="text" size="35" name="admin_email" value="'.unhtml($_POST['admin_email']).'" /></td>
			</tr>
			<tr>
				<td class="title">Password <small>*</small></td>
				<td><input type="password" size="35" name="admin_passwd1" /></td>
			</tr>
			<tr>
				<td class="title">Repeat password <small>*</small></td>
				<td><input type="password" size="35" name="admin_passwd2" /></td>
			</tr>
		</table>
		
		'.$submit.'
		<p>If you encounter a <em>General Error</em>, the configuration values may be wrong. Check them and restart the installation.</p>
';
		
	}
	
} elseif ( $_GET['step'] === 2 && !empty($_SESSION['admin_username']) && preg_match(USER_PREG, $_SESSION['admin_username']) && !empty($_SESSION['admin_email']) && preg_match(EMAIL_PREG, $_SESSION['admin_email']) && !empty($_SESSION['admin_passwd']) ) {
	
	$lines_schema = file('./schemas/mysql.sql');
	$lines_data = file('./usebb.sql');
	$lines = array_merge($lines_schema, $lines_data);
	$queries = array();
	$i = 0;
	
	foreach ($lines as $sql) {
		
		$sql = trim(stripslashes($sql));
		if ( !empty($sql) && !preg_match('#^[-\#]#', $sql) ) {
			
			if ( !array_key_exists($i, $queries) )
				$queries[$i] = '';
			
			$queries[$i] .= $sql.' ';
			
			if ( preg_match('#;$#', $sql) ) {
				
				$query = trim(str_replace('usebb_', TABLE_PREFIX, preg_replace("#\s#", ' ', $queries[$i])));
				$queries[$i] = substr($query, 0, strlen($query)-1);
				$i++;
				
			}
			
		}
		
	}
	
	$queries[] = "INSERT INTO ".TABLE_PREFIX."members ( id, name, displayed_name, email, passwd, regdate, level, active, template, language, date_format, enable_quickreply, return_to_topic_after_posting, target_blank, hide_avatars, hide_userinfo, hide_signatures, banned_reason, signature ) VALUES ( NULL, '".$_SESSION['admin_username']."', '".$_SESSION['admin_username']."', '".$_SESSION['admin_email']."', '".$_SESSION['admin_passwd']."', ".time().", 3, 1, '".$functions->get_config('template')."', '".$functions->get_config('language')."', '".$functions->get_config('date_format')."', ".$functions->get_config('enable_quickreply').", ".$functions->get_config('return_to_topic_after_posting').", ".$functions->get_config('target_blank').", ".$functions->get_config('hide_avatars').", ".$functions->get_config('hide_userinfo').", ".$functions->get_config('hide_signatures').", '', '' )";
	$queries[] = "UPDATE ".TABLE_PREFIX."stats SET content = content+1 WHERE name = 'members'";
	
	foreach ( $queries as $query )
		$db->query($query);
	
	unset($_SESSION['installer_running'], $_SESSION['admin_username'], $_SESSION['admin_email'], $_SESSION['admin_passwd']);
	
	$out .= '		<p>The installation is complete. You must now <strong>remove the <code>install</code> directory</strong> from your forum\'s files. After this, you can log in into <a href="../">your UseBB forum</a>.</p>
		<p>If you need any help, feel free to visit the <a href="http://www.usebb.net/community/">community forums</a> at UseBB.net. Thanks for choosing UseBB!</p>
';
	
} else {
	
	$functions->redirect('index.php');
	
}

$out .= '		</form>
	</div>
</div>

<p id="copyright">&copy; <a href="http://www.usebb.net">UseBB Project</a></p>

</body>
</html>';

$template->add_raw_content($out);
$template->body();

?>
