<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Stripe Payment Gateway</title>
        <!-- CSS only -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
        <script src="https://code.jquery.com/jquery-3.6.0.slim.min.js"></script>
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
    </head>
    
    <body>
    
        <nav class="navbar navbar-light bg-light">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">
                <img src="https://cdn.learnwoo.com/wp-content/uploads/2016/11/Payment-Gateway_Stripe.png" alt="" width="75" height="45" class="d-inline-block">
                Stripe Payment Gatway
                </a>
            </div>
        </nav> 

<div class="container pt-5">
	<div class="row">	


        <div class="col-md-4">

        </div>
        <div class="col-md-4">
            <form action="<?php echo base_url('stripe/oneclick'); ?>" method="post" class="p-2">
            <div class="form-group">
                <label for="amount">Enter Amount</label>    
                <input class="form-control" type="number" min="10" id="amount" name="amount" step="any" required>
            </div>
            <div class="form-group">
                <label for="clientname">Client Name</label>    
                <input class="form-control" type="text" id="clientname" name="clientname" required>
            </div>
            <div class="form-group">
                <label for="clientemail">Client Email</label>    
                <input class="form-control" type="email" id="clientemail" name="clientemail" required>
            </div>
            <div class="form-group">
                <label for="desc">Transaction Description</label> 
                <textarea class="form-control" name="desc" id="desc" cols="30" rows="10"></textarea>   
            </div>

                <script
                    src="https://checkout.stripe.com/checkout.js" class="stripe-button"
                    data-key="<?php echo STRIPE_PUBLISHABLE_KEY; ?>"
                    data-amount="1000"
                    data-name="Programming with Nutan"
                    data-description="Programming with Nutan Desc"
                    data-image="<?php echo base_url('assets/images/logo-white-theme.png'); ?>"
                    data-currency="usd"
                    data-email="phpnutan@gmail.com"
                >
                </script>
            </form>
                 
        </div>
        <div class="col-md-4">
        </div>
       
    </div>
</div> 

<script>

    $(document).ready(function() {
        // $('#amount').on('blur',function(){
        //     console.log(this.value)
        // });
        $("#amount").keyup(function(){
            const amt = $(this).value();
            alert(amt);
            // $('#stripe-button').attr('data-amount').value(amt);
        });
    });

</script>
<!-- <script type="text/javascript" src="https://js.stripe.com/v2/"></script>
   
<script> 
    //set your publishable key
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
   


   </body>
</html>