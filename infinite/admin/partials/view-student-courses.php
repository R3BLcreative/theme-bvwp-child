<div class="col-span-7 infinite-card">
	<h2 class="h2">Courses</h2>

	<div class="">
		<div class="grid grid-cols-9 gap-4 py-2 px-4">
			<div class="text-white text-center">Order #</div>
			<div class="text-white col-span-5">Course</div>
			<div class="text-white col-span-3">Schedule</div>
		</div>

		<?php
		if ($args) :
			foreach ($args as $course) :
				$link = admin_url('admin.php?page=infinite-rosters&view=roster-details&ID=' . $course['ID']);
		?>
				<a href="<?php echo $link; ?>" aria-label="View Roster" class="grid grid-cols-9 gap-4 py-3 px-4 even:bg-surface-800 hover:bg-surface-600">
					<div class="text-center"><?php echo $course['order']; ?></div>
					<div class="col-span-5"><?php echo $course['title']; ?></div>
					<div class="col-span-3"><?php echo $course['sched']; ?></div>
				</a>
		<?php
			endforeach;
		endif;
		?>
	</div>
</div>