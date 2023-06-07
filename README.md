# Webshop Package

This is a Laravel 10 package that provides you with an API to handle a simple webshop powered by Stripe.

## Installation

#### Package

```sh
composer require raw-focus/webshop
```

#### Database

After installing the package you should migrate the package's migration files with:
```sh
php artisan migrate
```

We have defined some seeders for testing purposes which you can publish, edit and use yourselves with:
```sh
php artisan vendor:publish --tag=webshop-seeders
```

Then add the seeders to your `database\seeders\DatabaseSeeder.php` file:
```php
<?php

namespace Database\Seeders;

...

use RawFocus\Webshop\database\seeders\OrderSeeder;
use RawFocus\Webshop\database\seeders\ProductSeeder;

...

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        ...

        $this->call(ProductSeeder::class);
        $this->call(OrderSeeder::class);
    }
}
```

#### Configuration

Publish the config file with:
```sh
php artisan vendor:publish --tag=webshop-config
```

#### Language files

Publish the language files with:
```sh
php artisan vendor:publish --tag=webshop-lang
```

#### 
 
## Usage



## Publishing
