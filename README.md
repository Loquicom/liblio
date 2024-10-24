# Liblio

Liblio is an easy-to-use library management system (SIGB in French).

## CodeIgniter

The application is written in PHP using the CodeIgniter 4 framework.

CodeIgniter is a PHP full-stack web framework that is light, fast, flexible and secure.
More information can be found at the [official site](https://codeigniter.com).

You can read the [user guide](https://codeigniter.com/user_guide/)
corresponding to the latest version of the framework.

## Setup

Copy the application files to the server 
(:warn: note that the application's public folder must be the root folder of your website).

Copy `env` to `.env` and tailor for your app, specifically the baseURL
and any database settings.

Run the following commands from the application root:

```bash
composer install --no-dev
php spark migrate --all
```

It's all good, the default account is admin/admin (don't forget to change the password in the application)

## Server Requirements

PHP version 8.1 or higher is required, with the following extensions installed:

- [intl](http://php.net/manual/en/intl.requirements.php)
- [mbstring](http://php.net/manual/en/mbstring.installation.php)

> [!WARNING]
> - The end of life date for PHP 7.4 was November 28, 2022.
> - The end of life date for PHP 8.0 was November 26, 2023.
> - If you are still using PHP 7.4 or 8.0, you should upgrade immediately.
> - The end of life date for PHP 8.1 will be December 31, 2025.

Additionally, make sure that the following extensions are enabled in your PHP:

- json (enabled by default - don't turn it off)
- [mysqlnd](http://php.net/manual/en/mysqlnd.install.php) if you plan to use MySQL
- [libcurl](http://php.net/manual/en/curl.requirements.php) if you plan to use the HTTP\CURLRequest library

See CodeIgniter documentation for more information.

## Planned improvements

Next update:
  - Tag system for books (with search)
  - Automatic email system (reminder to return a book, for example) and ability to send emails from the application for managers
  - Be able to configure the app's basic parameters from the site (name, no. of borrowing days, etc.)
  - CSV export
  - Customizable data purges

Maybe one day:
  - Better simple search
  - Alert if a book is borrowed more than its number of copies
  - Authentication system with code sent by email to allow users to view their full profile
