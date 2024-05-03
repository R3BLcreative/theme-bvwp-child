<?php extract($args); ?>

<div class="col-span-2 infinite-card">

	<h2 class="h2">Contact Info</h2>

	<div class="group">
		<div class="font-semibold text-secondary flex items-center gap-3">
			Name
			<button class="edit-modal-btn w-4 h-4 fill-primary block opacity-0 pointer-events-none transition-all ease-in-out group-hover:opacity-100 group-hover:pointer-events-auto" data-modal="modal-student-name">
				<?php echo inf_get_icon('edit'); ?>
			</button>
		</div>
		<div class="text-white"><?php echo $full_name; ?></div>
	</div>

	<div class="group">
		<div class="font-semibold text-secondary flex items-center gap-3">
			License
			<button class="edit-modal-btn w-4 h-4 fill-primary block opacity-0 pointer-events-none transition-all ease-in-out group-hover:opacity-100 group-hover:pointer-events-auto" data-modal="modal-student-license">
				<?php echo inf_get_icon('edit'); ?>
			</button>
		</div>
		<div class="text-white"><?php echo $license; ?></div>
	</div>

	<div class="group">
		<div class="font-semibold text-secondary flex items-center gap-3">
			Phone
			<button class="edit-modal-btn w-4 h-4 fill-primary block opacity-0 pointer-events-none transition-all ease-in-out group-hover:opacity-100 group-hover:pointer-events-auto" data-modal="modal-student-phone">
				<?php echo inf_get_icon('edit'); ?>
			</button>
		</div>
		<div class="text-white"><?php echo $primary_phone; ?></div>
	</div>

	<div class="group">
		<div class="font-semibold text-secondary flex items-center gap-3">
			Email
			<button class="edit-modal-btn w-4 h-4 fill-primary block opacity-0 pointer-events-none transition-all ease-in-out group-hover:opacity-100 group-hover:pointer-events-auto" data-modal="modal-student-email">
				<?php echo inf_get_icon('edit'); ?>
			</button>
		</div>
		<div class="text-white"><?php echo $primary_email; ?></div>
	</div>

	<div class="group">
		<div class="font-semibold text-secondary flex items-center gap-3">
			Address
			<button class="edit-modal-btn w-4 h-4 fill-primary block opacity-0 pointer-events-none transition-all ease-in-out group-hover:opacity-100 group-hover:pointer-events-auto" data-modal="modal-student-address">
				<?php echo inf_get_icon('edit'); ?>
			</button>
		</div>
		<div class="text-white">
			<?php
			echo $street1 . '<br>';
			if (!empty($street2)) echo $street2 . '<br>';
			echo $city . ', ' . $state . ' ' . $postal_code;
			?>
		</div>
	</div>

</div>

<?php
get_template_part(
	'infinite/admin/partials/edit',
	'modal',
	[
		'data' => $args,
		'options' => [
			'slug' => 'modal-student-name',
			'title' => 'Edit Student Name',
			'form' => 'student-name',
			'action' => 'update_student',
		]
	]
);

get_template_part(
	'infinite/admin/partials/edit',
	'modal',
	[
		'data' => $args,
		'options' => [
			'slug' => 'modal-student-license',
			'title' => 'Edit Student License',
			'form' => 'student-license',
			'action' => 'update_student',
		]
	]
);

get_template_part(
	'infinite/admin/partials/edit',
	'modal',
	[
		'data' => $args,
		'options' => [
			'slug' => 'modal-student-phone',
			'title' => 'Edit Student Phone',
			'form' => 'student-phone',
			'action' => 'update_student',
		]
	]
);

get_template_part(
	'infinite/admin/partials/edit',
	'modal',
	[
		'data' => $args,
		'options' => [
			'slug' => 'modal-student-email',
			'title' => 'Edit Student Email',
			'form' => 'student-email',
			'action' => 'update_student',
		]
	]
);

get_template_part(
	'infinite/admin/partials/edit',
	'modal',
	[
		'data' => $args,
		'options' => [
			'slug' => 'modal-student-address',
			'title' => 'Edit Student Address',
			'form' => 'student-address',
			'action' => 'update_student',
		]
	]
);
?>