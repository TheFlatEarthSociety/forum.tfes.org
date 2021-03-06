<?php
/**
 * Simple Machines Forum (SMF)
 *
 * @package SMF
 * @author Simple Machines
 * @copyright 2011 Simple Machines
 * @license http://www.simplemachines.org/about/smf/license.php BSD
 *
 * @version 2.0.16
 */

function template_main()
{
	global $context, $settings, $options, $txt, $scripturl;

	echo '
		<div class="cat_bar">
			<h3 class="catbg">
				<span class="ie6_header floatleft"><img src="', $settings['images_url'], '/email_sm.gif" alt="" class="icon" />', $txt['notify'], '</span>
			</h3>
		</div>
		<span class="upperframe"><span></span></span>
		<div class="roundframe centertext">
			<p>', $context['notification_set'] ? $txt['notify_deactivate'] : $txt['notify_request'], '</p>
			<p>
				<strong><a href="', $scripturl, '?action=notify;sa=', $context['notification_set'] ? 'off' : 'on', ';topic=', $context['current_topic'], '.', $context['start'], ';', (!empty($context['notify_info']['token']) ? 'u=' . $context['notify_info']['u'] . ';token=' . $context['notify_info']['token'] : $context['session_var'] . '=' . $context['session_id']), '">', $txt['yes'], '</a> - <a href="', $context['topic_href'], '">', $txt['no'], '</a></strong>
			</p>
		</div>
		<span class="lowerframe"><span></span></span>';
}

function template_notify_board()
{
	global $context, $settings, $options, $txt, $scripturl;

	echo '
		<div class="cat_bar">
			<h3 class="catbg">
				<span class="ie6_header floatleft"><img src="', $settings['images_url'], '/email_sm.gif" alt="" class="icon" />', $txt['notify'], '</span>
			</h3>
		</div>
		<span class="upperframe"><span></span></span>
		<div class="roundframe centertext">
			<p>', $context['notification_set'] ? $txt['notifyboard_turnoff'] : $txt['notifyboard_turnon'], '</p>
			<p>
				<strong><a href="', $scripturl, '?action=notifyboard;sa=', $context['notification_set'] ? 'off' : 'on', ';board=', $context['current_board'], '.', $context['start'], ';', (!empty($context['notify_info']['token']) ? 'u=' . $context['notify_info']['u'] . ';token=' . $context['notify_info']['token'] : $context['session_var'] . '=' . $context['session_id']), '">', $txt['yes'], '</a> - <a href="', $context['board_href'], '">', $txt['no'], '</a></strong>
			</p>
		</div>
		<span class="lowerframe"><span></span></span>';
}

function template_notify_announcements()
{
	global $context, $settings, $options, $txt, $scripturl;

	echo '
		<div class="cat_bar">
			<h3 class="catbg">
				<span class="ie6_header floatleft"><img src="', $settings['images_url'], '/email_sm.gif" alt="" class="icon" />', $txt['notify'], '</span>
			</h3>
		</div>
		<span class="upperframe"><span></span></span>
		<div class="roundframe centertext">
			<p>', $txt['notifyannouncements_prompt'], '</p>
			<p>
				<strong><a href="', $scripturl, '?action=notifyannouncements;sa=on;', (!empty($context['notify_info']['token']) ? 'u=' . $context['notify_info']['u'] . ';token=' . $context['notify_info']['token'] : $context['session_var'] . '=' . $context['session_id']), '">', $txt['yes'], '</a> - <a href="', $scripturl, '?action=notifyannouncements;sa=off;', (!empty($context['notify_info']['token']) ? 'u=' . $context['notify_info']['u'] . ';token=' . $context['notify_info']['token'] : $context['session_var'] . '=' . $context['session_id']), '">', $txt['no'], '</a></strong>
			</p>
		</div>
		<span class="lowerframe"><span></span></span>';
}

function template_notify_pref_changed()
{
	global $context, $settings, $options, $txt, $scripturl;

	echo '
		<div class="cat_bar">
			<h3 class="catbg">
				<span class="ie6_header floatleft"><img src="', $settings['images_url'], '/email_sm.gif" alt="" class="icon" />', $txt['notify'], '</span>
			</h3>
		</div>
		<span class="upperframe"><span></span></span>
		<div class="roundframe centertext">
			<p>', $context['notify_success_msg'], '</p>
		</div>
		<span class="lowerframe"><span></span></span>';
}

?>