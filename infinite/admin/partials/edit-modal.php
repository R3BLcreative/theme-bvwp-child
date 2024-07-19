<?php
extract($args);
?>

<div id="<?php echo $options['slug']; ?>" class="hidden fixed z-0 left-0 top-0 w-full h-full overflow-auto bg-black/50">
	<div class="bg-surface-800 my-[15%] mx-auto p-6 border border-secondary rounded-lg w-full max-w-[500px] relative">
		<button id="<?php echo $options['slug']; ?>-close" class="absolute top-2 right-2 w-6 h-6 fill-white hover:fill-primary">
			<?php echo inf_get_icon('close'); ?>
		</button>
		<div class="pt-6">
			<h2 class="h2 mb-4"><?php echo $options['title']; ?></h2>

			<form action="<?php echo admin_url('admin-ajax.php'); ?>" class="flex flex-col gap-4">
				<input type="hidden" name="action" value="<?php echo $options['action']; ?>">
				<input type="hidden" name="nonce" value="<?php echo wp_create_nonce('ajax_' . $options['action']); ?>">
				<input type="hidden" name="ID" value="<?php echo $data['ID']; ?>">

				<?php
				get_template_part(
					'infinite/admin/partials/form',
					$options['form'],
					[
						'data' => $data,
						'options' => $options
					]
				);
				?>

				<div class="mt-6">
					<button type="submit" class="infinite-button btn-primary !w-full">Update</button>
				</div>
			</form>
		</div>
	</div>
</div>