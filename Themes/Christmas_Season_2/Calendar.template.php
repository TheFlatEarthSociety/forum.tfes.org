<?php
// Version: 2.0 Beta 3 Public; Calendar

// The main calendar - January, for example.
function template_main()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings;
	echo '
		<div style="padding: 3px;">', theme_linktree(), '</div>
		<div>
			<div style="padding: 1px; align: center; float: left; width: 200px;">
					', template_show_month_grid('prev'), '
					', template_show_month_grid('current'), '
					', template_show_month_grid('next'), '
			</div>
			<div style="align: center;', $context['browser']['is_ie'] ? 'float: right; ' : 'margin-left: 220px; ', '">
				', $context['view_week'] ? template_show_week_grid('main') : template_show_month_grid('main'), '
			</div>
		</div>
		<form style="clear: both;" action="', $scripturl, '?action=calendar" method="post" accept-charset="', $context['character_set'], '">
			<table cellspacing="0" cellpadding="3" width="100%" class="tborder">
				<tr class="catbg3">
					<td align="center">';
	// Show a little "post event" button?
	if ($context['can_post'])
		echo '
						<a href="', $scripturl, '?action=calendar;sa=post;month=', $context['current_month'], ';year=', $context['current_year'], ';sesc=', $context['session_id'], '">', create_button('calendarpe.gif', 'calendar_post_event', 'calendar_post_event', 'align="middle"'), '</a>';
	echo '
					</td>
					<td align="center">
						<select name="month">';
	// Show a select box with all the months.
	foreach ($txt['months'] as $number => $month)
		echo '
							<option value="', $number, '"', $number == $context['current_month'] ? ' selected="selected"' : '', '>', $month, '</option>';
	echo '
						</select>&nbsp;
						<select name="year">';
	// Show a link for every year.....
	for ($year = $modSettings['cal_minyear']; $year <= $modSettings['cal_maxyear']; $year++)
		echo '
							<option value="', $year, '"', $year == $context['current_year'] ? ' selected="selected"' : '', '>', $year, '</option>';
	echo '
						</select>&nbsp;
						<input type="submit" value="', $txt['view'], '" />
					</td>
					<td align="center">';
	// Show another post button just for symmetry.
	if ($context['can_post'])
		echo '
						<a href="', $scripturl, '?action=calendar;sa=post;month=', $context['current_month'], ';year=', $context['current_year'], ';sesc=', $context['session_id'], '">', create_button('calendarpe.gif', 'calendar_post_event', 'calendar_post_event', 'align="middle"'), '</a>';
	echo '
					</td>
				</tr>
			</table>
		</form>';
}

