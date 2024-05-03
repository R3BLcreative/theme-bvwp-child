<?php extract($args); ?>

<div>
	<label for="license1" class="font-semibold text-secondary">License #</label>
	<input type="text" id="license1" name="updates[license1]" placeholder="License #" class="w-full" value="<?php echo $data['license']; ?>">
	<input type="hidden" id="license2" name="updates[license2]" value="">
</div>