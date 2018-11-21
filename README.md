# RightMorsel API

This application provides an API to retrieve, add, edit and delete [HMC approved](https://halalhmc.org/) restaurants.

### Prerequisites
* PHP 5.5.9 or higher
* Composer
### Installing
Run the following command to install project dependencies.
```
composer install
```

### Set up database
Database name and host will be configured when the ```composer install``` runs.

1) Create database with the following command (should not be used on production):

```php bin/console doctrine:database:create``` 

2) Create the database tables (based on Doctrine entity classes) with the following command (shoult not be used on production):

```php bin/console doctrine:schema:create```

3) To populate the database with initial outlet:

```php bin/console outlet:retrieve "https://halalhmc.org/outlets/?cat=restaurants-and-takeaways&distance=25&q=London%2C+UK&lat=&lang="```

## Tests
PHPUnit tests can be run by the following command:
```
./vendor/bin/simple-phpunit
```

## Endpoints
* /api/v1/outlets/nearest/{longitude}/{latitude} - (GET request) Returns the nearest outlets to a given geocode

## Built with
* [Symfony](https://symfony.com/doc/3.4//index.html) The web framework used.
* [Doctrine](https://www.doctrine-project.org/projects/doctrine-orm/en/2.5/index.html) ORM layer.
* [Gouette](https://github.com/FriendsOfPHP/Goutte) Used to retrieve outlets from HMC websites.
* [BazingaGeocoderBundle](https://github.com/geocoder-php/BazingaGeocoderBundle) Used to Geocode postcodes.
* [CraueGeoBundle](https://github.com/craue/CraueGeoBundle) Used to calculate distance between postcodes or geocodes.

## Author
Saeed Bhuta