// Template for posting a calendar event.
function template_event_post()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings;

	// Start the javascript for drop down boxes...
	echo '
		<script language="JavaScript" type="text/javascript"><!-- // --><![CDATA[
			var monthLength = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

			function generateDays()
			{
				var days = 0, selected = 0;
				var dayElement = document.getElementById("day"), yearElement = document.getElementById("year"), monthElement = document.getElementById("month");

				monthLength[1] = 28;
				if (yearElement.options[yearElement.selectedIndex].value % 4 == 0)
					monthLength[1] = 29;

				selected = dayElement.selectedIndex;
				while (dayElement.options.length)
					dayElement.options[0] = null;

				days = monthLength[monthElement.value - 1];

				for (i = 1; i <= days; i++)
					dayElement.options[dayElement.length] = new Option(i, i);

				if (selected < days)
					dayElement.selectedIndex = selected;
			}

			function toggleLinked(form)
			{
				form.board.disabled = !form.link_to_board.checked;
			}

			function saveEntities()
			{
				document.forms.postevent.evtitle.value = document.forms.postevent.evtitle.value.replace(/&#/g, "&#38;#");
			}
		// ]]></script>

		<form action="', $scripturl, '?action=calendar;sa=post" method="post" name="postevent" accept-charset="', $context['character_set'], '" onsubmit="submitonce(this);saveEntities();" style="margin: 0;">
			<table width="55%" align="center" cellpadding="0" cellspacing="3">
				<tr>
					<td valign="bottom" colspan="2">
						', theme_linktree(), '
					</td>
				</tr>
			</table>';

	if (!empty($context['event']['new']))
		echo '
			<input type="hidden" name="eventid" value="', $context['event']['eventid'], '" />';

	// Start the main table.
	echo '
			<table border="0" width="55%" align="center" cellspacing="1" cellpadding="3" class="bordercolor">
				<tr class="titlebg">
					<td>', $context['page_title'], '</td>
				</tr>
				<tr>
					<td class="windowbg">
						<table border="0" cellpadding="3" width="100%">';

	if (!empty($context['post_error']['messages']))
	{
		echo '
							<tr>
								<td></td>
								<td>
									', $context['error_type'] == 'serious' ? '<b>' . $txt['error_while_submitting'] . '</b>' : '', '
									<div style="color: red; margin: 1ex 0 2ex 3ex;">
										', implode('<br />', $context['post_error']['messages']), '
									</div>
								</td>
							</tr>';
	}
	echo '
							<tr>
								<td align="right">
									<b', isset($context['post_error']['no_event']) ? ' style="color: red;"' : '', '>', $txt['calendar_event_title'], '</b>
								</td>
								<td class="smalltext">
									<input type="text" name="evtitle" maxlength="30" size="30" value="', $context['event']['title'], '" style="width: 90%;" />
								</td>
							</tr><tr>
								<td></td>
								<td class="smalltext">
									<input type="hidden" name="calendar" value="1" />', $txt['calendar_year'], '&nbsp;
									<select name="year" id="year" onchange="generateDays();">';

	// Show a list of all the years we allow...
	for ($year = $modSettings['cal_minyear']; $year <= $modSettings['cal_maxyear']; $year++)
		echo '
										<option value="', $year, '"', $year == $context['event']['year'] ? ' selected="selected"' : '', '>', $year, '</option>';

	echo '
									</select>&nbsp;
									', $txt['calendar_month'], '&nbsp;
									<select name="month" id="month" onchange="generateDays();">';

	// There are 12 months per year - ensure that they all get listed.
	for ($month = 1; $month <= 12; $month++)
		echo '
										<option value="', $month, '"', $month == $context['event']['month'] ? ' selected="selected"' : '', '>', $txt['months'][$month], '</option>';

	echo '
									</select>&nbsp;
									', $txt['calendar_day'], '&nbsp;
									<select name="day" id="day">';

	// This prints out all the days in the current month - this changes dynamically as we switch months.
	for ($day = 1; $day <= $context['event']['last_day']; $day++)
		echo '
										<option value="', $day, '"', $day == $context['event']['day'] ? ' selected="selected"' : '', '>', $day, '</option>';

	echo '
									</select>
								</td>
							</tr>';

	// If events can span more than one day then allow the user to select how long it should last.
	if (!empty($modSettings['cal_allowspan']))
	{
		echo '
							<tr>
								<td align="right"><b>', $txt['calendar_numb_days'], '</b></td>
								<td class="smalltext">
									<select name="span">';

		for ($days = 1; $days <= $modSettings['cal_maxspan']; $days++)
			echo '
										<option value="', $days, '"', $context['event']['span'] == $days ? ' selected="selected"' : '', '>', $days, '</option>';

		echo '
									</select>
								</td>
							</tr>';
	}

	// If this is a new event let the user specify which board they want the linked post to be put into.
	if ($context['event']['new'])
	{
		echo '
							<tr>
								<td align="right"><b>', $txt['calendar_link_event'], '</b></td>
								<td class="smalltext">
									<input type="checkbox" class="check" name="link_to_board" checked="checked" onclick="toggleLinked(this.form);" />
								</td>
							</tr>
							<tr>
								<td align="right"><b>', $txt['calendar_post_in'], '</b></td>
								<td class="smalltext">
									<select id="board" name="board" onchange="this.form.submit();">';
		foreach ($context['event']['categories'] as $category)
		{
			echo '
										<optgroup label="', $category['name'], '">';
			foreach ($category['boards'] as $board)
				echo '
											<option value="', $board['id'], '"', $board['selected'] ? ' selected="selected"' : '', '>', $board['child_level'] > 0 ? str_repeat('==', $board['child_level'] - 1) . '=&gt;' : '', ' ', $board['name'], '</option>';
			echo '
										</optgroup>';
		}
		echo '
									</select>
								</td>
							</tr>';
	}

	echo '
							<tr align="center">
								<td colspan="2">
									<input type="submit" value="', empty($context['event']['new']) ? $txt['save'] : $txt['post'], '" />';
	// Delete button?
	if (empty($context['event']['new']))
		echo '
									<input type="submit" name="deleteevent" value="', $txt['event_delete'], '" onclick="return confirm(\'', $txt['calendar_confirm_delete'], '\');" />';

	echo '
									<input type="hidden" name="sc" value="', $context['session_id'], '" />
									<input type="hidden" name="eventid" value="', $context['event']['eventid'], '" />
								</td>
							</tr>';

	echo '
						</table>
					</td>
				</tr>
			</table>
		</form>';
}

