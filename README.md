# Laravel GeeTest Captcha - The Best Alternative to Google reCAPTCHA

[![Latest Version on Packagist](https://img.shields.io/packagist/v/salahhusa9/laravel-geetest-captcha.svg?style=flat-square)](https://packagist.org/packages/salahhusa9/laravel-geetest-captcha)
![Laravel](https://img.shields.io/badge/Laravel-6%7C7%7C8%7C9%7C10%7C11%7CLatest-red)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/salahhusa9/laravel-geetest-captcha/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/salahhusa9/laravel-geetest-captcha/actions?query=workflow%3Arun-tests+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/salahhusa9/laravel-geetest-captcha.svg?style=flat-square)](https://packagist.org/packages/salahhusa9/laravel-geetest-captcha)

Laravel GeeTest Captcha is a comprehensive package that provides seamless integration of GeeTest Captcha in your Laravel applications. Compatible with Laravel 6 through the latest version.

![GeeTest Captcha Demo](https://github.com/user-attachments/assets/24c38b93-817a-43ec-bc30-9cf54736b346)

## Features

- ✅ **Multi-Laravel Support**: Compatible with Laravel 6.x to 11.x and latest
- ✅ **Easy Integration**: Simple Blade directives for quick setup
- ✅ **Flexible Validation**: Multiple validation methods (Rules, Middleware)
- ✅ **Robust Error Handling**: Comprehensive error handling and logging
- ✅ **Configuration**: Flexible configuration options
- ✅ **Modern HTTP Client**: Uses Guzzle for reliable API requests
- ✅ **Artisan Commands**: Installation and management commands

## Requirements

- PHP 7.4 or higher
- Laravel 6.0 or higher
- GuzzleHTTP 6.5 or 7.x

## Installation

### 1. Install via Composer

```bash
composer require salahhusa9/laravel-geetest-captcha
```

### 2. Publish Configuration (Optional)

```bash
php artisan geetest:install --config
```

Or manually publish:

```bash
php artisan vendor:publish --tag=geetest-captcha-config
```

### 3. Configure Environment Variables

Add your GeeTest credentials to your `.env` file:

```env
GEETEST_ID=your_captcha_id_here
GEETEST_KEY=your_captcha_key_here

# Optional configurations
GEETEST_API_SERVER=http://gcaptcha4.geetest.com
GEETEST_TIMEOUT=5
GEETEST_JS_URL=https://static.geetest.com/v4/gt4.js
```

### 4. Get GeeTest Credentials

1. Sign up at [GeeTest Official Website](https://geetest.com)
2. Create a new application in your dashboard
3. Copy your Captcha ID and Private Key

## Usage

### Basic Setup

#### 1. Add Assets to Layout

Add the GeeTest JavaScript assets to your main layout file's `<head>` section:

```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your App</title>
    
    @geetestCaptchaAssets()
</head>
<body>
    <!-- Your content -->
</body>
</html>
```

#### 2. Add Captcha to Forms

Add the captcha to your forms and initialize it:

```html
<form method="POST" action="{{ route('login') }}">
    @csrf
    
    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" name="email" id="email" class="form-control" required>
    </div>
    
    <div class="form-group">
        <label for="password">Password</label>
        <input type="password" name="password" id="password" class="form-control" required>
    </div>
    
    <div class="form-group">
        <div id="captcha-container">
            <!-- GeeTest Captcha will be rendered here -->
        </div>
        @error('geetest_captcha')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>
    
    <button type="submit" class="btn btn-primary">Login</button>
</form>

@geetestCaptchaInit('captcha-container')
```

### Validation Methods

#### Method 1: Using Validation Rules

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Controller;
use Salahhusa9\GeetestCaptcha\Rules\GeetestCaptchaValidate;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
            'geetest_captcha' => ['required', new GeetestCaptchaValidate]
        ]);

        // Your authentication logic here
        // The captcha is valid if we reach this point
    }
}
```

#### Method 2: Using Middleware

First, register the middleware in your `app/Http/Kernel.php`:

```php
protected $routeMiddleware = [
    // ... other middleware
    'geetest.captcha' => \Salahhusa9\GeetestCaptcha\Http\Middleware\ValidateGeetestCaptcha::class,
];
```

Then apply it to your routes:

```php
use Salahhusa9\GeetestCaptcha\Http\Middleware\ValidateGeetestCaptcha;

// Using route middleware
Route::post('/login', [AuthController::class, 'login'])
    ->middleware('geetest.captcha');

// Or directly in the route definition
Route::post('/contact', [ContactController::class, 'store'])
    ->middleware(ValidateGeetestCaptcha::class);
```

#### Method 3: Manual Validation

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Salahhusa9\GeetestCaptcha\Facades\GeetestCaptcha;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        $captchaValue = $request->input('geetest_captcha');
        
        if (!GeetestCaptcha::validate($captchaValue)) {
            return back()->withErrors([
                'geetest_captcha' => 'Captcha validation failed.'
            ]);
        }

        // Get validation details (optional)
        $validationData = GeetestCaptcha::getValidatedData();
        
        // Your logic here
    }
}
```

### Advanced Configuration

#### Custom Configuration

You can override the default configuration by publishing the config file and modifying `config/geetest-captcha.php`:

```php
<?php

return [
    'captcha_id' => env('GEETEST_ID'),
    'captcha_key' => env('GEETEST_KEY'),
    'api_server' => env('GEETEST_API_SERVER', 'http://gcaptcha4.geetest.com'),
    'timeout' => env('GEETEST_TIMEOUT', 5),
    'js_url' => env('GEETEST_JS_URL', 'https://static.geetest.com/v4/gt4.js'),
];
```

#### Dynamic Configuration

```php
use Salahhusa9\GeetestCaptcha\GeetestCaptcha;

$captcha = new GeetestCaptcha();
$captcha->setConfig('your-id', 'your-key', 'custom-api-server');

$isValid = $captcha->validate($captchaValue);
```

## API Reference

### GeetestCaptcha Class Methods

#### `validate($value)`
Validates the captcha response from the frontend.

**Parameters:**
- `$value` (string): JSON string containing captcha validation data

**Returns:** `bool` - True if validation succeeds, false otherwise

#### `getValidatedData()`
Returns the full validation response from GeeTest API.

**Returns:** `array|null` - Validation response data

#### `setConfig($captcha_id, $captcha_key, $api_server = null)`
Dynamically sets configuration.

**Parameters:**
- `$captcha_id` (string): GeeTest Captcha ID
- `$captcha_key` (string): GeeTest Private Key
- `$api_server` (string, optional): Custom API server URL

**Returns:** `$this` - For method chaining

### Blade Directives

#### `@geetestCaptchaAssets()`
Includes the GeeTest JavaScript library.

#### `@geetestCaptchaInit('element-id')`
Initializes the captcha on the specified element.

**Parameters:**
- `element-id` (string): The ID of the HTML element where the captcha will be rendered

## Error Handling

The package includes comprehensive error handling:

- **Network Issues**: Graceful fallback when API is unreachable
- **Invalid Responses**: Proper handling of malformed API responses
- **Missing Configuration**: Clear error messages for missing credentials
- **Logging**: Automatic error logging for debugging

## Testing

```bash
composer test
```

## Laravel Version Compatibility

| Laravel Version | Package Version | PHP Version |
|-----------------|-----------------|-------------|
| 6.x             | ^1.0           | 7.4+        |
| 7.x             | ^1.0           | 7.4+        |
| 8.x             | ^1.0           | 7.4+        |
| 9.x             | ^1.0           | 8.0+        |
| 10.x            | ^1.0           | 8.1+        |
| 11.x            | ^1.0           | 8.2+        |

## Troubleshooting

### Common Issues

1. **Captcha not loading**
   - Verify your GeeTest credentials in `.env`
   - Check if `@geetestCaptchaAssets()` is included in your layout
   - Ensure there are no JavaScript console errors

2. **Validation always fails**
   - Confirm your GeeTest private key is correct
   - Check network connectivity to GeeTest API
   - Review application logs for detailed error messages

3. **Multiple captchas on same page**
   - Use unique element IDs for each captcha
   - Initialize each captcha separately

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on recent changes.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [salahhusa9](https://github.com/salahhusa9)
- [GeeTest](https://www.geetest.com/en/)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
