# Webshop Package

This is a Laravel 10 package that provides you with an API to handle a simple webshop powered by Stripe.

[Check out the official Laravel docs](https://laravel.com/docs/10.x/packages)

## Development

First of all clone the repository in a directory next to your `climbing-buddies-backend` directory with the following command:
```sh
cd ~/Code
git clone git@github.com:RawFocus/webshop.git package-webshop
```

Until we have a stable v1.0.0 we will be working in the `webshop` branch of the `climbing-buddies-backend` repository. So checkout that branch and it should be configured for loading the package locally:
```sh
cd ../climbing-buddies-backend
git fetch
git checkout webshop
```

Notice the `repositories` entry in the `composer.json` file:
```json
"repositories": [
    {
        "type": "path",
        "url": "../package-webshop",
        "options": {
            "symlink": true
        }
    }
]
```

Since we are running Laravel sail (aka Docker) the package won't be automatically available within our docker environment. To make the package directory available we add the following to the `volumes` section of the `docker-compose.yaml` of the `climbing-buddies-backend` project, like so:
```
volumes:
    - '.:/var/www/html'
    - '../package-webshop:/var/www/package-webshop'
```

Afterwards run the following command to symlink the package into the climbing buddies backend:
```sh
composer require raw/webshop:dev-master
```
The `dev-` tells composer we want to load a local package and not a tag and the `master` is the branch we want to load. 

After that the package should be loaded and you can develop it in it's own directory without having to update.

## Publishing



## Installation

After installing the package you should:
```
sail artisan migrate
sail artisan lang:publish --provider=Raw\Webshop\Providers\WebshopServiceProvider
```

## Usage