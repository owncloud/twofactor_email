<?php

// Script is added for demonstration purposes only
script('twofactor_email', 'challenge');

?>

<form method="POST">
	<input type="text" name="challenge" required="required">
	<input type="submit" class="button" value="Verify">
</form>
