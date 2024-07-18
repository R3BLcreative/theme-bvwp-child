<?php extract($args); ?>

<div>
	<label for="primary_email" class="font-semibold text-secondary">Email Address</label>
	<input type="text" id="primary_email" name="updates[primary_email]" placeholder="Email Address" class="w-full" value="<?php echo $data['primary_email']; ?>">
</div>