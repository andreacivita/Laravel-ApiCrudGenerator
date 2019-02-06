# Laravel | API CRUD Generator

This Generator package provides generators of Models, Controllers, Request & Routes for a painless development. 

## INSTALL

Install the package through [Composer](https://getcomposer.org/).

Run the Composer require command from the Terminal:

```sh
composer require andreacivita/api-crud-generator --dev
```

### SETUP

Run this command from the Terminal

```sh
php artisan vendor:publish
```
Select andreacivita/api-crud-generator and setup it's complete.


## USAGE

### Managing all database
Usage of this package is very simple.

First, let's supposing I want to generate CRUD for all table in my db.

So, we run

```sh
php artisan make:crud All
```

No further options required. Your setup is complete!

### Managing a single db table

Now i suppose generation of CRUD operations of Car db table.

Run this command:

```sh
php artisan make:crud Car
```
Done! You will have Car model (located in App/Model directory), CarController, CarRequest (used for input data) and Routes (located in routes/api.php).

### OPTIONS

#### TABLE NAME
By default, DB Table's name is plural, while Model class name is singular (e.g. Table => Cars, Model => Car). 
You can change this behavior specifying the name in terminal

```sh
php artisan make:crud Car --table Car
```
This will create the same resources, but table name in model will be 'Car' (instead of default 'Cars')

#### TIMESTAMPS

By default, this packages will set all timestamps to false. You can change this doing this command:

```sh
php artisan make:crud Car --timestamps true
```

This will set 'timestamps=true' in Model class.

## ROUTING

I developed this package for myself. So, default generation of API Routes will follow this schema:
Example: i'm generating Car crud

| Route         | Method           | Operation        |
| ------------- |:----------------:| ----------------:|
| cars          | GET              | Get all cars     |
| car/{id}      | GET              | Find car by id   |
| car           | PUT              | Insert a new car |
| car/{id}      | PATCH            | Update car by id |
| car/{id}      | DELETE           | Delete car by id |

Remember that all api routes have 'api/' prefix.

## CONTRIBUTING

This package is covered by LGPL license. You are able to do whatever you want with this code.

Please feel free to fork this package and contribute by submitting a pull request to enhance the functionalities.
You can see issues or enhancement and assign task for contributing :)

**Remember that all modifications must be under the same license (or GNU GPLv3).**

## How can I thank you?

Star this repo or follow me on GitHub. And, if you want, you can share this link! :)