// Display a monthly calendar grid.
function template_show_month_grid($grid_name)
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings;

	if (!isset($context['calendar_grid_' . $grid_name]))
		return false;

	$calendar_data = &$context['calendar_grid_' . $grid_name];
	$colspan = !empty($calendar_data['show_week_links']) ? 8 : 7;

	echo '
		<table cellspacing="1" cellpadding="2" width="100%" class="bordercolor" style="margin-bottom: 1em;">';

	if (empty($calendar_data['disable_title']))
	{
		echo '
			<tr class="titlebg6">
				<td style="font-size: ', $calendar_data['size'] == 'large' ? 'x-large' : 'x-small', ';" align="center" colspan="', $colspan, '">';

		if (empty($calendar_data['previous_calendar']['disabled']) && $calendar_data['show_next_prev'])
			echo '
						<b style="display: block; float: left;"><a href="', $calendar_data['previous_calendar']['href'], '">&#171;</a></b>';

		if (empty($calendar_data['next_calendar']['disabled']) && $calendar_data['show_next_prev'])
			echo '
						<b style="display: block; float: right;"><a href="', $calendar_data['next_calendar']['href'], '">&#187;</a></b>';

		if ($calendar_data['show_next_prev'])
			echo '
						', $txt['months_titles'][$calendar_data['current_month']], ' ', $calendar_data['current_year'];
		else
			echo '
						<a href="', $scripturl, '?action=calendar;year=', $calendar_data['current_year'], ';month=', $calendar_data['current_month'], '">', $txt['months_titles'][$calendar_data['current_month']], ' ', $calendar_data['current_year'], '</a>';


		echo '
				</td>
			</tr>';
	}

	// Show each day of the week.
	if (empty($calendar_data['disable_day_titles']))
	{
		echo '
			<tr>';

		if (!empty($calendar_data['show_week_links']))
			echo '
				<td class="titlebg">&nbsp;</td>';

		foreach ($calendar_data['week_days'] as $day)
			echo '
				<td class="titlebg" width="14%" align="center" ', $calendar_data['size'] == 'small' ? 'style="font-size: x-small;"' : '', '>', !empty($calendar_data['short_day_titles']) ? substr($txt['days'][$day], 0, 1) : $txt['days'][$day], '</td>';
		echo '
			</tr>';
	}

	/* Each week in weeks contains the following:
		days (a list of days), number (week # in the year.) */
	foreach ($calendar_data['weeks'] as $week)
	{
		echo '
			<tr>';

		if (!empty($calendar_data['show_week_links']))
			echo '
				<td valign="middle" align="center" class="windowbg2">
					<a href="', $scripturl, '?action=calendar;viewweek;year=', $calendar_data['current_year'], ';month=', $calendar_data['current_month'], ';day=', $week['days'][0]['day'], '">&#187;</a>
				</td>';
		/* Every day has the following:
			day (# in month), is_today (is this day *today*?), is_first_day (first day of the week?),
			holidays, events, birthdays. (last three are lists.) */
		foreach ($week['days'] as $day)
		{
			// If this is today, make it a different color and show a border.
			echo '
				<td valign="top" align="center" style="height: ', $calendar_data['size'] == 'small' ? '20' : '100', 'px; padding: 2px;', $calendar_data['size'] == 'small' ? 'font-size: x-small;' : '', '" class="', $day['is_today'] ? 'calendar_today' : 'windowbg' , '">';

			// Skip it if it should be blank - it's not a day if it has no number.
			if (!empty($day['day']))
			{
				// Should the day number be a link?
				if (!empty($modSettings['cal_daysaslink']) && $context['can_post'])
						echo '
					<a href="', $scripturl, '?action=calendar;sa=post;month=', $calendar_data['current_month'], ';year=', $calendar_data['current_year'], ';day=', $day['day'], ';sesc=', $context['session_id'], '">', $day['day'], '</a>';
					else
						echo '
					', $day['day'];

				// Is this the first day of the week? (and are we showing week numbers?)
				if ($day['is_first_day'] && $calendar_data['size'] != 'small')
					echo '<span class="smalltext"> - <a href="', $scripturl, '?action=calendar;viewweek;year=', $calendar_data['current_year'], ';month=', $calendar_data['current_month'], ';day=', $day['day'], '">', $txt['calendar_week'], ' ', $week['number'], '</a></span>';

				// Are there any holidays?
				if (!empty($day['holidays']))
					echo '
					<div class="smalltext holiday">', $txt['calendar_prompt'], ' ', implode(', ', $day['holidays']), '</div>';

				// Show any birthdays...
				if (!empty($day['birthdays']))
				{
					echo '
					<div class="smalltext">
						<span class="birthday">', $txt['birthdays'], '</span> ';

					/* Each of the birthdays has:
						id, name (person), age (if they have one set?), and is_last. (last in list?) */
					$use_js_hide = empty($context['show_all_birthdays']) && count($day['birthdays']) > 15;
					$count = 0;
					foreach ($day['birthdays'] as $member)
					{
						echo '
						<a href="', $scripturl, '?action=profile;u=', $member['id'], '">', $member['name'], isset($member['age']) ? ' (' . $member['age'] . ')' : '', '</a>', $member['is_last'] || ($count == 10 && $use_js_hide)? '' : ', ';

						// Stop at ten?
						if ($count == 10 && $use_js_hide)
							echo '<span style="font-style: italic;" id="bdhidelink_', $day['day'], '">...<br /><a href="', $scripturl, '?action=calendar;month=', $calendar_data['current_month'], ';year=', $calendar_data['current_year'], ';showbd" onclick="document.getElementById(\'bdhide_', $day['day'], '\').style.display = \'\'; document.getElementById(\'bdhidelink_', $day['day'], '\').style.display = \'none\'; return false;">(', sprintf($txt['calendar_click_all'], count($day['birthdays'])), ')</a></span><span id="bdhide_', $day['day'], '" style="display: none;">, ';

						$count++;
					}
					if ($use_js_hide)
						echo '
						</span>';

					echo '
					</div>';
				}

				// Any special posted events?
				if (!empty($day['events']))
				{
					echo '
					<div class="smalltext">
						<span class="event">', $txt['events'], '</span>';
					/* The events are made up of:
						title, href, is_last, can_edit (are they allowed to?), and modify_href. */
					foreach ($day['events'] as $event)
					{
						// If they can edit the event, show a star they can click on....
						if ($event['can_edit'])
							echo '
						<a href="', $event['modify_href'], '" style="color: #FF0000;">*</a> ';

						echo '
						', $event['link'], $event['is_last'] ? '' : ', ';
					}
					echo '
					</div>';
				}
			}

			echo '
				</td>';
		}

		echo '
			</tr>';
	}

	echo '
		</table>';
}

