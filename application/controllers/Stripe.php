<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stripe extends CI_Controller {
 
	public function index()
	{
		// echo 'Stripe payemnt gateway'; 
		$data['amount'] = 100;
		$data['tax'] = 0;
		$data['currency_code'] = 'USD'; 
		$data['item_name'] = 'test product';
		$data['item_number'] = 123;
		$data['type'] = '';
		$data['base_price'] = 10;
		$data['day'] ='';
		$data['user_id'] = 123;
		$this->load->view('stripe/product_form', $data);
	}

	public function check()
	{ 
	
		if($_POST['stripeToken']==""){
			redirect('users');
		}

		//check whether stripe token is not empty
		if(!empty($_POST['stripeToken']))
		{
			//get token, card and user info from the form
			$email 		= 'nutan2247@gmail.com';
			$token  	= $_POST['stripeToken'];
			$type  		= $_POST['type'];
			$user_id  	= $_POST['user_id'];
			$buyername 	= $_POST['name'];
			$buyeremail = $_POST['email'];
			$card_num 	= $_POST['card_num'];
			$card_cvc 	= $_POST['cvc'];
			$card_exp_month = $_POST['exp_month'];
			$card_exp_year 	= $_POST['exp_year'];
			// echo APPPATH.'third_party/stripe/init.php';die; 
			//include Stripe PHP library
			require_once APPPATH."third_party/stripe/init.php";

			//set api key
			$stripe = array(
			  "secret_key"      => STRIPE_SECRET_KEY,
			  "publishable_key" => STRIPE_PUBLISHABLE_KEY
			);
			
			\Stripe\Stripe::setApiKey($stripe['secret_key']);
			
			//add customer to stripe
			$customer = \Stripe\Customer::create(array(
				'email' => $email,
				'source'  => $token,
				'name' => 'Jenny Rosen',
				'address' => [
					'line1' => '510 Townsend St',
					'postal_code' => '98140',
					'city' => 'San Francisco',
					'state' => 'CA',
					'country' => 'US',
				],
			));

			//item information
			$itemName = $_POST['item_name'];
			$itemNumber = $_POST['item_number']*100;
			$itemPrice = (int)$_POST['amount']*100;
			$currency = $_POST['currency_code'];
			// $orderID = "SKA92712382139";
			
			//charge a credit or a debit card
			$charge = \Stripe\Charge::create(array(
				'customer' 		=> $customer->id,
				'amount'   		=> $itemPrice,
				'currency' 		=> $currency,
				'description' 	=> $itemNumber,
				'shipping' 		=> [
					'name' => 'Jenny Rosen',
					'address' => [
					'line1' => '510 Townsend St',
					'postal_code' => '98140',
					'city' => 'San Francisco',
					'state' => 'CA',
					'country' => 'US',
					],
							],
				'metadata' 		=> array(
					'item_id' 		=> $itemNumber,
					'username'   => $_POST['name'],
					'name'       => $_POST['name'],
					'registered' => date('y-m-d'),
					'user_email' => $email
					)
			));
			
			//retrieve charge details
			$chargeJson = $charge->jsonSerialize();
			// echo '<pre>';print_r($chargeJson);die;

			//check whether the charge is successful
			if($chargeJson['amount_refunded'] == 0 && empty($chargeJson['failure_code']) && $chargeJson['paid'] == 1 && $chargeJson['captured'] == 1)
			{
				//order details 
				$amount = $chargeJson['amount']/100;
				$balance_transaction = $chargeJson['balance_transaction'];
				$currency = $chargeJson['currency'];
				$status = $chargeJson['status'];
				$date = date("Y-m-d H:i:s"); 
				
				//insert tansaction data into the database
				$dataDB = array(
					'buyer_name' 		=> $buyername,
					'buyer_email' 		=> $buyeremail, 
					'card_number' 		=> $card_num, 
					'product_type' 		=> $type, 
					'user_id' 			=> $user_id, 
					// 'card_cvc' 			=> $card_cvc, 
					'card_exp_month' 	=> $card_exp_month, 
					'card_exp_year' 	=> $card_exp_year, 
					'product_name' 		=> $itemName, 
					'product_id' 		=> $itemNumber, 
					// 'item_price' 		=> $itemPrice, 
					'tax' 				=> 1, 
					'currency' 			=> $currency, 
					'paid_amount' 		=> $amount, 
					'currency' 			=> $currency, 
					'txn_id' 			=> $balance_transaction, 
					'payment_status' 	=> $status,
					'token'				=> $_POST['stripeToken'],
					'added_on' 			=> $date,
					'transaction_details' => json_encode($_POST)
				);

				// $this->load->view('stripe/payment_success', $data);
				echo '<pre>'; print_r($dataDB); echo '</pre>'; 	die;
			}
			else
			{
				$this->session->set_flashdata('response',"Invalid Token");
					redirect('stripe/payment_error');
			}
		}else{ echo'no token'; }
	}

	public function payment_success()
	{	
		$this->load->view('template/header_home');	
		$this->load->view('stripe/payment_success');
		$this->load->view('template/footer_home');	
	}

	public function payment_error()
	{
		$this->load->view('template/header_home');	
		$this->load->view('stripe/payment_error');
		$this->load->view('template/footer_home');	
	}

	public function help()
	{
		$this->load->view('template/header_home');	
		$this->load->view('stripe/help');
		$this->load->view('template/footer_home');	
	}


} ?>
