<?php

/*
	Copyright (C) 2003-2011 UseBB Team
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
 * @copyright	Copyright (C) 2003-2011 UseBB Team
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
			while ( $topics = $db->fetch_result($result) ) {

				if ( $topics['count_replies'] == $topics['num_posts'] - 1 )
					continue;
				
				$db->query(
					"UPDATE ".TABLE_PREFIX."topics "
					."SET count_replies = ".( $topics['num_posts'] - 1 )." "
					."WHERE id = ".$topics['id']);
				$i++;

			}
			
			$_SESSION['resync_stats']['topic_counts'] = true;
			
			return '<p>'.$i.' topics have been adjusted.</p>';

		}

		function stage_topic_posts() {

			

		}

		function stage_forum_counts() {



		}

		function stage_forum_topics() {



		}

		function stage_members() {



		}

		function stage_global() {



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
				'members' => array('Members', 'Number of posts per member.'),
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