// Or show a weekly one?
function template_show_week_grid($grid_name)
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings;

	if (!isset($context['calendar_grid_' . $grid_name]))
		return false;

	$calendar_data = &$context['calendar_grid_' . $grid_name];

	echo '
		<table cellspacing="1" cellpadding="2" width="100%" class="bordercolor">';

	// Loop through each month (At least one) and print out each day.
	foreach ($calendar_data['months'] as $month_data)
	{
		echo '
			<tr>
				<td class="titlebg" align="center" colspan="2" style="font-size: large;">';

		if (empty($calendar_data['previous_calendar']['disabled']) && $calendar_data['show_next_prev'] && empty($done_title))
			echo '
						<b style="float: left; display: block;"><a href="', $calendar_data['previous_week']['href'], '">&#171;</a></b>';

		if (empty($calendar_data['next_calendar']['disabled']) && $calendar_data['show_next_prev'] && empty($done_title))
			echo '
						<b style="float: right; display: block;"><a href="', $calendar_data['next_week']['href'], '">&#187;</a></b>';

		echo '
						<a href="', $scripturl, '?action=calendar;month=', $month_data['current_month'], ';year=', $month_data['current_year'], '">', $txt['months_titles'][$month_data['current_month']], ' ', $month_data['current_year'], '</a>', empty($done_title) && !empty($calendar_data['week_number']) ? (' - ' . $txt['calendar_week'] . ' ' . $calendar_data['week_number']) : '' , '
				</td>
			</tr>';

		$done_title = true;

		foreach ($month_data['days'] as $day)
		{
			echo '
			<tr>
				<td class="catbg" colspan="2">
					', $txt['days'][$day['day_of_week']], '
				</td>
			</tr>
			<tr style="height: 50px;">
				<td class="titlebg" align="center" width="25" style="font-size: large;">
					', $day['day'], '
				</td>
				<td class="', $day['is_today'] ? 'calendar_today' : 'windowbg', '" valign="top" width="100%">';

			// Are there any holidays?
			if (!empty($day['holidays']))
				echo '
					<div class="smalltext holiday">', $txt['calendar_prompt'], ' ', implode(', ', $day['holidays']), '</div>';

			// Show any birthdays...
			if (!empty($day['birthdays']))
			{
				echo '
					<div class="smalltext">
						<span class="birthday">', $txt['birthdays'], '</span> ';

				/* Each of the birthdays has:
					id, name (person), age (if they have one set?), and is_last. (last in list?) */
				foreach ($day['birthdays'] as $member)
					echo '
						<a href="', $scripturl, '?action=profile;u=', $member['id'], '">', $member['name'], isset($member['age']) ? ' (' . $member['age'] . ')' : '', '</a>', $member['is_last'] ? '' : ', ';
				echo '
					</div>';
			}

			// Any special posted events?
			if (!empty($day['events']))
			{
				echo '
					<div class="smalltext">
						<span class="event">', $txt['events'], '</span>';
				/* The events are made up of:
					title, href, is_last, can_edit (are they allowed to?), and modify_href. */
				foreach ($day['events'] as $event)
				{
					// If they can edit the event, show a star they can click on....
					if ($event['can_edit'])
						echo '
						<a href="', $event['modify_href'], '" style="color: #FF0000;">*</a> ';
						echo '
						', $event['link'], $event['is_last'] ? '' : ', ';
				}
				echo '
					</div>';
			}

			echo '
				</td>
			</tr>';
		}
	}

	echo '
		</table>';
}

