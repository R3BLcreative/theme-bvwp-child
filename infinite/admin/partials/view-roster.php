<?php extract($args); ?>

<section class="flex flex-col gap-6">
	<div class="w-full infinite-card">

		<h2 class="h2 flex flex-row items-center gap-3">
			<span><?php echo $title; ?></span>
			<span class="text-white">-</span>
			<span class="text-secondary text-xl font-normal"><?php echo $schedule; ?></span>
		</h2>

		<div class="">
			<div class="grid grid-cols-12 gap-4 py-2 px-4">
				<div class="text-white">ID</div>
				<div class="text-white col-span-4">Student Name</div>
				<div class="text-white col-span-4">License</div>
				<div class="text-white col-span-3"></div>
			</div>


			<?php
			if ($roster) :
				foreach ($roster as $student) :
					//$link = admin_url('admin.php?page=infinite-rosters&view=roster-details&ID=' . $course['ID']);
					$link = '#';
			?>
					<a href="<?php echo $link; ?>" aria-label="View Roster" class="grid grid-cols-12 gap-4 py-3 px-4 even:bg-surface-800 hover:bg-surface-600">
						<div class=""><?php echo $student['student_id']; ?></div>
						<div class="col-span-4"><?php echo $student['first_name'] . ' ' . $student['last_name']; ?></div>
						<div class="col-span-4">
							<?php echo ($student['license1']) ? $student['license1'] : $student['license2']; ?>
						</div>
						<div class="col-span-3"></div>
					</a>
			<?php
				endforeach;
			endif;
			?>
		</div>
	</div>
</section>