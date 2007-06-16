<?php

/*
	Copyright (C) 2003-2007 UseBB Team
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
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 * Header file
 *
 * Does some stuff at the beginning of the processing.
 *
 * @author	UseBB Team
 * @link	http://www.usebb.net
 * @license	GPL-2
 * @version	$Revision$
 * @copyright	Copyright (C) 2003-2007 UseBB Team
 * @package	UseBB
 * @subpackage Core
 */

//
// Die when called directly in browser
//
if ( !defined('INCLUDED') )
	exit();

//
// Fetch the language array
//
$lang = $functions->fetch_language();

//
// Init external window Javascript
//
if ( $functions->get_config('target_blank') )
	$template->set_js_onload('init_external()');

$link_bar = array();

//
// ACP
//
if ( $functions->get_user_level() == LEVEL_ADMIN )
	$link_bar[] = '<a href="'.$functions->make_url('admin.php').'">'.$lang['ACP'].'</a>';
	
//
// Don't show these is they cannot be accessed after all
//
if ( ( !$session->sess_info['ip_banned'] && !$functions->get_config('board_closed') && ( $functions->get_config('guests_can_access_board') || $functions->get_user_level() != LEVEL_GUEST ) ) || $functions->get_user_level() == LEVEL_ADMIN  ) {
	
	//
	// Member list
	//
	if ( $functions->get_config('enable_memberlist') && $functions->get_user_level() >= $functions->get_config('view_memberlist_min_level') )
		$link_bar[] = '<a href="'.$functions->make_url('members.php').'">'.$lang['MemberList'].'</a>';
	
	//
	// Staff list
	//
	if ( $functions->get_config('enable_stafflist') && $functions->get_user_level() >= $functions->get_config('view_stafflist_min_level') )
		$link_bar[] = '<a href="'.$functions->make_url('members.php', array('act' => 'staff')).'">'.$lang['StaffList'].'</a>';
	
	//
	// Statistics
	//
	if ( $functions->get_config('enable_stats') && $functions->get_user_level() >= $functions->get_config('view_stats_min_level') )
		$link_bar[] = '<a href="'.$functions->make_url('stats.php').'">'.$lang['Statistics'].'</a>';
	
	//
	// RSS feed
	//	
	if ( $functions->get_config('enable_rss') ) {
		
		$rss_feed_icon = $template->get_config('rss_feed_icon');
		
		if ( !empty($rss_feed_icon) )
			$link_bar[] = '<a href="'.$functions->make_url('rss.php').'" id="rss-feed-icon"><img src="templates/'.$functions->get_config('template').'/gfx/'.$rss_feed_icon.'" alt="'.$lang['RSSFeed'].'" /></a><a href="'.$functions->make_url('rss.php').'">'.$lang['RSSFeed'].'</a> ';
		else
			$link_bar[] = '<a href="'.$functions->make_url('rss.php').'">'.$lang['RSSFeed'].'</a>';
		
	}
	
}
	
//
// Contact admin
//
if ( $functions->get_config('enable_contactadmin') && $functions->get_user_level() >= $functions->get_config('view_contactadmin_min_level') )
	$link_bar[] = '<a href="mailto:'.$functions->get_config('admin_email').'">'.$lang['ContactAdmin'].'</a>';

$template->add_global_vars(array(
	
	//
	// board settings
	//
	'board_name' => unhtml($functions->get_config('board_name')),
	'board_descr' => unhtml($functions->get_config('board_descr')),
	'board_keywords' => unhtml($functions->get_config('board_keywords')),
	'board_url' => $functions->get_config('board_url'),
	'admin_email' => $functions->get_config('admin_email'),
	
	//
	// menu links
	//
	'link_home' => $functions->make_url('index.php'),
	'link_reg_panel' => ( $session->sess_info['user_id'] ) ? $functions->make_url('panel.php') : $functions->make_url('panel.php', array('act' => 'register')),
	'reg_panel' => ( $session->sess_info['user_id'] ) ? $lang['YourPanel'] : $lang['Register'],
	'link_faq' => $functions->make_url('faq.php'),
	'link_search' => $functions->make_url('search.php'),
	'link_active' => $functions->make_url('active.php'),
	'link_log_inout' => ( $session->sess_info['user_id'] ) ? $functions->make_url('panel.php', array('act' => 'logout')) : $functions->make_url('panel.php', array('act' => 'login')),
	'log_inout' => ( $session->sess_info['user_id'] ) ? sprintf($lang['LogOut'], '<em>'.unhtml(stripslashes($session->sess_info['user_info']['name'])).'</em>') : $lang['LogIn'],
	
	//
	// link bar (list of additional enabled features)
	//
	'link_bar' => ( count($link_bar) ) ? join($template->get_config('item_delimiter'), $link_bar) : '',
	
	//
	// additional links to features (might end up in error when feature is disabled)
	// use 'em when you want to have more links in the menu or somewhere else
	//
	'link_memberlist' => $functions->make_url('members.php'),
	'link_stafflist' => $functions->make_url('members.php', array('act' => 'staff')),
	'link_rss' => $functions->make_url('rss.php'),
	'link_stats' => $functions->make_url('stats.php'),
	
	'rss_head_link' => ( $functions->get_config('enable_rss') ) ? '<link rel="alternate" type="application/rss+xml" title="'.unhtml($functions->get_config('board_name')).' '.$lang['RSSFeed'].'" href="'.$functions->make_url('rss.php').'" />' : '',
	'usebb_copyright' => sprintf($lang['PoweredBy'], unhtml($functions->get_config('board_name')), '<a href="http://www.usebb.net">UseBB '.$lang['ForumSoftware'].'</a>')
	
));

