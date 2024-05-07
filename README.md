# Sealed-bid Auction

## Table of Contents

- [Description](#description)
  - [Strategy](#strategy)
    - [Structure design](#design-and-principles)
  - [Features](#features)
  - [Improvements](#possible-improvements)
- [Infrastructure](#infrastructure-used)
  - [Symfony Packages](#installed-symfony-packages)
- [Getting Started](#getting-started)
  - [Run using composer (recommended)](#run-using-composer-recommended)
  - [Run using docker](#run-using-docker)
    - [Next steps (important!)](#important)
- [How it works?](#how-it-works)
  - [API/UI](#apiui)
  - [PHPUnit Testing](#phpunit-testing)
  - [xDebug](#xdebug-debugger)
  - [Docker client host](#__client_host__-)
- [Troubleshooting](#troubleshooting)

## Description
Let's consider a second-price, sealed-bid auction:
* An object is for sale with a reserve price.
* We have several potential buyers, each one being able to place one or more bids.
* The buyer winning the auction is the one with the highest bid above or equal to the reserve price.
* The winning price is the highest bid price from a non-winning buyer above the reserve price (or the reserve price if none applies)

**Example**:

Consider 5 potential buyers (A, B, C, D, E) who compete to acquire an object with a reserve price set at 100 euros, bidding as follows:
* A: 2 bids of 110 and 130 euros 
* B: 0 bid 
* C: 1 bid of 125 euros 
* D: 3 bids of 105, 115, and 90 euros 
* E: 3 bids of 132, 135, and 140 euros.

The buyer E wins the auction at the price of 130 euros.

***

### Strategy
API/UI implementation with Controller that calls a Services.

An algorithm that finds the winner and winning price according to the specified rules.

#### Design and Principles
The project structure follows the **hexagonal architecture** of Application, Domain, and Infrastructure.

Design patterns used:
- Dependency Injection (*AuctionService*)
- Entities (*Auction, Bid, Buyer*)
- Exception & Logging Handling 

Design principles used:
- OOP
- SOLID

Test-driven development (TDD)
- The logic to find a winner auction and the winner price was developed after having the controller & service

### Features
The following key features are implemented

#### Good practices
* Manual logging and Exceptions catching during the service and controller process.
  * Log critical exceptions, like code errors, and return generic response messages like 'Something went wrong' to the API client to not provide details.

#### Logic
* Find a Winner Auction by highest bid amount, by filtering and sorting the bids (array).
* Find a WinningPrice Auction by the highest bid amount, from a non-winning buyer above the reserve price (or the reserve price if none applies), by removing the winner Buyer's bids, and filtering and sorting the bids (array).
* Controller and Test with the same example data as the task description is provided to validate the goal.

### Possible improvements
* Add a proper responseHandler to the Controller output

***

## Infrastructure used
* Symfony 7
* Docker
  * PHP 8.3 (w/ opcache & [xDebug](#xdebug-debugger))
  * Nginx


### Installed Symfony Packages
* **phpunit/phpunit**: testing framework for PHP
* **phpstan/phpstan**: analysis tool for PHP code, to detect and fix issues,

***

## Getting Started
Copy or rename the `.env.dist` files (for docker and symfony) to an environment variable file and edit the entries to your needs:
```
cp ./app/.env.dist ./app/.env && cp .env.dist .env
```

### Run using composer (recommended)

`composer run` commands are provided as **shortcuts**.

Use `composer run setup` to start and initialize all needed containers.

Available commands are:
```
composer run [
    setup             --- Build the docker images and run the containers in the background.
    build             --- Build the docker images.
    up                --- Run the containers in the background.
    down              --- Stop the containers.
    logs              --- Show container sys logs (php-fpm, nginx, and MariaDB).
    cache-clear       --- Execute Symfony clear cache command.
    stan              --- Execute PHPStan 'analyse' command.
    test              --- Execute PHPUnit test cases.    
    coverage          --- Execute PHPUnit test coverage.    
]
```

A folder named `var` will be created in the project root folder upon the first run. This folder includes the database files and server logs to provide help while developing.

### Run using docker
Alternatively to the use of `composer`, you can directly build & run the app by using the following docker commands:

* Use `docker compose` to start your environment.
  * Add the _param_ `-d` if you wish to run the process in the background.
  * Add the _param_ `--build` the **first time** to build the images.
  * Add the _keyword_ `down` to stop the containers.
```
# Build & up. From the project's root folder exec
docker compose up -d --build
```

#### IMPORTANT
After booting the container, run `composer install` from outside or inside the container.
```
docker exec -t php-fpm composer install
```

##### Optional

After booting the container, you can use this command to enter inside it and execute commands (the container's name is defined in the _**docker-compose.yml**_ file):
```
docker exec -it $container_name bash
```
or identify the name of it displayed under the column `NAMES` of this command output:
```
docker ps
```
There's an alias being created upon the build process, and it will allow you to execute the Symfony command directly only with `sf`. Example:
```
sf debug:router
```

***

## How it works?
You have up to 2 containers running: php-fpm + nginx.
Check the running containers by using the command: ``docker ps``
- [Symfony Web-App welcome page](http://localhost:80)


#### API/UI
Use the browser, Postman, or another CLI to perform actions on each endpoint.

The list of available endpoints can be shown by executing (target **php-fpm** container):
```
docker exec php-fpm php bin/console debug:router
```
Provided endpoints are (Example):
```
  Name                      Method    Path                         
 ------------------------- --------  ----------------------------- 
  task_auction              GET      /auction/task                    
  demo_auction              GET      /auction/demo    
```

#### PHPUnit Testing
Additionally, run all the tests available using (target **php-fpm**  container):
```
docker exec php-fpm ./vendor/bin/phpunit --verbose
```
or
```
composer test
```

***

#### xDebug debugger
xDebug (the last version) is installed and ready to use. Check the config params in `/docker/extras/xdebug.ini`
By default, these are the main critical parameters provided:
+ [mode](https://xdebug.org/docs/all_settings#mode) = coverage
+ [client_host*](https://xdebug.org/docs/all_settings#client_host) = host.docker.internal
+ [client_port](https://xdebug.org/docs/all_settings#client_port) = 9003
+ [idekey](https://xdebug.org/docs/all_settings#idekey) = PHPSTORM
+ [log_level](https://xdebug.org/docs/all_settings#log_level) = 0

Please check the [official documentation](https://xdebug.org/docs/all_settings) for more info about them.
Add the call to `xdebug_info()` from any PHP file to show the info panel.

####  __client_host__ (*)
Depending on your environment, it's **required** to add the following to the **_docker-composer.yml_** file to enable 
communication between the container and the host machine. By default, this is **ON**.
```
extra_hosts:
    - host.docker.internal:host-gateway
```
If you find it's not working after setting up your IDE, try to comment on section and change the [xDebug.ini file](/docker/extras/xdebug.ini)
accordingly.

***

## Troubleshooting
Nothing else for now!