<?php extract($args); ?>

<section class="flex flex-col gap-6">
	<div class="w-full infinite-card">

		<div class="flex items-center justify-between gap-6">
			<h2 class="h2 flex flex-row items-center gap-3">
				<span><?php echo $title; ?></span>
				<span class="text-white">-</span>
				<span class="text-secondary text-xl font-normal"><?php echo $schedule; ?></span>
			</h2>

			<?php
			$nonce = wp_create_nonce('roster_nonce');
			$link = admin_url('admin-ajax.php?action=generate_signin&nonce=' . $nonce . '&RID=' . $RID);
			?>
			<a href="<?php echo $link; ?>" target="_blank" class="infinite-button btn-alt" aria-label="Print Roster">
				<i class="w-5"><?php echo inf_get_icon('print'); ?></i>
				Print Sign In Sheet
			</a>
		</div>

		<div class="">
			<div class="grid grid-cols-12 gap-4 py-2 px-4">
				<div class="text-white">SID</div>
				<div class="text-white col-span-3">Student Name</div>
				<div class="text-white col-span-3">License</div>
				<div class="text-white col-span-2">Pass/Fail</div>
				<div class="text-white col-span-3">Actions</div>
			</div>


			<?php
			if ($roster) :
				foreach ($roster as $student) :
					$link = admin_url('admin.php?page=infinite-students&view=student-details&ID=' . $student['ID']);
			?>
					<div class="grid grid-cols-12 gap-4 py-3 px-4 even:bg-surface-800 hover:bg-surface-600 cursor-pointer">
						<div onclick="window.location='<?php echo $link; ?>';" aria-label="View Roster" class=""><?php echo $student['student_id']; ?></div>
						<div onclick="window.location='<?php echo $link; ?>';" aria-label="View Roster" class="col-span-3"><?php echo $student['first_name'] . ' ' . $student['last_name']; ?></div>
						<div onclick="window.location='<?php echo $link; ?>';" aria-label="View Roster" class="col-span-3">
							<?php echo ($student['license1']) ? $student['license1'] : $student['license2']; ?>
						</div>
						<div id="pass-fail" onclick="window.location='<?php echo $link; ?>';" aria-label="View Roster" class="col-span-2">
							<?php
							if (is_null($student['passed'])) {
								echo '---';
							} elseif ($student['passed']) {
								echo 'PASS';
							} else {
								echo 'FAIL';
							} ?>
						</div>
						<div class="col-span-3 flex items-center justify-start gap-8">
							<button onclick="passFailStudent(this);" data-action="update_passed" data-student="<?php echo $student['ID']; ?>" data-roster="<?php echo $student['RID']; ?>" type="button" id="" class="infinite-button btn-success btn-icon" aria-label="Student Passed Course">
								<i class="w-5"><?php echo inf_get_icon('check'); ?></i>
							</button>
							<!--  -->
							<button onclick="passFailStudent(this);" data-action="update_failed" data-student="<?php echo $student['ID']; ?>" data-roster="<?php echo $student['RID']; ?>" type="button" id="" class="infinite-button btn-error btn-icon" aria-label="Student Failed Course">
								<i class="w-5"><?php echo inf_get_icon('fail'); ?></i>
							</button>
							<!--  -->
							<!-- <button onclick="" data-action="" data-student="<?php echo $student['ID']; ?>" data-roster="<?php echo $student['RID']; ?>" type="button" id="" class="infinite-button btn-alt btn-icon" aria-label="Move Student to a Different Roster">
								<i class="w-5"><?php echo inf_get_icon('move'); ?></i>
							</button> -->
							<button type="button" data-modal="modal-move-roster-<?php echo $student['RID']; ?>" class="edit-modal-btn infinite-button btn-alt btn-icon" aria-label="Move Student to a Different Roster">
								<i class="w-5"><?php echo inf_get_icon('move'); ?></i>
							</button>
							<?php
							get_template_part(
								'infinite/admin/partials/edit',
								'modal',
								[
									'data' => $student,
									'options' => [
										'slug' => 'modal-move-roster-' . $student['RID'],
										'title' => 'Moving: ' . $student['first_name'] . ' ' . $student['last_name'],
										'form' => 'move-roster',
										'action' => 'move_roster',
									]
								]
							);
							?>
						</div>
					</div>
			<?php
				endforeach;
			endif;
			?>
		</div>
	</div>
</section>