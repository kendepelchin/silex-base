#Silex base - app
Basically this is my personal repo where I always start from. The most important parts are the DoctrineServiceProvider, TwigServiceProvider and the ConsoleServiceProvider.

## Install
Install this repo using composer (https://getcomposer.org)
* Run composer install

## To do's
When cloning, make sure you create the tables needed. See [dump](dump.sql)
Add the database credentials to the bootstrap.php file

## Console
I've added some helper classes to work with console scripts.
When adding your own scripts, you can follow these steps (for example for an import script):
* Consider app/console/placeholder as the base for your own script
* Create a new file called app/console/import (with extension)
* Make sure the permissions are correct on this file
* Add your script config to Console.php (protected static $config = array())
* Create your ImportCommand class
    * Two required functions (see PlaceholderCommand)
        * Configure
            * Fetch the config
        * Execute
            * This executes your own code
* Make sure the following folders exist
    * app/logs/locks
    * app/logs/console

### Timing
I've added a simple class which monitors timers for each script.
See PlaceholderCommand on how this is used.
There is a default interval in the Console.php of 60 seconds between executing the same script. This can be overridden in the config of each script.

### Running
Now go to your terminal and execute the following commands:
* app/console/console scripts --list
    * Show a list of scripts (and if they are running or not)
* app/console/console scripts
    * This will start all existing scripts
* app/console/console scripts --kill
    * This will kill all running scripts
* app/console/console scripts import
    * This will run the import script
* app/console/console scripts import --kill
    * This will kill the import script

### Logging
All output is saved to app/logs/console/{name of script}


