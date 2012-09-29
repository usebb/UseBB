**IMPORTANT: UseBB 2 got cancelled in October 2012. No further major versions are currently developed or planned. The information below may largely be out of date.**

# UseBB 2.0.0 pre-alpha installation

## Requirements

* PHP >= 5.3.0
* Doctrine DBAL 2.2
* MySQL (InnoDB), SQLite, PostgreSQL, Oracle or SQL Server
* PHPUnit 3.6 (tests only)

Doctrine DBAL and PHPUnit can be installed through PEAR. They are not 
included in the source tree.

When using MySQL, only InnoDB is supported. MyISAM - as used for years with
legacy PHP software such as UseBB 1 - is not supported due to the absence
of transactions and more.

## Installation

Access the root of the web application in the web browser. The Simple Installer
module will list the steps to install the system and perform the database
installation for the production/development environment.

Before using PHPUnit, the test database must be installed. Do so using:

	$ php index.php --env=testing --install-db

The sources can be installed on a different location. Move every file except
`index.php` and `dbConfig.php` to the new location and define the value of
`USEBB_ROOT_PATH` in `index.php`. This way, multiple forums can share one
source tree.

## Testing

Make sure the testing database has been set up. In a command line window,
go to the root UseBB directory and execute:

	$ phpunit .

This will make PHPUnit run all tests in the source tree. Passing a path to a 
test case file will only run that test case.

## More info

Please visit the [wiki at GitHub](https://github.com/usebb/UseBB/wiki) for more info, 
details, etc. The main website and forums are at [UseBB.net](http://www.usebb.net/).
