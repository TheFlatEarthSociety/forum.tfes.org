<?php

function Unsubscribe() {
	global $smcFunc, $user_info;

	// Guests can't be subscribed *or* unsubscribed to topics.
	is_not_guest();

	if (!isset($_GET['topic']))
		fatal_lang_error('unsubscribe_notopic');
	$topic = $_GET['topic'];

	if (!isset($_GET['unsubscribe']))
		fatal_lang_error('unsubscribe_nounsubscribe');
	$unsubscribe = $_GET['unsubscribe'];
	if ($unsubscribe != '0')
		$unsubscribe = 1;

	$return = $_GET['return'];

	$smcFunc['db_query']('', '
		DELETE FROM {db_prefix}topics_unsubscribe
		WHERE id_topic = {int:topic}
			AND id_member = {int:member}',
		array(
			'topic' => $topic,
			'member' => $user_info['id'],
		)
	);
	$smcFunc['db_query']('', '
		INSERT INTO {db_prefix}topics_unsubscribe
			(id_topic, id_member, unsubscribed)
		VALUES
			({int:topic}, {int:member}, {int:unsubscribed})',
		array(
			'topic' => $topic,
			'member' => $user_info['id'],
			'unsubscribed' => $unsubscribe,
		)
	);

	if ($return and $return == 'unreadreplies')
		redirectexit('action=unreadreplies');
	elseif ($return and preg_match('/^[0-9]+$/', $return))
		redirectexit('topic=' . $topic . '.' . $return);
	else
		redirectexit('topic=' . $topic);
}

function CheckUnsubscribe() {
	global $context, $smcFunc, $user_info;

	if ($context['is_subscribed'] or $context['is_unsubscribed'])
		return;

	$topic = $context['current_topic'];
	$request = $smcFunc['db_query']('', '
		SELECT m.id_topic, IFNULL(u.unsubscribed, 0)
		FROM {db_prefix}messages as m
			LEFT JOIN {db_prefix}topics_unsubscribe AS u ON (u.id_topic = m.id_topic AND u.id_member = m.id_member)
		WHERE m.id_topic = {int:topic}
			AND m.id_member = {int:member}
		',
		array(
			'topic' => $topic,
			'member' => $user_info['id'],
		)
	);
	$row = $smcFunc['db_fetch_assoc']($request);
	$smcFunc['db_free_result']($request);

	if ($row['id_topic'] != $topic)
		return;

	if ($row['ifnull'] == 0)
		$context['is_subscribed'] = 1;
	else
		$context['is_unsubscribed'] = 1;
}
