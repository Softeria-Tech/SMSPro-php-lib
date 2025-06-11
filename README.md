<p align="center">
  <a href="https://sms.softeriatech.com/" target="_blank" >
    <img alt="Smspro" src="https://sms.softeriatech.com/images/promotion.png"/>
  </a>
</p>
<p align="center">
	PHP SMS API Sending SMS via the <strong><em>SMSPRO SMS gateway</em></strong>
</p>
<p align="center">
    <a href="https://github.com/softeria-tech/smspro-php-lib" target="_blank">
        <img alt="Build Status" src="https://github.com/softeria-tech/smspro-php-lib/actions/workflows/unittest.yml/badge.svg">
    </a>
	<a href="https://codecov.io/gh/smspro/sms">
  		<img alt="smspro-badge" src="https://codecov.io/gh/smspro/sms/branch/master/graph/badge.svg" />
	</a>
</p>

Requirement
-----------

This library needs minimum requirement for doing well on run.

   - [Sign up](https://sms.softeriatech.com) for a free SMSPRO account
   - Ask SMSPRO Team for new access_key for developers
   - SMSPRO SMS API client for PHP requires version 8.1.x and above

## Installation via Composer

Package is available on [Packagist](https://packagist.org/packages/smspro/sms),
you can install it using [Composer](http://getcomposer.org).
```shell
composer require smspro/sms
```
### Or go to

   [Smspro-SMS-API-Latest Release](https://github.com/softeria-tech/smspro-php-lib/releases/tag/1.0.0)

And download the full version

If you want to install a legacy version running with `PHP7.4`
Run composer with the command below
```shell
composer require smspro/sms "1.0.*"
```
Or Download it from [Smspro-SMS-API-Legacy](https://github.com/softeria-tech/smspro-php-lib/releases/tag/1.0.0)

Quick Examples
--------------

##### Sending a SMS
```php
	$oMessage = \Smspro\Sms\Message::create('YOUR_PRO_API_KEY');
	$oMessage->from ='SenderId';
	$oMessage->to = '+254712509826,071250xxx';
	$oMessage->message ='Test sms from smspro';
	var_dump($oMessage->send());
  ```

##### Sending non customized sender SMS.
```php
   $oBalance = \Smspro\Sms\Balance::create('YOUR_API_KEY');
	$response = $oBalance->get();
	$balance = $response->getBalance();
	$currency = $response->getCurrency();
	$rate = $response->getRate(); 
	echo "Your current balance is {$balance} {$currency} at a rate of {$rate}.\n";
```


##### Sending Bulk SMS from your Script
It is obvious that sending bulk data to any system is a problem! Therefore, you should check our recommendation for the best approach
   - (_**[See example for bulk sms](https://github.com/softeria-tech/smspro-php-lib/wiki/How-to-send-Bulk-SMS-from-your-script#send-sms-sequentially)**_)

WordPress Plugin
----------------
If you are looking for a powerful WordPress plugin to send SMS, then download our [sms-pro-wp-plugin](https://github.com/Softeria-Tech/sms-pro-wp-plugin)

Resources
---------

  * [Documentation](https://github.com/softeria-tech/smspro-php-lib/wiki)
  * [Report issues](https://github.com/softeria-tech/smspro-php-lib/issues)
