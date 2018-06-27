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

Create database with the following command (should not be used on production):

```php bin/console doctrine:database:create``` 

To populate the database with initial outlet:

```php bin/console outlet:retrieve "https://halalhmc.org/outlets/?cat=restaurants-and-takeaways&distance=25&q=London%2C+UK&lat=&lang="```

## Tests
PHPUnit tests can be run by the following command:
```
./vendor/bin/simple-phpunit
```

## Built with
* [Symfony](https://symfony.com/doc/3.4//index.html) The web framework used.
* [Doctrine](https://www.doctrine-project.org/projects/doctrine-orm/en/2.5/index.html) ORM layer.

## Author
Saeed Bhuta
