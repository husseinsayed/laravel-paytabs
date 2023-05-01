<?php
namespace Husseinsayed\Paytabs;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

class Paytabs
{
	private $paytabs_profile_id;
	private $paytabs_base_url;
	private $paytabs_server_key;
	private $paytabs_checkout_lang;
	private $paytabs_currency;
	private $verify_route_name;

	public function __construct()
	{
		$this->paytabs_profile_id = config('paytabs.PAYTABS_PROFILE_ID');
		$this->paytabs_base_url = config('paytabs.PAYTABS_BASE_URL');
		$this->paytabs_server_key = config('paytabs.PAYTABS_SERVER_KEY');
		$this->paytabs_checkout_lang = config('paytabs.PAYTABS_CHECKOUT_LANG');
		$this->paytabs_currency = config('paytabs.PAYTABS_CURRENCY');
		$this->verify_route_name = config('paytabs.VERIFY_ROUTE_NAME');
	}

	function pay($amount, $user_id = null, $user_name = null, $user_email = null, $user_phone = null, $values = [])
	{
		$unique_id = uniqid();

		$data = [
			'profile_id' => $this->paytabs_profile_id,
			"tran_type" => "sale",
			"tran_class" => "ecom",
			"cart_id" => $unique_id,
			"cart_currency" => $this->paytabs_currency,
			"cart_amount" => $amount,
			"hide_shipping" => true,
			"cart_description" => "items",
			"paypage_lang" => $this->paytabs_checkout_lang,
			"callback" => Route::has($this->verify_route_name) ? route($this->verify_route_name, ['payment_ref' => $unique_id]) : "http://localhost?customer_ref=" . $unique_id,
			"return" => Route::has($this->verify_route_name) ? route($this->verify_route_name, ['payment_ref' => $unique_id]) : "http://localhost?customer_ref=" . $unique_id,
			"customer_ref" => strval($user_id ? $user_id : $unique_id), //convert to string
			"customer_details" => [
				"name" => $user_name,
				"email" => $user_email,
				"phone" => $user_phone,
				"street1" => "Not Available Data",
				"city" => "Not Available Data",
				"state" => "Not Available Data",
				"country" => "Not Available Data",
				"zip" => "00000"
			],
			'valu_down_payment' => "0",
			"tokenise" => 1
		];

		if (isset($values['customer_details'])) {
			$data['customer_details'] = array_merge($data['customer_details'], $values['customer_details']);
			unset($values['customer_details']);
		}
		$data = array_merge($data, $values);

		$response = Http::withHeaders([
			'Authorization' => $this->paytabs_server_key,
			'Content-Type' => "application/json"
		])->post($this->paytabs_base_url . "/payment/request", $data);

		return [
			'uuid'			=> $unique_id,
			'payment_id'	=> $response['tran_ref'] ?? NULL,
			'redirect_url'	=> $response['redirect_url'] ?? NULL,
			'html'			=> '',
		];
	}

	// Check this link https://support.paytabs.com/en/support/solutions/articles/60000709842-3-3-4-pt2-api-endpoints-token-based-transactions-recurring-payments

	function payRecurring($amount, $user_id = null, $user_name = null, $user_email = null, $user_phone = null, $values = [],$tokenise=1,$tran_class='ecom',$recurring_token=null,$recurring_tranRef=null)
	{
		$unique_id = uniqid();

		$data = [
			'profile_id' => $this->paytabs_profile_id,
			"tran_type" => "sale",
			"tran_class" => $tran_class,
			"cart_id" => $unique_id,
			"cart_currency" => $this->paytabs_currency,
			"cart_amount" => $amount,
			"hide_shipping" => true,
			"cart_description" => "items",
			"paypage_lang" => $this->paytabs_checkout_lang,
			"callback" => Route::has($this->verify_route_name) ? route($this->verify_route_name, ['payment_ref' => $unique_id]) : "http://localhost?customer_ref=" . $unique_id,
			"return" => Route::has($this->verify_route_name) ? route($this->verify_route_name, ['payment_ref' => $unique_id]) : "http://localhost?customer_ref=" . $unique_id,
			"customer_ref" => strval($user_id ? $user_id : $unique_id), //convert to string
			"customer_details" => [
				"name" => $user_name,
				"email" => $user_email,
				"phone" => $user_phone,
				"street1" => "Not Available Data",
				"city" => "Not Available Data",
				"state" => "Not Available Data",
				"country" => "Not Available Data",
				"zip" => "00000"
			],
			'valu_down_payment' => "0",
			"tokenise" => $tokenise
		];
		if($tran_class=='recurring'){
			$data['token'] =$recurring_token;
			$data['tran_ref'] =$recurring_tranRef;
		}

		if (isset($values['customer_details'])) {
			$data['customer_details'] = array_merge($data['customer_details'], $values['customer_details']);
			unset($values['customer_details']);
		}
		$data = array_merge($data, $values);

		$response = Http::withHeaders([
			'Authorization' => $this->paytabs_server_key,
			'Content-Type' => "application/json"
		])->post($this->paytabs_base_url . "/payment/request", $data);

		return [
			'uuid'			=> $unique_id,
			'payment_id'	=> $response['tran_ref'] ?? NULL,
			'redirect_url'	=> $response['redirect_url'] ?? NULL,
			'html'			=> '',
		];
	}


	function verify($tran_ref)
	{
		$response = Http::withHeaders([
			'Authorization' => $this->paytabs_server_key,
			'Content-Type' => "application/json"
		])->post($this->paytabs_base_url . "/payment/query", [
			'profile_id' => $this->paytabs_profile_id,
			'tran_ref' => $tran_ref
		])->json();

		if (isset($response['payment_result']['response_status']) && $response['payment_result']['response_status'] == "A") {
			return [
				'success' => true,
				'process_data' => $response
			];
		} else {
			return [
				'success' => false,
				'process_data' => $response
			];
		}
	}

	private function associativeMerge(iterable $base, iterable $addition): iterable
	{
		foreach ($addition as $key => $sub)

			if (!array_key_exists($key, $base))
				$base[$key] = $sub;
			elseif (is_array($base[$key]) && is_array($addition[$key]))
				$base[$key] = associativeMerge($base[$key], $addition[$key]);

		return $base;
	}
}
