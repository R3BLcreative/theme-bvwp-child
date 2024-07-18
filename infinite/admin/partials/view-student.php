<?php extract($args); ?>

<section class="flex flex-col gap-6">
	<div class="grid grid-cols-9 gap-6">

		<?php
		get_template_part('infinite/admin/partials/view', 'student-info', $student);

		get_template_part('infinite/admin/partials/view', 'student-courses', $courses);

		get_template_part('infinite/admin/partials/view', 'student-certificates', $certificates);
		?>

	</div>
</section>