<?php

/*
	Copyright (C) 2003-2005 UseBB Team
	http://www.usebb.net
	
	$Header$
	
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
	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

define('INCLUDED', true);
define('ROOT_PATH', '../');



if ( !empty($_GET['step']) && intval($_GET['step']) < 2 )
	define('NO_DB', true);

define('IS_INSTALLER', true);

//
// Include usebb engine
//
require(ROOT_PATH.'sources/common.php');

$lang = $functions->fetch_language();

require(ROOT_PATH.'sources/functions_admin.php');

$admin_functions = new admin_functions;

$_GET['step'] = ( !empty($_GET['step']) && valid_int($_GET['step']) ) ? intval($_GET['step']) : 1;

$out = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>UseBB Installation</title>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1"/>
<style type="text/css">
* {
	padding: 0;
	margin: 0;
}
body {
	font-family: verdana, sans-serif;
	font-size: 10pt;
	background-color: #FFF;
	background-image: url(./bg_reverse.png);
	background-repeat: repeat-x;
}
a:link, a:active, a:visited {
	color: #336699;
	text-decoration: underline;
}
a:hover {
	color: #7F0000 !important;
	text-decoration: none;
}
select option {
	padding-right: 3px;
}
#wrapper {
	width: 650px;
	border: 1px solid silver;
	margin: 25px 0px 25px 0px;
	margin-left: auto;
	margin-right: auto;
	background-color: #EFEFEF;
	background-image: url(./bg.png);
	background-repeat: repeat-x;
}
#wrapper h1 {
	height: 90px;
	line-height: 90px;
	background-image: url(./logo.png);
	background-repeat: no-repeat;
	background-position: top right;
	padding: 0px 25px 0px 25px;
	font-weight: normal;
	font-size: 24pt;
	letter-spacing: -2px;
	word-spacing: 5px;
	color: #336699;
}
#wrapper #content {
	padding: 0px 25px 10px 25px;
}
#wrapper #content p, #wrapper #content table {
	margin: 0px 0px 15px 0px;
}
#wrapper #content p.important {
	background-color: #EFDFBF;
	padding: 10px;
	font-size: 8pt;
}
#wrapper #content p#submit, #wrapper #content p#submit input {
	text-align: center;
	font-weight: bold;
}
#wrapper #content p#submit input {
	padding: 5px;
}
#wrapper #content table {
	border-collapse: collapse;
	margin-left: auto;
	margin-right: auto;
}
#wrapper #content table th, #wrapper #content table td {
	padding: 5px;
	border-bottom: 1px solid silver;
}
#wrapper #content table th {
	text-align: left;
	color: #336699;
}
#wrapper #content table td.title {
	width: 135px;
}
#copyright {
	margin: 0px 0px 25px 0px;
	text-align: center;
	font-size: 8pt;
	color: #666;
}
</style>
</head>
<body>

<div id="wrapper">
	<h1>UseBB Installation</h1>
	<div id="content">
		<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
';

if ( $_GET['step'] === 1 ) {
	
	foreach ( array('db_type', 'db_server', 'db_username', 'db_passwd', 'db_dbname', 'db_prefix', 'admin_username', 'admin_email', 'admin_passwd1', 'admin_passwd2') as $key )
		$_POST[$key] = ( !empty($_POST[$key]) ) ? $_POST[$key] : '';
	
	if ( !empty($_POST['db_type']) && in_array($_POST['db_type'], array('mysql', 'mysqli')) && !empty($_POST['db_server']) && !empty($_POST['db_username']) && !empty($_POST['db_dbname']) && !empty($_POST['admin_username']) && preg_match(USER_PREG, $_POST['admin_username']) && !empty($_POST['admin_email']) && preg_match(EMAIL_PREG, $_POST['admin_email']) && !empty($_POST['admin_passwd1']) && !empty($_POST['admin_passwd2']) && preg_match(PWD_PREG, $_POST['admin_passwd1']) && $_POST['admin_passwd1'] == $_POST['admin_passwd2'] ) {
		
		$admin_functions->set_config(array(
			'type' => $_POST['db_type'],
			'server' => $_POST['db_server'],
			'username' => $_POST['db_username'],
			'passwd' => $_POST['db_passwd'],
			'dbname' => $_POST['db_dbname'],
			'prefix' => $_POST['db_prefix']
		));
		
		$_SESSION['admin_username'] = $_POST['admin_username'];
		$_SESSION['admin_email'] = $_POST['admin_email'];
		$_SESSION['admin_passwd'] = md5($_POST['admin_passwd1']);
		
		$functions->redirect('index.php', array('step' => 2));
		
	} else {
		
		if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
			
			$out .= '		<p class="important"><strong>Important:</strong> some values were missing or filled in incorrectly. Please check them.</p>
		
		<p>Please fill in all the required fields below (marked with <small>*</small>). If you don\'t know what a field means or you don\'t know what to fill in, please ask your web hosting company for the right values.</p>
';
			
		} else {
			
			$out .= '		<p>Hello and welcome to the UseBB installation script. First, thanks for choosing UseBB for your forum needs!</p>
		
		<p>This wizard will install a basic UseBB forum at your website. Therefore, we need some information from you. Please fill in all the required fields below (marked with <small>*</small>). If you don\'t know what a field means or you don\'t know what to fill in, please ask your web hosting company for the right values.</p>
		
		<p class="important"><strong>Important:</strong> this wizard does <strong>not</strong> upgrade an existing installation. Please see the <a href="../docs/UPGRADE"><code>UPGRADE</code></a> document for upgrading instructions.</p>
		
		<p>You can also manually install UseBB. The instructions can be found in <a href="../docs/INSTALL"><code>INSTALL</code></a>. Also, check the system requirements found in that file.</p>
';
			
		}
		
		$_POST['db_server'] = ( $_SERVER['REQUEST_METHOD'] == 'GET' ) ? 'localhost' : $_POST['db_server'];
		$_POST['db_prefix'] = ( $_SERVER['REQUEST_METHOD'] == 'GET' ) ? 'usebb_' : $_POST['db_prefix'];
		
		$db_server_text = array('mysql' => 'MySQL (3.x, 4.0)', 'mysqli' => 'MySQLi (4.1, 5.0)');
		$db_server = '<select name="db_type">';
		foreach ( array('mysql', 'mysqli') as $type ) {
			
			$selected = ( $_POST['db_type'] == $type ) ? ' selected="selected"' : '';
			$db_server .= '<option value="'.$type.'"'.$selected.'>'.$db_server_text[$type].'</option>';
			
		}
		$db_server .= '</select>';
		
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
				<td><input type="text" size="35" name="db_username" /></td>
			</tr>
			<tr>
				<td class="title">Password</td>
				<td><input type="password" size="35" name="db_passwd" value="'.unhtml($_POST['db_passwd']).'" /></td>
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
		
		<p>This will also create an admin account for your forum. Fill in the fields below. Note a username can only contain alphanumeric characters, _ and -. The password can only contain alphanumeric characters.</p>
		
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
		
		<p>Start the installation when you are sure everything is filled in correctly. If you encounter a <em>General Error</em>, the configuration values may be wrong. Check them and restart the installation.</p>
		<p id="submit"><input type="submit" value="Start installation" /></p>
';
		
	}
	
} elseif ( $_GET['step'] === 2 && !empty($_SESSION['admin_username']) && preg_match(USER_PREG, $_SESSION['admin_username']) && !empty($_SESSION['admin_email']) && preg_match(EMAIL_PREG, $_SESSION['admin_email']) && !empty($_SESSION['admin_passwd']) ) {
	
	if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
		
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
		
		$queries[] = "INSERT INTO ".TABLE_PREFIX."members ( id, name, displayed_name, email, passwd, regdate, level, active, template, language, date_format ) VALUES ( NULL, '".$_SESSION['admin_username']."', '".$_SESSION['admin_username']."', '".$_SESSION['admin_email']."', '".$_SESSION['admin_passwd']."', ".time().", 3, 1, '".$functions->get_config('template')."', '".$functions->get_config('language')."', '".$functions->get_config('date_format')."' )";
		$queries[] = "UPDATE ".TABLE_PREFIX."stats SET content = content+1 WHERE name = 'members'";
		
		foreach ( $queries as $query )
			$db->query($query);
		
		$out .= '		<p>The installation is now complete. You can now log in into <a href="../">your UseBB forum</a>. If you need any help, feel free to visit the <a href="http://www.usebb.net/support/">support pages</a> at UseBB.net.</p>
		<p>Thanks for choosing UseBB!</p>
';
		
	} else {
		
		$out .= '		<p>The configuration values have been written to <code>config.php</code>. Click below to continue the installation.</p>
		<p id="submit"><input type="submit" value="Continue installation" /></p>
';
		
	}
	
} else {
	
	$functions->redirect('index.php');
	
}

$out .= '		</form>
	</div>
</div>

<p id="copyright">Powered by UseBB '.USEBB_VERSION.' &middot; Copyright &copy; 2003-2005 <a href="http://www.usebb.net">UseBB Team</a></p>

</body>
</html>';

$template->add_raw_content($out);
$template->body();

?>
