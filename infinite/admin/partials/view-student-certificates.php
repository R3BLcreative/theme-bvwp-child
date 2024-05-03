<div class="col-span-full infinite-card">
	<h2 class="h2">Certificates</h2>

	<div class="">
		<div class="grid grid-cols-9 gap-4 py-2 px-4">
			<div class="text-white col-span-7">Course</div>
			<div class="text-white col-span-2">Completion Date</div>
		</div>

		<?php
		if ($args) :
			foreach ($args as $cert) :
				$nonce = wp_create_nonce('cert_nonce');
				$date = strtotime($cert['passed_at']);
				$link = admin_url('admin-ajax.php?action=generate_cert&nonce=' . $nonce . '&SID=' . $cert['SID'] . '&CID=' . $cert['CID'] . '&date=' . $date);
		?>
				<a href="<?php echo $link; ?>" data-nonce="<?php echo $nonce; ?>" target="_blank" class="grid grid-cols-9 items-center gap-4 py-3 px-4 even:bg-surface-800 hover:bg-surface-600" aria-label="View certificate">
					<div class="col-span-7"><?php echo $cert['course']; ?></div>
					<div class="col-span-2"><?php echo $cert['passed_at']; ?></div>
				</a>
		<?php
			endforeach;
		endif;
		?>
	</div>
</div>