<?php

/*
	Copyright (C) 2003-2012 UseBB Team
	http://www.usebb.net
	
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
 * Statistics resync module
 *
 * @author	UseBB Team
 * @link	http://www.usebb.net
 * @license	GPL-2
 * @copyright	Copyright (C) 2003-2012 UseBB Team
 * @package	UseBB
 */

$usebb_module_info = array(
	'short_name' => 'resync_stats',
	'long_name' => 'Resync Statistics',
	'acp_category' => 'various',
);

if ( defined('RUN_MODULE') ) {
	
	class usebb_module {

		function stage_topic_counts() {

			global $db;
			
			$result = $db->query(
				"SELECT t.id, count_replies, COUNT(p.id) AS num_posts "
				."FROM ".TABLE_PREFIX."topics t, ".TABLE_PREFIX."posts p "
				."WHERE p.topic_id = t.id "
				."GROUP BY t.id");
			$i = 0;
			while ( $topic = $db->fetch_result($result) ) {

				if ( $topic['count_replies'] == $topic['num_posts'] - 1 )
					continue;
				
				$db->query(
					"UPDATE ".TABLE_PREFIX."topics "
					."SET count_replies = ".( $topic['num_posts'] - 1 )." "
					."WHERE id = ".$topic['id']);
				$i++;

			}
			
			$_SESSION['resync_stats']['topic_counts'] = true;
			
			return '<p>'.$i.' topic(s) have been adjusted.</p>';

		}

		function stage_topic_posts() {

			global $db;
			
			$result = $db->query(
				"SELECT t.id, first_post_id, last_post_id, MIN(p.id) AS new_first_post_id, MAX(p.id) AS new_last_post_id "
				."FROM ".TABLE_PREFIX."topics t, ".TABLE_PREFIX."posts p "
				."WHERE p.topic_id = t.id "
				."GROUP BY t.id");
			$i = 0;
			while ( $topic = $db->fetch_result($result) ) {

				if ( $topic['first_post_id'] == $topic['new_first_post_id'] 
					&& $topic['last_post_id'] == $topic['new_last_post_id'] )
					continue;
				
				$db->query(
					"UPDATE ".TABLE_PREFIX."topics "
					."SET first_post_id = ".$topic['new_first_post_id'].", last_post_id = ".$topic['new_last_post_id']." "
					."WHERE id = ".$topic['id']);
				$i++;

			}
			
			$_SESSION['resync_stats']['topic_posts'] = true;
			
			return '<p>'.$i.' topic(s) have been adjusted.</p>';

		}

		function stage_forum_counts() {

			global $db;
			
			$result = $db->query(
				"SELECT f.id, topics, posts, COUNT(t.id) AS num_topics, SUM(t.count_replies) AS num_replies "
				."FROM ".TABLE_PREFIX."forums f, ".TABLE_PREFIX."topics t "
				."WHERE t.forum_id = f.id "
				."GROUP BY f.id");
			$i = 0;
			while ( $forum = $db->fetch_result($result) ) {

				if ( $forum['topics'] == $forum['num_topics'] 
					&& $forum['posts'] == ( $forum['num_replies'] + $forum['num_topics'] ) )
					continue;
				
				$db->query(
					"UPDATE ".TABLE_PREFIX."forums "
					."SET topics = ".$forum['num_topics'].", posts = ".( $forum['num_replies'] + $forum['num_topics'] )." "
					."WHERE id = ".$forum['id']);
				$i++;

			}
			
			$_SESSION['resync_stats']['forum_counts'] = true;
			
			return '<p>'.$i.' forum(s) have been adjusted.</p>';

		}

		function stage_forum_topics() {

			global $db;
			
			$result = $db->query(
				"SELECT f.id, f.last_topic_id, MAX(t.last_post_id) AS new_last_post_id "
				."FROM ".TABLE_PREFIX."forums f, ".TABLE_PREFIX."topics t "
				."WHERE t.forum_id = f.id "
				."GROUP BY f.id");
			$i = 0;
			while ( $forum = $db->fetch_result($result) ) {

				$result2 = $db->query(
					"SELECT topic_id AS new_last_topic_id "
					."FROM ".TABLE_PREFIX."posts "
					."WHERE id = ".$forum['new_last_post_id']);
				$topic = $db->fetch_result($result2);

				if ( $forum['last_topic_id'] == $topic['new_last_topic_id'] )
					continue;
				
				$db->query(
					"UPDATE ".TABLE_PREFIX."forums "
					."SET last_topic_id = ".$topic['new_last_topic_id']." "
					."WHERE id = ".$forum['id']);
				$i++;

			}
			
			$_SESSION['resync_stats']['forum_topics'] = true;
			
			return '<p>'.$i.' forum(s) have been adjusted.</p>';

		}

		function stage_members() {

			global $db;
			
			$result = $db->query(
				"SELECT m.id, m.posts, COUNT(p.id) AS new_posts "
				."FROM ".TABLE_PREFIX."forums f, ".TABLE_PREFIX."topics t, ".TABLE_PREFIX."posts p, ".TABLE_PREFIX."members m "
				."WHERE m.id = p.poster_id AND p.topic_id = t.id AND t.forum_id = f.id AND f.increase_post_count = 1 "
				."GROUP BY m.id");
			$i = 0;
			while ( $member = $db->fetch_result($result) ) {

				if ( $member['posts'] == $member['new_posts'] )
					continue;
				
				$db->query(
					"UPDATE ".TABLE_PREFIX."members "
					."SET posts = ".$member['new_posts']." "
					."WHERE id = ".$member['id']);
				$i++;

			}
			
			$_SESSION['resync_stats']['members'] = true;
			
			return '<p>'.$i.' member(s) have been adjusted.</p>';

		}

		function stage_global() {

			global $db;

			$result = $db->query(
				"SELECT SUM(topics) AS num_topics, SUM(posts) AS num_posts "
				."FROM ".TABLE_PREFIX."forums");
			$forums = $db->fetch_result($result);

			$result = $db->query(
				"SELECT COUNT(id) AS num_members "
				."FROM ".TABLE_PREFIX."members");
			$members = $db->fetch_result($result);

			$db->query("UPDATE ".TABLE_PREFIX."stats "
				."SET content = '".$forums['num_topics']."' "
				."WHERE name = 'topics'");
			$db->query("UPDATE ".TABLE_PREFIX."stats "
				."SET content = '".$forums['num_posts']."' "
				."WHERE name = 'posts'");
			$db->query("UPDATE ".TABLE_PREFIX."stats "
				."SET content = '".$members['num_members']."' "
				."WHERE name = 'members'");

			$_SESSION['resync_stats']['global'] = true;
			
			return '<p>Global statistics have been adjusted.</p>';

		}
		
		function run_module() {

			global $functions, $template;

			$content = '<p>With this module you can resynchronize the statistics across the entire forum. This is useful in case any counts have become out of sync due to a data race or incomplete third party script or addition.</p>';

			if ( !$functions->get_config('board_closed') ) {

				$content .= '<p><strong>Currently, the board is not closed. It is required to close the board during resynchronization. Please change this in &quot;General Configuration&quot;.</strong></p>';

				return $content;

			}

			$_SESSION['resync_stats'] = ( isset($_SESSION['resync_stats']) && is_array($_SESSION['resync_stats']) ) ? $_SESSION['resync_stats'] : array();
			$stages = array(
				'topic_counts' => array('Topic counts', 'Number of posts per topic.'),
				'topic_posts' => array('Topic posts', 'First and last post IDs.'),
				'forum_counts' => array('Forum counts', 'Number of topics and posts per forum.'), 
				'forum_topics' => array('Forum topics', 'Last topic IDs.'),
				'members' => array('Members', 'Number of posts per member (in forums with post count increasing only).'),
				'global' => array('Global', 'Global forum statistics.'),
			);

			if ( !empty($_GET['stage']) && isset($stages[$_GET['stage']]) ) {

				$content .= '<h2>Stage <em>'.$stages[$_GET['stage']][0].'</em></h2>'
					. call_user_func(array(&$this, 'stage_'.$_GET['stage']));

			}

			$content .= '<h2>Start resynchronization</h2>'
				.'<p>For best results, it is advised to execute the different stages in the order listed.</p>'
				.'<ul>';
			foreach ( $stages as $stage => $desc ) {

				$content .= '<li><a href="'.$functions->make_url('admin.php', array('act' => 'mod_resync_stats', 'stage' => $stage)).'">'.$desc[0].'</a>';
				if ( isset($_SESSION['resync_stats'][$stage]) )
					$content .= ' &ndash; completed';
				$content .= '<br />'.$desc[1].'</li>';

			}
			$content .= '</ul>';

			return $content;

		}		
	}
	
	$usebb_module = new usebb_module;
	
}

?>
