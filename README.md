# JSON Web Token Session Adapter for Lithium

## Installation

### Composer

```
composer require jasonroyle/li3_jwt
```

### Git

```
git submodule add https://github.com/jasonroyle/li3_jwt.git libraries/li3_jwt
```

## Enable the Library

Make the application aware of the library by adding the following to `app/config/bootstrap/libraries.php`.

```php
Libraries::add('li3_jwt');
```