//
// Page header
//
$template->parse('normal_header', 'global');

//
// Make a Forbidden header when the RSS feed cannot be requested
//
if ( $session->sess_info['location'] == 'rss' && ( $session->sess_info['ip_banned'] || $functions->get_config('board_closed') || ( !$functions->get_config('guests_can_access_board') && $functions->get_user_level() == LEVEL_GUEST ) ) ) {
	
	header($_SERVER['SERVER_PROTOCOL'].' 403 Forbidden');
	exit();
	
}

//
// Banned IP addresses catch this message
//
if ( $session->sess_info['ip_banned'] ) {
	
	header($_SERVER['SERVER_PROTOCOL'].' 403 Forbidden');
	$template->set_page_title($lang['Note']);
	$template->parse('msgbox', 'global', array(
		'box_title' => $lang['Note'],
		'content' => sprintf($lang['BannedIP'], $session->sess_info['ip_addr'])
	));
	
	//
	// Include the page footer
	//
	require(ROOT_PATH.'sources/page_foot.php');
	
	exit();
	
}

//
// Board Closed message
//
if ( $functions->get_config('board_closed') && $session->sess_info['location'] != 'login' ) {
	
	$template->set_page_title($lang['BoardClosed']);
	
	//
	// Show this annoying board closed message on all pages but the login page.
	//
	$template->parse('msgbox', 'global', array(
		'box_title' => $lang['BoardClosed'],
		'content' => $functions->get_config('board_closed_reason')
	));
	
	//
	// Admins can still enter the board
	//
	if ( $functions->get_user_level() < LEVEL_ADMIN ) {
		
		//
		// Include the page footer
		//
		require(ROOT_PATH.'sources/page_foot.php');
		
		exit();
		
	}
	
}

//
// Guests must log in
//
if ( !$functions->get_config('guests_can_access_board') && $functions->get_user_level() == LEVEL_GUEST && !in_array($session->sess_info['location'], array('login', 'register', 'activate', 'sendpwd')) ) {
	
	$functions->redir_to_login();
	
	//
	// Include the page footer
	//
	require(ROOT_PATH.'sources/page_foot.php');
	
	exit();
	
}

//
// Anti-spam question
//
if ( $session->sess_info['pose_antispam_question'] ) {
	
	$template->set_page_title($lang['AntiSpamQuestion']);
	
	$mode = (int)$functions->get_config('antispam_question_mode');
	
	if ( empty($_SESSION['antispam_question_question']) ) {
		
		//
		// The question and answer has not been generated yet
		//
		switch ( $mode ) {
			
			case ANTI_SPAM_MATH:
				//
				// Random math question
				//
				$operator = mt_rand(1, 2);
				if ( $operator == 1 ) {
					
					$num1 = mt_rand(1, 9);
					$num2 = mt_rand(1, 9);
					$_SESSION['antispam_question_question'] = sprintf($lang['AntiSpamQuestionMathPlus'], $num1, $num2);
					$_SESSION['antispam_question_answer'] = $num1 + $num2;

				} else {
					
					$num1 = mt_rand(1, 9);
					$num2 = mt_rand(1, $num1);
					$_SESSION['antispam_question_question'] = sprintf($lang['AntiSpamQuestionMathMinus'], $num1, $num2);
					$_SESSION['antispam_question_answer'] = $num1 - $num2;
					
				}
				break;
			
			case ANTI_SPAM_CUSTOM:
				//
				// Custom admin-defined question
				//
				$questionPairs = $functions->get_config('antispam_question_questions');
				if ( !is_array($questionPairs) || !count($questionPairs) )
					trigger_error('No custom anti-spam questions found.', E_USER_ERROR);
				$questions = array_keys($questionPairs);
				$answers = array_values($questionPairs);
				unset($questionPairs);
				
				if ( count($questions) == 1 )
					$questionId = 0;
				else
					$questionId = mt_rand(0, count($questions)-1);
				
				$_SESSION['antispam_question_question'] = $questions[$questionId];
				$_SESSION['antispam_question_answer'] = $answers[$questionId];
				break;
			
			default:
				trigger_error('Spam check mode '.$mode.' does not exist.', E_USER_ERROR);
			
		}
		
	}
	
	if ( isset($_POST['answer']) && !strcasecmp(strval($_POST['answer']), strval($_SESSION['antispam_question_answer'])) ) {
		
		//
		// Question passed, continuing...
		//
		$_SESSION['antispam_question_posed'] = true;
		unset($_SESSION['antispam_question_question'], $_SESSION['antispam_question_answer']);
		$functions->redirect($_SERVER['PHP_SELF'], $_GET);
		
	} else {
		
		if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
			
			$template->parse('msgbox', 'global', array(
				'box_title' => $lang['Error'],
				'content' => $lang['AntiSpamWrongAnswer']
			));
			
		}
		
		$size = ( $mode === ANTI_SPAM_MATH ) ? 'size="2" maxlength="2"' : 'size="35"';
		$template->parse('anti_spam_question', 'various', array(
			'form_begin' => '<form action="'.$functions->make_url($_SERVER['PHP_SELF'], $_GET).'" method="post">',
			'question' => unhtml($_SESSION['antispam_question_question']),
			'answer_input' => '<input type="text" name="answer" id="answer" '.$size.' />',
			'submit_button' => '<input type="submit" name="submit" value="'.$lang['Send'].'" />',
			'reset_button' => '<input type="reset" value="'.$lang['Reset'].'" />',
			'form_end' => '</form>'
		));
		$template->set_js_onload("set_focus('answer')");
		
		//
		// Include the page footer
		//
		require(ROOT_PATH.'sources/page_foot.php');
		
		exit();
		
	}
	
}

?>
