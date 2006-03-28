<?php

/*
	Copyright (C) 2003-2006 UseBB Team
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

/**
 * Member profile
 *
 * Shows the profile of a forum member.
 *
 * @author	UseBB Team
 * @link	http://www.usebb.net
 * @license	GPL-2
 * @version	$Revision$
 * @copyright	Copyright (C) 2003-2006 UseBB Team
 * @package	UseBB
 */

define('INCLUDED', true);
define('ROOT_PATH', './');

//
// Include usebb engine
//
require(ROOT_PATH.'sources/common.php');

//
// If an ID has been passed
//
if ( !empty($_GET['id']) && valid_int($_GET['id']) ) {
	
	//
	// Update and get the session information
	//
	$session->update('profile:'.$_GET['id']);
	
	//
	// Include the page header
	//
	require(ROOT_PATH.'sources/page_head.php');
	
	if ( !$functions->get_config('guests_can_view_profiles') && $session->sess_info['user_id'] == LEVEL_GUEST ) {
		
		$functions->redir_to_login();
		
	} else {
		
		//
		// Get the user information
		//
		if ( $_GET['id'] == $session->sess_info['user_id'] ) {
			
			//
			// This user is viewing his own profile, so we don't need a new query
			//
			$own_profile = true;
			
		} else {
			
			//
			// This user is not viewing his own profile, so we need a new query
			//
			$own_profile = false;
			
			$result = $db->query("SELECT * FROM ".TABLE_PREFIX."members WHERE id = ".$_GET['id']);
			$profiledata = $db->fetch_result($result);
			
		}
		
		if ( $own_profile || $profiledata['id'] ) {
			
			//
			// The user exists, show its profile
			//
			
			if ( $own_profile ) {
				
				$profiledata = $session->sess_info['user_info'];
				$template->set_page_title('<a href="'.$functions->make_url('panel.php').'">'.$lang['YourPanel'].'</a>'.$template->get_config('locationbar_item_delimiter').$lang['ViewProfile']);
				
				//
				// View the panel menu if the user is viewing his own profile
				//
				$template->parse('menu', 'panel', array(
					'panel_home' => '<a href="'.$functions->make_url('panel.php').'">'.$lang['PanelHome'].'</a>',
					'panel_subscriptions' => '<a href="'.$functions->make_url('panel.php', array('act' => 'subscriptions')).'">'.$lang['Subscriptions'].'</a>',
					'view_profile' => '<a href="'.$functions->make_url('profile.php', array('id' => $session->sess_info['user_info']['id'])).'"><strong>'.$lang['ViewProfile'].'</strong></a>',
					'panel_profile' => '<a href="'.$functions->make_url('panel.php', array('act' => 'editprofile')).'">'.$lang['EditProfile'].'</a>',
					'panel_options' => '<a href="'.$functions->make_url('panel.php', array('act' => 'editoptions')).'">'.$lang['EditOptions'].'</a>',
					'panel_passwd' => '<a href="'.$functions->make_url('panel.php', array('act' => 'editpwd')).'">'.$lang['EditPasswd'].'</a>',
				));
				
			} else {
				
				$template->set_page_title(sprintf($lang['Profile'], unhtml(stripslashes($profiledata['displayed_name']))));
				
			}
			
			$username = unhtml(stripslashes($profiledata['displayed_name']));
			if ( $functions->get_user_level() == LEVEL_ADMIN || $own_profile )
				$username .= ' (<em>'.unhtml(stripslashes($profiledata['name'])).'</em>)';
			if ( $functions->get_user_level() == LEVEL_ADMIN )
				$username .= $template->get_config('item_delimiter').'<a href="'.$functions->make_url('admin.php', array('act' => 'members', 'id' => $_GET['id'])).'">'.$lang['EditThisMember'].'</a>';
			
			switch ( $profiledata['level'] ) {
				
				case 3:
					$level = $lang['Administrator'];
					break;
				case 2:
					$level = $lang['Moderator'];
					break;
				case 1:
					$level = $lang['Member'];
					break;
				
			}
			
			if ( $profiledata['last_login_show'] || $own_profile || $functions->get_user_level() == LEVEL_ADMIN )
				$last_login = ( $profiledata['last_login'] != 0 ) ? $functions->make_date($profiledata['last_login']) : $lang['Never'];
			else
				$last_login = $lang['Hidden'];
			
			if ( !$profiledata['avatar_type'] ) {
				
				$avatar = '';
				
			} elseif ( intval($profiledata['avatar_type']) === 1 ) {
				
				$avatar_force_width = ( $functions->get_config('avatars_force_width') ) ? ' width="'.intval($functions->get_config('avatars_force_width')).'"' : '';
				$avatar_force_height = ( $functions->get_config('avatars_force_height') ) ? ' height="'.intval($functions->get_config('avatars_force_height')).'"' : '';
				$avatar = '<img src="'.unhtml($profiledata['avatar_remote']).'" alt=""'.$avatar_force_width.$avatar_force_height.' />';
				
			}
			
			$days_since_registration = ( ( time() - $profiledata['regdate'] ) / 86400 );
			if ( $days_since_registration <= 1 )
				$posts_per_day = $profiledata['posts'];
			else
				$posts_per_day = round($profiledata['posts'] / $days_since_registration, 2);
			
			$birthday = $profiledata['birthday'];
			if ( $birthday )
				$age = $functions->calculate_age($birthday);
			else
				$age = '';
			
			$target_blank = ( $functions->get_config('target_blank') ) ? ' target="_blank"' : '';
			
			if ( $functions->get_user_level() == LEVEL_GUEST && !$functions->get_config('guests_can_see_contact_info') ) {
				
				//
				// Hide contact info for guests
				//
				$email_v = $lang['Hidden']; 
				$msnm_v = $yahoom_v = $aim_v = $icq_v = $icq_status = $jabber_v = $skype_v = '';
				
			} else {
				
				$email_v = $functions->show_email($profiledata);
				$msnm_v = ( preg_match(EMAIL_PREG, $profiledata['msnm']) ) ? '<a href="http://members.msn.com/'.$profiledata['msnm'].'"'.$target_blank.'>'.$profiledata['msnm'].'</a>' : unhtml(stripslashes($profiledata['msnm']));
				$yahoom_v = ( !empty($profiledata['yahoom']) ) ? '<a href="http://edit.yahoo.com/config/send_webmesg?.target='.unhtml(stripslashes($profiledata['yahoom'])).'"'.$target_blank.'>'.unhtml(stripslashes($profiledata['yahoom'])).'</a>' : '';
				$aim_v = ( !empty($profiledata['aim']) ) ? '<a href="aim:goim?screenname='.unhtml(stripslashes($profiledata['aim'])).'&amp;message=Hi.+Are+you+there?">'.unhtml(stripslashes($profiledata['aim'])).'</a>' : '';
				$icq_v = ( valid_int($profiledata['icq']) ) ? '<a href="http://www.icq.com/whitepages/about_me.php?Uin='.intval($profiledata['icq']).'"'.$target_blank.'>'.intval($profiledata['icq']).'</a>' : unhtml(stripslashes($profiledata['icq']));
				$icq_status = ( valid_int($profiledata['icq']) ) ? '<img src="http://web.icq.com/whitepages/online?icq='.intval($profiledata['icq']).'&amp;img=25" alt="'.intval($profiledata['icq']).'" />' : '';
				$jabber_v = ( preg_match(EMAIL_PREG, $profiledata['jabber']) ) ? '<a href="jabber:'.$profiledata['jabber'].'"'.$target_blank.'>'.$profiledata['jabber'].'</a>' : unhtml(stripslashes($profiledata['jabber']));
				$skype_v = unhtml(stripslashes($profiledata['skype']));
				
			}
			
			$template->parse('profile', 'various', array(
				'title'         => sprintf($lang['Profile'], unhtml(stripslashes($profiledata['displayed_name']))),
				'username_v'    => $username,
				'userid_v'      => $_GET['id'],
				'real_name_v'   => unhtml(stripslashes($profiledata['real_name'])),
				'level_v'       => $level,
				'rank_v'        => stripslashes($profiledata['rank']),
				'avatar_v'      => $avatar,
				'regdate_v'     => $functions->make_date($profiledata['regdate']),
				'posts_v'       => $profiledata['posts'],
				'postsperday_v' => $posts_per_day,
				'searchposts'   => '<a href="'.$functions->make_url('search.php', array('author' => stripslashes($profiledata['displayed_name'])), true, true, true).'" rel="nofollow">'.$lang['SearchMembersPosts'].'</a>',
				'lastlogin_v'   => $last_login,
				'age_v'         => $age,
				'location_v'    => unhtml(stripslashes($profiledata['location'])),
				'website_v'     => ( !empty($profiledata['website']) ) ? '<a href="'.unhtml(stripslashes($profiledata['website'])).'"'.$target_blank.'>'.unhtml(stripslashes($profiledata['website'])).'</a>' : '',
				'occupation_v'  => unhtml(stripslashes($profiledata['occupation'])),
				'interests_v'   => unhtml(stripslashes($profiledata['interests'])),
				'signature_v'   => $functions->markup($functions->replace_badwords(stripslashes($profiledata['signature'])), $functions->get_config('sig_allow_bbcode'), $functions->get_config('sig_allow_smilies')),
				'email_v'       => $email_v,
				'msnm_v'        => $msnm_v,
				'yahoom_v'      => $yahoom_v,
				'aim_v'         => $aim_v,
				'icq_v'         => $icq_v,
				'icq_status'    => $icq_status,
				'jabber_v'      => $jabber_v,
				'skype_v'       => $skype_v
			));
			
		} else {
			
			//
			// This user does not exist, show an error
			//
			header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
			$template->set_page_title($lang['Error']);
			$template->parse('msgbox', 'global', array(
				'box_title' => $lang['Error'],
				'content' => sprintf($lang['NoSuchMember'], 'ID '.$_GET['id'])
			));
			
		}
		
	}
	
	//
	// Include the page footer
	//
	require(ROOT_PATH.'sources/page_foot.php');
	
} else {
	
	//
	// There's no user ID! Get us back to the index...
	//
	$functions->redirect('index.php');
	
}

?>
