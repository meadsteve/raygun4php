Raygun4php
==========

This is a fork of the official ray gun client for php found here: [MindscapeHQ/raygun4php](https://github.com/MindscapeHQ/raygun4php/).
I am intending to keep interface of the Client object the same as for the official client so that it will be possible to switch back and forth.

## Installation

Firstly, ensure that **curl** is installed and enabled in your server's php.ini file.

### With Composer

Composer is a package management tool for PHP which automatically fetches dependencies and also supports autoloading - this is a low-impact way to get Raygun4php into your site.

1. If you use a *nix environment, [follow the instructions](http://getcomposer.org/doc/01-basic-usage.md#installation) to install Composer. Windows users can run [this installer](https://github.com/johnstevenson/composer-setup) to automatically add it to the path etc.

2. Inside your project's root directory create a composer.json file, containing:
```json
{
        "require": {
            "mead-steve/raygun4php": "1.*"
        }
}
```
3. From your shell run `php composer.phar install` (*nix) or `composer install` (Windows). This will download Raygun4Php and create the appropriate autoload data.

4. Then in a PHP file just add:
```php
require_once 'vendor/autoload.php';
```
and the library will be imported ready for use.

### Manually with Git

Clone this repository and copy src/Raygun4php into an appropriate subdirectory in your project, such as /vendor/Raygun4php. Add `requires` definitions for RaygunClient.php where you want to make a call to Send().

```php
require (dirname(dirname(__FILE__)).'/vendor/Raygun4php/RaygunClient.php');
```
## Usage

You can send both PHP errors and object-oriented exceptions to Raygun. An easy way to accomplish this is to create a file containing exception and error handlers which make calls to the appropriate Raygun4php functions. As above, import Raygun4php - if you're using Composer, just add `require_once 'vendor/autoload.php'`, or if not manually import RaygunClient.php.

Then, create handlers that look something like this:

```php
	$client = \MeadSteve\Raygun4php\RayGun::getClient("{{apikey for your application}}");

	function error_handler($errno, $errstr, $errfile, $errline ) {
		global $client;
  		$client->SendError($errno, $errstr, $errfile, $errline);
	}

	function exception_handler($exception)
	{
		global $client;
		$client->SendException($exception);
	}

	set_exception_handler('exception_handler');
	set_error_handler("error_handler");
```


Copy your application's API key from the Raygun.io dashboard, and place it in the constructor call as above (do not include the curly brackets).

If the handlers reside in their own file, just import it in every file where you'd like exceptions and errors to be sent, and they will be delivered to Raygun.io.


#### Version numbers

You can transmit the version number of your PHP project along with the message by calling `SetVersion()` on your RaygunClient after it is instantiated - this is optional but recommended as the version number is considered to be first-class data for a message.

#### User tracking

You can call $client->SetUser($user), passing in a string representing the username or email address of the current user of the calling application. This will be attached to the message and visible in the dashboard. This method is optional - if it is not called, a random identifier will be used. If you use this, and the user changes (log in/out), be sure to call it again passing in the new user (or just call $client->SetUser() to assign a new random identifier).

Note that this data is stored as a cookie. If you do not call SetUser the default is to store a random UUID to represent the user.

This feature can be used in CLI mode by calling SetUser(string) at the start of your session.

## Troubleshooting

SendError and SendException return the HTTP status code of the transaction - `echo`ing this will give you a 403 if your API key is incorrect or a 200 if everything was a success.