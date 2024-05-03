<?php extract($args); ?>

<div>
	<label for="street1" class="font-semibold text-secondary">Street Address</label>
	<input type="text" id="street1" name="updates[street1]" placeholder="Street Address" class="w-full" value="<?php echo $data['street1']; ?>">
</div>

<div>
	<label for="street2" class="font-semibold text-secondary">Address Line 2</label>
	<input type="text" id="street2" name="updates[street2]" placeholder="Address Line 2" class="w-full" value="<?php echo $data['street2']; ?>">
</div>

<div>
	<label for="city" class="font-semibold text-secondary">City</label>
	<input type="text" id="city" name="updates[city]" placeholder="City" class="w-full" value="<?php echo $data['city']; ?>">
</div>

<div>
	<label for="state" class="font-semibold text-secondary">State</label>
	<input type="text" id="state" name="updates[state]" placeholder="State" class="w-full" value="<?php echo $data['state']; ?>" maxlength="2">
</div>

<div>
	<label for="postal_code" class="font-semibold text-secondary">Postal Code</label>
	<input type="text" id="postal_code" name="updates[postal_code]" placeholder="Postal Code" class="w-full" value="<?php echo $data['postal_code']; ?>" maxlength="5">
</div>