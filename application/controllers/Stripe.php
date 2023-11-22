<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stripe extends CI_Controller {
 
	public function index()
	{
		// echo 'Stripe payemnt gateway'; 
		$data['amount'] = 100;
		$data['tax'] = 0;
		$data['currency_code'] = 'INR'; 
		$data['item_name'] = 'test product';
		$data['item_number'] = 12;
		$data['type'] = '';
		$data['base_price'] = 10;
		$data['day'] ='';
		$data['user_id'] = 123;
		$this->load->view('stripe/product_form', $data);
	}

	public function oneclick()
	{
		//require_once APPPATH."third_party/stripe_php_payment_gateway/stripe-php-master/init.php";

		require_once APPPATH."third_party/stripe/init.php";
		if(isset($_POST['stripeToken'])){
			\Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);
			\Stripe\Stripe::setVerifySslCerts(false);
		
			$token=$_POST['stripeToken'];
		
			$data=\Stripe\Charge::create(array(
				"amount"=>1000,
				"currency"=>"inr",
				"description"=>"Programming with Nutan Kumar Desc",
				"source"=>$token,
			));
		
			echo "<pre>";
			print_r($data); die;
		}
		// $data = ($_REQUEST)?$_REQUEST:"";
		// $this->load->view('template/header_home');		
		// $this->load->view('stripe/index',$data);		
		// $this->load->view('template/footer_home');		
	}

	public function check()
	{ 
		// echo '<pre>';print_r($_REQUEST);die;
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
				'source'  => $token
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
				'metadata' 		=> array(
					'item_id' 		=> $itemNumber
					)
			));
			
			//retrieve charge details
			$chargeJson = $charge->jsonSerialize();

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
					
			    $insert_id = $this->users_model->save('tbl_payment_transaction',$dataDB);

			    if ($insert_id > 0) {

					switch ($type) {
					  case "course":
						$addcourse = array(
							'user_id'			=> $user_id,
							'payer_email'		=> $buyeremail,
							// 'payer_id'			=> $_REQUEST['payer_id'],
							// 'payer_status'		=> $_REQUEST['payer_status'],
							'txn_id'			=> $balance_transaction,
							'item_name'			=> $itemNumber,
							'quantity'			=> 1,
							'tax'				=> $_POST['tax'],
							'payment_fee'		=> $amount,
							'txn_status'		=> $status,
							'payment_type'		=> $type,
							// 'pending_reason'	=> $_REQUEST['pending_reason'],
							// 'paypal_payment_date'=> $_REQUEST['payment_date'],
							'status'			=> 1,
							'added_on'			=> date('y-m-d h:i:s'),
							'payment_at'		=> $date,
							'amount'			=> $amount,
							'transaction_details'=> json_encode($_POST)
						);
						$result = $this->user->save('tbl_purchase_llis',$addcourse);
					    break;

					  case "training":
					    $addtrainig = array(
							'user_id'			=> $user_id,
							'training_seminar_id' => $itemNumber,
							'payment_status' 	=> 1,
							'payment_mode' 		=> 'Online',
							'txn_id'			=> $balance_transaction,
							'tax'				=> $_POST['tax'],
							'amount'			=> $amount,
							'name' 				=> $_POST['name'],
							'email' 			=> $_POST['email'],
							'quantity'			=> 1,
							'status'			=> 1,
							'added_on'			=> date('y-m-d h:i:s'),
							'transaction_details'=> json_encode($_POST)
						);
						$result = $this->user->save('tbl_training_book',$addtrainig);
					    break;

					  case "PCE-MS":
					    $existingplandetails = $this->user->getusetdetails('professional_pce_plan','user_id',$user_id);
						$userexpiryDate = $existingplandetails->plan_expiry_at;
						$itemTaxBase = explode('_', $_REQUEST['item_number']);
						$planPcems = explode('*', $_REQUEST['item_name']);
						if($itemNumber == 1){
							$expirydate = strtotime(date("Y-m-d", strtotime($userexpiryDate)) . " +1 month");
						}
						if($itemNumber == 2){ 
							$expirydate = strtotime(date("Y-m-d", strtotime($userexpiryDate)) . " +6 month");
						}
						if($itemNumber == 3){
							$expirydate = strtotime(date("Y-m-d", strtotime($userexpiryDate)) . " +12 month");
						}
						if($itemNumber == 4){
							$expirydate = strtotime(date("Y-m-d", strtotime($userexpiryDate)) . " +36 month");
						}
						$requestupdate = array(
							'user_id' 				=> $user_id,
							'payer_email' 			=> $buyeremail,
						// 'payer_id' 				=> $_REQUEST['payer_id'],
						// 'payer_status' 			=> $_REQUEST['payer_status'],
							'first_name' 			=> $buyername,
						// 'last_name' 			=> $_REQUEST['last_name'],
							'payment_transtion_id' 	=> $balance_transaction,
							'mc_gross' 				=> $amount,
							'tax' 					=> $_POST['tax'],
							'base_price' 			=> $_POST['base_price'],
							'payment_amount' 		=> $amount,
							'payment_status' 		=> $status,
						// 'pending_reason' 		=> $_REQUEST['pending_reason'],
							'pce_plan_name' 		=> $itemName,
							'pce_plan_id' 			=> $itemNumber,
							'payment_date' 			=> $date,
						// 'verify_sign' 			=> $_REQUEST['verify_sign'],
							'payment_at' 			=> date("Y-m-d H:i:s"),
							'plan_active_date' 		=> date("Y-m-d"),
							'plan_expiry_date' 		=> date("Y-m-d",$expirydate),
						);
						$resultPCE = $this->user->save('professional_pce_plan_payment_history',$requestupdate);
						// echo $this->db->last_query(); echo $result; die;
						$lastpaymentid = $this->db->insert_id();
						if($resultPCE){
							$upateplan = array(
								'version_type' 			=> '2',
								'plan_duration' 		=> $itemName,
								'payment_status' 		=> 'y',
								'plan_active_at' 		=> date('Y-m-d'),
								'plan_expiry_at' 		=> date('Y-m-d',$expirydate),
								'payment_recieved_id' 	=> $lastpaymentid,
								'payment_recieved_at' 	=> date('Y-m-d')
							);
						$this->user->update('professional_pce_plan',$upateplan,'user_id',$user_id);
						}
						break;  

					  case "Promotion":
					    $promotedData = array(
							'user_id' 			=> $user_id,
							'role' 				=> $itemNumber,
							'promoted_date' 	=> date('Y-m-d'),
							'promoted_day' 		=> $_POST['day'],
							'tax' 			    => $_POST['tax'],
							'base_price' 		=> $_POST['base_price'],
							'promoted_amount' 	=> $amount,
							'txn_id' 			=> $balance_transaction,
							'transaction_details'=> json_encode($_POST),
						);
						$promotresult = $this->user->save('tbl_promoted_provider_transaction',$promotedData);
						$featuredData['featured_from'] = date('Y-m-d');
						$featuredData['featured_to'] = date('Y-m-d', strtotime(date('Y-m-d') . '+ '.$_POST['day'].'days'));
						$result = $this->user->update('tbl_user',$featuredData,'id',$user_id);
					    break;  

					  case "Course Promotion":
						$addpromotion = array(
							'user_id' 		=> $user_id,
							// 'plan_name'    	=> $_REQUEST['item_name'],
							'item_name'    	=> $itemName,
							'txn_id'        => $balance_transaction,
							'txn_status'    => $status,
							'status'        => 1,
							'added_on'      => date('Y-m-d'),
							'no_of_day' 	=> $_POST['day'],
							'tax'        	=> $_POST['tax'],
							'base_price'    => $_POST['base_price'],
							'amount'        => $amount,
							'course_id'     => $itemNumber,
							'transaction_details'=> json_encode($_POST),
						);
						$result = $this->user->save('tbl_course_promotion',$addpromotion);
						$featuredData['featured_from'] = date('Y-m-d');
						$featuredData['featured_to'] = date('Y-m-d', strtotime(date('Y-m-d') . '+ '.$_POST['day'].'days'));
						$uresult = $this->user->update('tbl_user',$featuredData,'id',$user_id);
					    break;

					  case "Training Promotion":
					    $training = $this->db->get_where('tbl_training',array('id'=>$itemNumber))->row_array();
						$addpromotion = array(
							'user_id' 		=> $user_id,
							'training_types'=> $training['training_type'],
							'item_name'    	=> $itemName,
							'txn_id'        => $balance_transaction,
							'txn_status'    => $status,
							'status'        => 1,
							'added_on'      => date('Y-m-d'),
							'no_of_day' 	=> $_POST['day'],
							'tax'        	=> $_POST['tax'],
							'base_price'    => $_POST['base_price'],
							'amount'        => $amount,
							'training_id'     => $itemNumber,
							'transaction_details'=> json_encode($_POST),
						);
						$result = $this->user->save('tbl_training_promotion',$addpromotion);
						$featuredData['featured_from'] = date('Y-m-d');
						$featuredData['featured_to'] = date('Y-m-d', strtotime(date('Y-m-d') . '+ '.$_POST['day'].'days'));
						$uresult = $this->user->update('tbl_user',$featuredData,'id',$user_id);
					    break;

					case "Training Publish":
						// echo '<pre>'; print_r($_POST);die;
						$base_price = $_POST['amount'] - $_POST['tax'];
						$addtrainigpublish = array(
							'user_id'			=> $_POST['user_id'],
							'training_id' 		=> $_POST['item_number'],
							'training_types' 	=> 1,
							'tax'				=> $_POST['tax'],
							'base_price'		=> $base_price,
							'amount'			=> $_POST['amount'],
							'txn_id'			=> $balance_transaction,
							'txn_status'		=> $status,
							'status'			=> 1,
							'txn_type'			=> 'cart',
							'transaction_details'=> json_encode($_POST),
							'added_on'			=> date('Y-m-d')
							
						);
						//print_r($addtrainigpublish);exit;
						$lastId = $this->user->save('tbl_training_published',$addtrainigpublish);
						//echo $result;exit;
						$trainingDetails = $this->db->get_where('tbl_training_published',array('id'=>$lastId))->row_array(); 
						$tid = $trainingDetails['training_id'];
		
						$data1['status'] = 2;
						$result = $this->user->update('tbl_training',$data1,'id',$tid);
						break;

					  case "Certificate Issued":
					  $num_of_participants = count(explode(',',$_POST['day']));
					    $datas['user_id']       = $user_id;
					    $datas['training_id']   = $itemNumber;
					    $datas['participate_cust_ids'] = $_POST['day'];
					    $datas['num_of_participants'] = $num_of_participants;
					    $datas['tax'] 			= $_POST['tax'];
					    $datas['base_price'] 	= $_POST['base_price'];
					    $datas['amount']        = $amount;
					    $datas['txn_id']        = $balance_transaction;
					    $datas['status']        = 1;
					    $datas['added_on']      = date('Y-m-d');
					    $datas['transaction_details'] = json_encode($_POST);
					    $datas['payment_status'] = 1;
					    $result = $this->user->save('tbl_training_certificate',$datas);
					    break;

					  case "Staff":
						$taxStaff_id = explode('_',$_REQUEST['custom']);
						$num_of_prof = count(explode(',',$_REQUEST['item_number']));
						$add = array(
							'provider_id' 			=> $uid,
							'prof_id'   			=> $_REQUEST['item_number'],
							'num_of_prof'   		=> $num_of_prof,
							'staff_id'     			=> $taxStaff_id[1],
							'tax'     				=> $taxStaff_id[0],
							'amount'        		=> $_REQUEST['payment_gross'],
							'transaction_details'	=> json_encode($_REQUEST),
							'added_on'      		=> date('Y-m-d H:i:s'),
						);
						$result = $this->user->save('tbl_institution_staff_payment',$add);
				
						if($result){
							// $staffIds = $_REQUEST['item_name'];
							$staffIds = $taxStaff_id[1];
							$staffIdsArr = explode(',', $staffIds);
							// print_r($staffIdsArr);die;
							$count = count($staffIdsArr);
				
							for($i=0; $i<$count; $i++){
								$stid = $staffIdsArr[$i];
								$data2['activated'] 	= 1;
								$this->user->update('tbl_institution_staff',$data2,'id',$stid);
							}
						}
					    break;

					  case "green":
					    echo "Your favorite color is green!";
					    break;

					  // default:
					  //   echo "Your favorite color is neither red, blue, nor green!";
					}
			 
				//need to add one emailer which goes to user email id and Ceonpoint's notication. 
				
					if( $insert_id > 0 && $status == 'succeeded'){
						$data['insertID'] = $insert_id;

						if($type == "Certificate Issued"){
							$this->session->set_flashdata('response-res', '<div style="margin-left:-1px;" class="alert alert-success alert-dismissable">Payment has been completed successfully.</div>');
							redirect('provider/template/'.$itemNumber.'/'.$result.'');
						}elseif($type == "Staff"){
							$this->session->set_flashdata('response-res', '<div style="margin-left:-1px;" class="alert alert-success alert-dismissable">Staff Payment successfully Done.</div>');
							redirect('provider/staffpayment?go='.$result.'');
						}else{
						$this->load->view('template/header_home');	
						$this->load->view('stripe/payment_success', $data);
						$this->load->view('template/footer_home');	
						}
						// redirect('Welcome/payment_success','refresh');
					}else{	
						$this->session->set_flashdata('response',"Transaction has been failed");
						redirect('stripe/payment_error');
					}
				}else{
					$this->session->set_flashdata('response',"not inserted. Transaction has been failed");
					redirect('stripe/payment_error');
				}

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

	public function create_stripe_session(){

		// require_once APPPATH."third_party/stripe/init.php";
		require_once APPPATH."third_party/stripe-php-13.4.0-beta.1/init.php";
		// Set your secret key. Remember to switch to your live secret key in production.
		// See your keys here: https://dashboard.stripe.com/apikeys
		// \Stripe\Stripe::setApiKey(STRIPE_PUBLISHABLE_KEY);

		$stripe = new \Stripe\StripeClient(STRIPE_SECRET_KEY);
		// $stripe->checkout->sessions->create([
		// 'success_url' => base_url('stripe/success').'?session_id=zx123qwerty',
		// 'line_items' => [
		// 	[
		// 	'price' => '100',
		// 	'quantity' => 2,
		// 	],
		// ],
		// 'mode' => 'payment',
		// ]);
		
		$stripe->checkout->sessions->create([
		'payment_method_types' => ['card'],
		'mode' => 'setup',
		// 'customer' => '12',
		'success_url' => base_url('stripe/success').'?session_id=zx123qwerty',
		'cancel_url' => base_url('stripe/cancel'),
		]);

		// 303 redirect to $session->url
	}
} ?>
