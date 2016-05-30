## Core

The main package used by most Nodes packages.

[![Total downloads](https://img.shields.io/packagist/dt/nodes/core.svg)](https://packagist.org/packages/nodes/core)
[![Monthly downloads](https://img.shields.io/packagist/dm/nodes/core.svg)](https://packagist.org/packages/nodes/core)
[![Latest release](https://img.shields.io/packagist/v/nodes/core.svg)](https://packagist.org/packages/nodes/core)
[![Open issues](https://img.shields.io/github/issues/nodes-php/core.svg)](https://github.com/nodes-php/core/issues)
[![License](https://img.shields.io/packagist/l/nodes/core.svg)](https://packagist.org/packages/nodes/core)
[![Star repository on GitHub](https://img.shields.io/github/stars/nodes-php/core.svg?style=social&label=Star)](https://github.com/nodes-php/core/stargazers)
[![Watch repository on GitHub](https://img.shields.io/github/watchers/nodes-php/core.svg?style=social&label=Watch)](https://github.com/nodes-php/core/watchers)
[![Fork repository on GitHub](https://img.shields.io/github/forks/nodes-php/core.svg?style=social&label=Fork)](https://github.com/nodes-php/core/network)

## 📝 Introduction
This package is what we in Nodes call the "Core" package. It consists of a lot of helpful methods, which makes it easier to develop other packages and projects in general.

The most important thing about this package, is that it contains a modified version of the default `Exception`. We've tweaked it a little bit, to add support for custom HTTP status codes and messages.
These custom HTTP status code are used in all of our projects to return project specific error codes to our mobile developers.

Therefore you will experience that a lot of the Nodes packages will have this core package as a required dependency, since it either utilizes the custom `Exception` or any of our helper methods.

Last but not least, this package also contains the package [Browscap](https://github.com/browscap/browscap-php), which makes it easier to parse user-agents, which is quite handy when used with services like [Bugsnag](http://bugsnag.com).

## 📦 Installation

To install this package you will need:

* Laravel 5.1+
* PHP 5.5.9+

You must then modify your `composer.json` file and run `composer update` to include the latest version of the package in your project.

```
"require": {
    "nodes/core": "^1.0"
}
```

Or you can run the composer require command from your terminal.

```
composer require nodes/core
```

## 🔧 Setup

Setup alias in `config/app.php`

```
'NodesUserAgent' => Nodes\Support\Facades\UserAgent::class,
```

Publish config files

```
php artisan vendor:publish --provider="Nodes\Core\ServiceProvider"
```

If you want to overwrite any existing config files use the `--force` paramter

```
php artisan vendor:publish --provider="Nodes\Core\ServiceProvider" --force
```

## 🏆 Credits

This package is developed and maintained by the PHP team at [Nodes](http://nodesagency.com)

[![Follow Nodes PHP on Twitter](https://img.shields.io/twitter/follow/nodesphp.svg?style=social)](https://twitter.com/nodesphp) [![Tweet Nodes PHP](https://img.shields.io/twitter/url/http/nodesphp.svg?style=social)](https://twitter.com/nodesphp)

## 📄 License

This package is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)