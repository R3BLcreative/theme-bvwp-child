<?php extract($args); ?>

<div>
	<label for="first_name" class="font-semibold text-secondary">First Name</label>
	<input type="text" id="first_name" name="updates[first_name]" placeholder="First Name" class="w-full" value="<?php echo $data['first_name']; ?>">
</div>

<div>
	<label for="middle_name" class="font-semibold text-secondary">Middle Name</label>
	<input type="text" id="middle_name" name="updates[middle_name]" placeholder="Middle Name" class="w-full" value="<?php echo $data['middle_name']; ?>">
</div>

<div>
	<label for="last_name" class="font-semibold text-secondary">Last Name</label>
	<input type="text" id="last_name" name="updates[last_name]" placeholder="Last Name" class="w-full" value="<?php echo $data['last_name']; ?>">
</div>

<div>
	<label for="suffix" class="font-semibold text-secondary">Suffix</label>
	<input type="text" id="suffix" name="updates[suffix]" placeholder="Suffix" class="w-full" value="<?php echo $data['suffix']; ?>">
</div>