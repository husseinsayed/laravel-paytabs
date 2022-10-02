# Laravel Paytabs

## Installation
Begin by installing this package through Composer. Just run following command to terminal-

```php
composer require mtgofa/laravel-paytabs
```

Once this operation completes the package will automatically be discovered for **Laravel 5.6 and above**, otherwise, the final step is to add the service provider. Open `config/app.php`, and add a new item to the providers array.
```php
'providers' => [
	...
	MTGofa\Paytabs\PaytabsServiceProvider::class,
],
```

Now add the alias.

```php
'aliases' => [
	...
	'Paytabs' => MTGofa\Paytabs\Facades\PaytabsFacade::class,
],
```
Don't forget to add your paytabs credentials into your .env file.

```bash
$ php artisan vendor:publish --provider="MTGofa\Paytabs\PaytabsServiceProvider"
```
Then fill in the credentials in `config/mtgofa-paytabs.php` file if you want instaed of env.

```php
PAYTABS_PROFILE_ID=2****
PAYTABS_SERVER_KEY=S6****6D2J-J2Z****H6K-6T2****MW
PAYTABS_CHECKOUT_LANG=en
PAYTABS_CURRENCY=EGP

VERIFY_ROUTE_NAME=payment.verify
```


## Example:
### Create Payment Page:
```php
Route::get('payment/paytabs',  function () {
	$user = auth()->user();
	$result = Paytabs::pay(10.00, $user->id, $user->name, $user->email, $user->phone, [
		'customer_details' => [
			'country' => 'EG',
			'state' => 'C'
		]
	]);
	return $result;
});
```
### Verify Payment:
```php
Route::get('payment/verify/{ref}',  function ($ref) {
	$result = Paytabs::verify($ref);
	return $result;
});
you will need to exclude your paytabs_response route from CSRF protection

```