function template_bcd()
{
	global $context, $scripturl;

		echo '
	<center>
	<table class="site" cellpadding="0" cellspacing="0">
		<caption>BCD Clock</caption>
		<tr class="odd">';
		$alt = false;
		foreach ($context['clockicons'] as $t => $v)
		{
			echo '
			<td style="border-', $alt ? 'right' : 'left', ': 1px solid black;" valign="bottom">';
			foreach ($v as $i)
				echo '
				<img src="', $context['offimg'], '" alt="" id="', $t, '_', $i, '" /><br />';
			echo '
			</td>';
			$alt = !$alt;
		}

		echo '
		</tr>
		<tr class="even"><td colspan="6" align="center"><a href="', $scripturl, '?action=clock;rb">Are you hardcore?</a></td></tr>
	</table>
	</center>
		<script language="JavaScript" type="text/javascript"><!-- // --><![CDATA[
		var icons = new Object();';

		foreach ($context['clockicons'] as $t => $v)
		{
			foreach ($v as $i)
				echo '
			icons[\'', $t, '_', $i, '\'] = document.getElementById(\'', $t, '_', $i, '\');';
		}

		echo '
		function update()
		{
			// Get the current time
			var time = new Date();
			var hour = time.getHours();
			var min = time.getMinutes();
			var sec = time.getSeconds();

			// Break it up into individual digits
			var h1 = parseInt(hour / 10);
			var h2 = hour % 10;
			var m1 = parseInt(min / 10);
			var m2 = min % 10;
			var s1 = parseInt(sec / 10);
			var s2 = sec % 10;

			// For each digit figure out which ones to turn off and which ones to turn on
			var turnon = new Array();';

		foreach ($context['clockicons'] as $t => $v)
		{
			foreach ($v as $i)
				echo '
			if (', $t, ' >= ', $i, ')
			{
				turnon.push("', $t, '_', $i, '");
				', $t, ' -= ', $i, ';
			}';
		}

		echo '
			for (var i in icons)
				if (!in_array(i, turnon))
					icons[i].src = "', $context['offimg'], '";
				else
					icons[i].src = "', $context['onimg'], '";

			window.setTimeout("update();", 500);
		}
		// Checks for variable in theArray.
		function in_array(variable, theArray)
		{
			for (var i = 0; i < theArray.length; i++)
			{
				if (theArray[i] == variable)
					return true;
			}
			return false;
		}

		update();
		// ]]></script>';
}

function template_hms()
{
	global $context, $scripturl;
	echo '
<center>
<table class="site" cellpadding="0" cellspacing="0" border="1">
	<caption>Binary Clock</caption>';
	$alt = false;
	foreach ($context['clockicons'] as $t => $v)
	{
		echo '
	<tr class="', $alt ? 'windowbg2' : 'windowbg', '">
		<td align="right">';
		foreach ($v as $i)
			echo '
			<img src="', $context['offimg'], '" alt="" id="', $t, '_', $i, '" />';
		echo '
		</td>';
		$alt = !$alt;
	}

	echo '
	</tr>
	<tr class="', $alt ? 'windowbg2' : 'windowbg', '"><td colspan="6" align="center"><a href="', $scripturl, '?action=clock">Too tough for you?</a></td></tr>
</table>
</center>';
	echo '
	<script language="JavaScript" type="text/javascript"><!-- // --><![CDATA[
	var icons = new Object();';

	foreach ($context['clockicons'] as $t => $v)
	{
		foreach ($v as $i)
			echo '
		icons[\'', $t, '_', $i, '\'] = document.getElementById(\'', $t, '_', $i, '\');';
	}

	echo '
	function update()
	{
		// Get the current time
		var time = new Date();
		var h = time.getHours();
		var m = time.getMinutes();
		var s = time.getSeconds();

		// For each digit figure out which ones to turn off and which ones to turn on
		var turnon = new Array();';

	foreach ($context['clockicons'] as $t => $v)
	{
		foreach ($v as $i)
			echo '
		if (', $t, ' >= ', $i, ')
		{
			turnon.push("', $t, '_', $i, '");
			', $t, ' -= ', $i, ';
		}';
	}

	echo '
		for (var i in icons)
			if (!in_array(i, turnon))
				icons[i].src = "', $context['offimg'], '";
			else
				icons[i].src = "', $context['onimg'], '";

		window.setTimeout("update();", 500);
	}
	// Checks for variable in theArray.
	function in_array(variable, theArray)
	{
		for (var i = 0; i < theArray.length; i++)
		{
			if (theArray[i] == variable)
				return true;
		}
		return false;
	}

	update();
	// ]]></script>';
}

function template_omfg()
{
	global $context, $scripturl;

	echo '
<center>
<table class="site" cellpadding="0" cellspacing="0" border="1">
	<caption>OMFG Binary Clock</caption>';
	$alt = false;
	foreach ($context['clockicons'] as $t => $v)
	{
		echo '
	<tr class="', $alt ? 'windowbg2' : 'windowbg', '">
		<td align="right">';
		foreach ($v as $i)
			echo '
			<img src="', $context['offimg'], '" alt="" id="', $t, '_', $i, '" />';
		echo '
		</td>';
		$alt = !$alt;
	}

	echo '
	</tr>
</table>
</center>';
	echo '
	<script language="JavaScript" type="text/javascript"><!-- // --><![CDATA[
	var icons = new Object();';

	foreach ($context['clockicons'] as $t => $v)
	{
		foreach ($v as $i)
			echo '
		icons[\'', $t, '_', $i, '\'] = document.getElementById(\'', $t, '_', $i, '\');';
	}

	echo '
	function update()
	{
		// Get the current time
		var time = new Date();
		var month = time.getMonth() + 1;
		var day = time.getDate();
		var year = time.getFullYear();
		year = year % 100;
		var hour = time.getHours();
		var min = time.getMinutes();
		var sec = time.getSeconds();

		// For each digit figure out which ones to turn off and which ones to turn on
		var turnon = new Array();';

	foreach ($context['clockicons'] as $t => $v)
	{
		foreach ($v as $i)
			echo '
		if (', $t, ' >= ', $i, ')
		{
			turnon.push("', $t, '_', $i, '");
			', $t, ' -= ', $i, ';
		}';
	}

	echo '
		for (var i in icons)
			if (!in_array(i, turnon))
				icons[i].src = "', $context['offimg'], '";
			else
				icons[i].src = "', $context['onimg'], '";

		window.setTimeout("update();", 500);
	}
	// Checks for variable in theArray.
	function in_array(variable, theArray)
	{
		for (var i = 0; i < theArray.length; i++)
		{
			if (theArray[i] == variable)
				return true;
		}
		return false;
	}

	update();
	// ]]></script>';
}

function template_thetime()
{
	global $context, $scripturl;

	echo '
<center>
<table class="site" cellpadding="0" cellspacing="0" border="1">
	<caption>The time you requested</caption>';
	$alt = false;
	foreach ($context['clockicons'] as $t => $v)
	{
		echo '
	<tr class="', $alt ? 'windowbg2' : 'windowbg', '">
		<td align="right">';
		foreach ($v as $i)
			echo '
			<img src="', $i ? $context['onimg'] : $context['offimg'], '" alt="" />';
		echo '
		</td>';
		$alt = !$alt;
	}

	echo '
	</tr>
</table>
</center>';

}

?>