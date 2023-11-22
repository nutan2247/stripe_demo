
<!-- Strip -->
<!-- <script type="text/javascript" src="https://js.stripe.com/v2/"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script> -->
<!-- Stripe JavaScript library -->  
<!-- <script> //set your publishable key
Stripe.setPublishableKey('<?php echo STRIPE_PUBLISHABLE_KEY; ?>');
    
    //callback to handle the response from stripe
    function stripeResponseHandler(status, response) {
        if (response.error) {
            //enable the submit button
            $('#payBtn').removeAttr("disabled");
            //display the errors on the form
            // $('#payment-errors').attr('hidden', 'false');
            $('#payment-errors').addClass('alert alert-danger');
            $("#payment-errors").html(response.error.message);
        } else {
            var form$ = $("#paymentFrm");
            //get token id
            var token = response['id'];
            //insert the token into the form
            form$.append("<input type='hidden' name='stripeToken' value='" + token + "' />");
            //submit form to the server
            form$.get(0).submit();
        }
    }
    $(document).ready(function() {
        //on form submit
        $("#paymentFrm").submit(function(event) {
            //disable the submit button to prevent repeated clicks
            $('#payBtn').attr("disabled", "disabled");
            
            //create single-use token to charge the user
            Stripe.createToken({
                number: $('#card_num').val(),
                cvc: $('#card-cvc').val(),
                exp_month: $('#card-expiry-month').val(),
                exp_year: $('#card-expiry-year').val()
            }, stripeResponseHandler);
            //submit from callback
            return false;
        });
    });
</script> -->
<!-- /Strip -->
    <!-- Begin page content -->
    <div class="container">
        <div class="col-md-4"></div>

        <div class="col-md-4">
            <div class="card">
                <img class="card-imgs-top" src="<?php echo ASSETS_URL.'images/strip_success.jpg'; ?>" alt="Card image cap" width="349" height="250">
                <div class="card-block" style="padding: 20px;">
                    <h4 class="card-title">Payment Successful #<?php echo $insertID; ?></h4>
                    <p class="card-text">We received your payment on your purchase, check your email for more information.</p>
                    <a href="<?php echo site_url('/'); ?>" class="btn btn-info btn-sm float-right">Go Home</a>
                </div>
            </div>
        </div>

        <div class="col-md-4"></div>
            
    </div> 
