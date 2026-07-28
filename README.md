# SMSPro - Bulk SMS Platform

[![Latest Version on Packagist](https://img.shields.io/packagist/v/softeriatech/smspro.svg?style=flat-square)](https://packagist.org/packages/softeriatech/smspro)
[![Total Downloads](https://img.shields.io/packagist/dt/softeriatech/smspro.svg?style=flat-square)](https://packagist.org/packages/softeriatech/smspro)
[![License](https://img.shields.io/packagist/l/softeriatech/smspro.svg?style=flat-square)](https://packagist.org/packages/softeriatech/smspro)

A Laravel package for SMSPro Bulk SMS platform by Softeria Tech.

## Installation

`bash
composer require softeriatech/smspro
`

## Requirements

- PHP 7.4 or higher
- Laravel 8.0 or higher
- GuzzleHTTP 7.0 or higher

## Configuration

Publish the config file:

`bash
php artisan vendor:publish --provider="SofteriaTech\\SmsPro\\SmsProServiceProvider"
`

Add to your `.env` file:

```env
SMSPRO_API_KEY=your-api-key
SMSPRO_SENDER_ID=your-sender-id
SMSPRO_BASE_URL=https://sms.softeriatech.com/api/v1/bulksms
SMSPRO_DEFAULT_COUNTRY_CODE=254
SMSPRO_TIMEOUT=30 
```


## Usage

### Basic Usage

```php
use SofteriaTech\SmsPro\Facades\SmsPro;

// Send SMS to single recipient
SmsPro::send('0712509826', 'Hello, this is a test message');

// Send SMS to multiple recipients
SmsPro::send(['0712509826', '0753268299'], 'Hello everyone!');

// Send SMS with custom sender ID
SmsPro::send('0712509826', 'Hello, this is a test message', 'WEERA');

// Get account balance
$balance = SmsPro::getBalance();
echo "Balance: " . $balance['response']['credit_balance'];

// Get all sender IDs
$senderIds = SmsPro::getSenderIds();

// Get all contact groups
$groups = SmsPro::getGroups();

// Send SMS to a contact group
SmsPro::sendToGroup('1', 'Hello group message!');

// Send OTP
$result = SmsPro::sendOTP(
    '0712509826',
    'Your verification code is [otp]. Powered by sms.softeriatech.com',
    'WEERA'
);
$otp = $result['otp'];

// Verify OTP
$verified = SmsPro::verifyMobile('0712509826', $otp);

// Validate mobile number
$validated = SmsPro::validateMobile('0712509826');

// Get supported countries
$countries = SmsPro::getSupportedCountries();

// Update contact group
SmsPro::updateGroup('group_name', '0712509826,0753268299');
```

### Laravel Notifications

First, create a notification:

```bash
php artisan make:notification WelcomeSmsNotification
```

Then implement the notification:

```php
<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use SofteriaTech\SmsPro\SmsProMessage;

class WelcomeSmsNotification extends Notification
{
    protected $message;

    public function __construct($message)
    {
        $this->message = $message;
    }

    public function via($notifiable)
    {
        return ['smspro'];
    }

    public function toSmsPro($notifiable)
    {
        return SmsProMessage::create($this->message)
            ->from('WEERA')
            ->to($notifiable->phone);
    }
}
```

Send the notification:

```php
use App\Notifications\WelcomeSmsNotification;
use Illuminate\Support\Facades\Notification;

$user = User::find(1);
Notification::send($user, new WelcomeSmsNotification('Welcome to our platform!'));
```

### Using the Message Class

```php
use SofteriaTech\SmsPro\SmsProMessage;

$message = SmsProMessage::create('Your order has been confirmed')
    ->from('WEERA')
    ->to('0712509826');

// Or send to a group
$message = SmsProMessage::create('Flash sale starts now!')
    ->from('WEERA')
    ->toGroup('1');
```

### Model with Route Notification

Add the `Notifiable` trait to your model and implement the route method:

```php
<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * Route notifications for the SMSPro channel.
     */
    public function routeNotificationForSmsPro()
    {
        return $this->phone; // or $this->mobile, $this->phone_number
    }
}
```

## Error Handling

All methods throw `SmsProException` on failure:

```php
use SofteriaTech\SmsPro\Exceptions\SmsProException;

try {
    SmsPro::send('0712509826', 'Test message');
} catch (SmsProException $e) {
    echo "Error: " . $e->getMessage();
    echo "Code: " . $e->getCode();
}
```

## API Reference

### SmsPro Facade Methods

| Method | Description | Parameters |
|--------|-------------|------------|
| `send()` | Send SMS to one or multiple recipients | `$mobiles` (string\|array), `$message` (string), `$senderId` (string\|null) |
| `sendToGroup()` | Send SMS to a contact group | `$groupId` (string), `$message` (string), `$senderId` (string\|null) |
| `getBalance()` | Get account balance | None |
| `getSenderIds()` | Get all registered sender IDs | None |
| `getGroups()` | Get all contact groups | None |
| `getGroup()` | Get a specific contact group | `$groupId` (string) |
| `updateGroup()` | Update a contact group | `$name` (string), `$contacts` (string) |
| `getSupportedCountries()` | Get supported countries | None |
| `sendOTP()` | Send OTP to a mobile number | `$mobile` (string), `$template` (string), `$senderId` (string\|null) |
| `verifyMobile()` | Verify OTP code | `$mobile` (string), `$code` (string) |
| `validateMobile()` | Validate mobile number format | `$mobile` (string) |
| `getLastResponse()` | Get last API response | None |

### SmsProMessage Methods

| Method | Description |
|--------|-------------|
| `create()` | Create a new message instance |
| `to()` | Set the recipient mobile number |
| `content()` | Set the message content |
| `from()` | Set the sender ID |
| `toGroup()` | Send to a contact group |

## Response Structure

### Success Response
```json
{
    "status": true,
    "response": {
        // API response data
    }
}
```

### Error Response
```json
{
    "status": false,
    "response": {
        "success": false,
        "msg": "Error message"
    }
}
```

## Testing

Run the tests:

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email security@softeriatech.com instead of using the issue tracker.

## Credits

- [Softeria Tech](https://softeriatech.com)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Support

For support, email info@softeriatech.com or visit https://sms.softeriatech.com