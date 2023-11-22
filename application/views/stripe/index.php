<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// $config = $this->config->load('stripe_config', TRUE);
?>
<form action="submit.php" method="post">
	<script
		src="https://checkout.stripe.com/checkout.js" class="stripe-button"
		data-key="<?php echo STRIPE_SECRET_KEY; ?>"
		data-amount="1000"
		data-name="Programming with Nutan"
		data-description="Programming with Nutan Desc"
		data-image="https://www.logostack.com/wp-content/uploads/designers/eclipse42/small-panda-01-600x420.jpg"
		data-currency="usd"
		data-email="phpnutan@gmail.com"
	>
	</script>

</form>