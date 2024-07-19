<?php
extract($args);

// Data needed... RID, CID, SID
$CID = $data['course_id'];
$selected = $data['schedule'];
$schedules = get_field('course_dates', $CID);
?>

<div>
	<input type="hidden" name="RID" value="<?php echo $data['RID']; ?>" />
	<label for="suffix" class="font-semibold text-secondary">Move to Roster</label>
	<select id="roster" name="updates[schedule]" class="w-full">
		<option value="" disabled>Select One</option>
		<?php
		foreach ($schedules as $schedule) :
			// Filter past dates
			$now = strtotime('now');
			$start = strtotime($schedule['start_date']);
			// if ($now > $start) continue;

			// Format
			$value = $schedule['start_date'];
			$value .= (!empty($schedule['end_date'])) ? ' - ' . $schedule['end_date'] : '';

			// Selected
			$isSelected = (isset($selected) && $selected == $value) ? 'selected' : '';
		?>
			<option value="<?php echo $value; ?>" <?php echo $isSelected; ?>>
				<?php echo $value; ?>
			</option>
		<?php endforeach; ?>
	</select>
</div>