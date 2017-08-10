[![Build Status](https://travis-ci.org/aiolos/sodaq-one-tracker-mapping.svg?branch=master)](https://travis-ci.org/aiolos/sodaq-one-tracker-mapping)
[![License](https://img.shields.io/github/license/aiolos/sodaq-one-tracker-mapping.svg)](LICENSE.md)
[![Code Climate](https://codeclimate.com/github/aiolos/sodaq-one-tracker-mapping/badges/gpa.svg)](https://codeclimate.com/github/aiolos/sodaq-one-tracker-mapping)
[![Issue Count](https://codeclimate.com/github/aiolos/sodaq-one-tracker-mapping/badges/issue_count.svg)](https://codeclimate.com/github/aiolos/sodaq-one-tracker-mapping)
[![Test Coverage](https://codeclimate.com/github/aiolos/sodaq-one-tracker-mapping/badges/coverage.svg)](https://codeclimate.com/github/aiolos/sodaq-one-tracker-mapping/coverage)
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/0248f3aa5ecf408e832e4536c397bb73)](https://www.codacy.com/app/aiolos/sodaq-one-tracker-mapping?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=aiolos/sodaq-one-tracker-mapping&amp;utm_campaign=Badge_Grade)
[![Repository Size](https://reposs.herokuapp.com/?path=aiolos/sodaq-one-tracker-mapping)](https://github.com/ruddfawcett/reposs)

# SoDaq One Tracker Mapping

## Introduction

This is an application that can receive the payload from a [SoDaq One LoRaWan](http://support.sodaq.com/sodaq-one/loraone/) tracker. 
When a device is registered and a http-integration is added in [TheThingsNetwork.org](http://www.TheThingsNetwork.org) Console, this application
can decode the messages and show the coordinates on a map, as well as the used gateways.

## Installation

- Checkout this repository
- Run composer (see [getcomposer.org](http://getcomposer.org) for more information about composer):
```bash
$ composer install
```
- Copy `config/autoload/local.php.dist` to `config/autoload/local.php` and change credentials and key there
    - Set up the 'Authorization' header and value that you also set in the integration
    - Add Google maps API key
- Create the database tables (in the database you set in your local config) run:
```bash
$ ./vendor/bin/doctrine-module orm:schema-tool:create
```
- In the console of TheThingsNetwork, you need to add a http-integration, see https://youtu.be/Uebcq7xmI1M for more information
- The url of the endpoint to set in the integration is `[YourDomain]/api/post`
- Let the messages come in

## Todo:

- ~~Use an authorization header in the call from TheThingsNetwork~~
- ~~Move the API-key from google maps to a config variable~~
- ~~Automatically decode raw payload~~
- ~~Change marker icons~~
- ~~Change application icon~~
- ~~Pretty print the json in the requests list~~
- ~~Order the timestamps in datatables better~~
- ~~Create some unit test, especially for decoding the payloads~~
- Gateways:
    - ~~Show the used gateways on the map~~
    - Show a line to the send location
    - Save the gateways that are available in the metadata
    - Create a database relation between the tracker point and the gateway
- Only show a limited number of markers, for example using the datatables pagination
- Enable/disable gateways and tracker points in map
- Make a popup in the map to show more information about the datapoint in the map
- Prevent duplicates
- Adapt center and zoom level to the available datapoints
- Add some logging
- Only use php7
- Add phpstan (php7 required)
- More mobile friendly
- Add graphs (using highcharts)
    - Temperature
    - Voltage
    
## Used packages worth mentioning
- [Zend Framework 3](https://framework.zend.com/)
- [Doctrine ORM](http://www.doctrine-project.org/)
- [Json Schema Validation](https://github.com/justinrainbow/json-schema)
- [Datatables](https://datatables.net/)
