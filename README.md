# SoDaq One Tracker Mapping

## Introduction

This is a very simple application that can receive the payload from a SoDaq One LoRaWan tracker. 
When a device is registered and a http-integration is added in [TheThingsNetwork.org](TheThingsNetwork.org) Console, this application
can decode the messages and show the coordinates on a map, as well as the used gateways.

This application is based on the Zend Framework 3 skeleton application.

## Installation

After a checkout of this repository, you need to run composer:

```bash
$ composer install
```

To create the database tables (in the database you set in your local config) run:
```bash
$ ./vendor/bin/doctrine-module orm:schema-tool create
```

If you don't have composer, see [getcomposer.org](http://getcomposer.org) for more information

- In the console of TheThingsNetwork, you need to add a http-integration, see https://youtu.be/Uebcq7xmI1M for more information
- The url of the endpoint to set in the integration is [YourDomain]/application/post
- Copy `config/autoload/local.php.dist` to `config/autoload/local.php`
    - Set up the 'Auth' header and value that you also set in the integration
    - Add Google maps API key
- Let the messages come in

## Todo:

- ~~Use an authorization header in the call from TheThingsNetwork~~
- ~~Move the API-key from google maps to a config variable~~
- ~~Automatically decode raw payload~~
- ~~Change marker icons~~
- ~~Change application icon~~
- ~~Pretty print the json in the requests list~~
- ~~Order the timestamps in datatables better~~
- Gateways:
    - ~~Show the used gateways on the map~~
    - Show a line to the send location
    - Save the gateways that are available in the metadata
    - Create a database relation between the tracker point and the  
- Only show a limited number of markers, for example using the datatables pagination
- Enable/disable gateways and tracker points in map
- Create some unit test, especially for decoding the payloads
- Make a popup in the map to show more information about the datapoint in the map
- Adapt center and zoom level to the available datapoints
- Add some logging
- Only use php7
- Add phpstan (php7 required)
