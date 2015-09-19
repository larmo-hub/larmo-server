# Larmo Server

[![Build Status](https://travis-ci.org/larmo-hub/larmo-server.svg?branch=master)](https://travis-ci.org/larmo-hub/larmo-server)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/larmo-hub/larmo-server/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/larmo-hub/larmo-server/?branch=master)
[![Code Climate](https://codeclimate.com/github/larmo-hub/larmo-server/badges/gpa.svg)](https://codeclimate.com/github/larmo-hub/larmo-server)
[![Test Coverage](https://codeclimate.com/github/larmo-hub/larmo-server/badges/coverage.svg)](https://codeclimate.com/github/larmo-hub/larmo-server/coverage)

## What is it?

This project is a PoC of a central hub that stores information from many data feeds - control version systems, Skype, IRC, etc. - in order to have a clear project history with ability to search and analyse and sending out aggregated information to different media: email, IRC, Skype, etc.

## Is it really working?

Yes, you can check the webapp under http://larmo.herokuapp.com/ - it's currently connected to our Github repo.

## Public API

```
POST /registerPacket
```

```
GET /latestMessages
```

```
GET /availableSources
```

## Composer scripts for *Larmo Server*

```bash
$: composer phplint
$: composer test
$: composer behat
```

## How to run *Larmo Server*

### Using Docker

Navigate from command line to *Larmo Server* directory and run:

```bash
$: cd run/
$: docker-compose up -d
```

Access to *Larmo Server*:

- [http://localhost:8181/](http://localhost:8181/)

### Using Vagrant

Setup configuration file *config/parameters.php* (if a file does not exist then create it). Content:

```php
<?php

$app['config.mongo_db'] = [
    'db_user' => '',
    'db_password' => '',
    'db_name' => 'larmo-server',
    'db_url' => '127.0.0.1',
    'db_port' => 27017,
    'db_options' => []
];
```

Navigate from command line to this directory and run:

```bash
$: vagrant up
```

Then you will access to *Larmo Server* by:

- [http://localhost:8181/](http://localhost:8181/)