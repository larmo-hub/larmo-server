# Larmo Server

[![Build Status](https://travis-ci.org/larmo-hub/larmo-server.svg?branch=master)](https://travis-ci.org/larmo-hub/larmo-server)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/larmo-hub/larmo-server/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/larmo-hub/larmo-server/?branch=master)
[![Code Climate](https://codeclimate.com/github/larmo-hub/larmo-server/badges/gpa.svg)](https://codeclimate.com/github/larmo-hub/larmo-server)
[![Test Coverage](https://codeclimate.com/github/larmo-hub/larmo-server/badges/coverage.svg)](https://codeclimate.com/github/larmo-hub/larmo-server/coverage)

## What is it?

This project is a PoC of a central hub that stores information from many data feeds - control version systems, Skype, IRC, etc. - in order to have a clear project history with ability to search and analyse and sending out aggregated information to different media: email, IRC, Skype, etc.

## How that works?

Each service that is source of data has a connector or bot that's gathering data and sending it to Larmo Server in unified form. Various source filters are then processing data, labelling it, and sending it to storage service. Once data is stored, it can be searched and filtered.

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

## Incomming message JSON

```json
{
 "metadata"   : {
  "source"    : "github",
  "auth"      : {"agent" : "agent_webhooks", "auth" : "auth_key"},
  "timestamp" : 1434723490
 },
 "data": [
  {
    "type": "commit",
    "timestamp": "2015-06-22T12:45:56+02:00",
    "author": {
      "name": "Test Test",
      "email": "test@test.test",
      "login": "test"
    },
    "message": "Test Test added commit: \"Added new file\"",
    "extras": {
      "id": "8849ee0814c8ccd73f52f8f50220e4a0bee20ff5",
      "files": {
        "added": ["new-file.txt"],
        "removed": [],
        "modified": []
      },
      "message": "Added new file",
      "url": "https:\/\/github.com\/test\/test-hooks-repo\/commit\/8849ee0814c8ccd73f52f8f50220e4a0bee20ff5"
    }
  }
 ]
}
```

- Metadata
 - source : must be supported message type
 - auth info : must be valid
 - timestamp : UTC UNIX Timestamp
- Data
  - Message
    - type : *skype.new.message* - is registered by plugin
    - timestamp of message : valid UTC UNIX timestamp
    - author of message : if email exists, it must be valid
    - message string (body) : not empty
    - extras (source-specific key-value object) : validated by plugins and defined

## Agents

- whatever language/technology
- **must** send **valid** messages to HUB
- NICE TO HAVE: *easy run* options with docker/vagrant

### Official agents

- [WebHooks](https://github.com/larmo-hub/larmo-webhooks-agent)

## Extras

### GUI Resources

#### Logo
- [Preview](https://drive.google.com/file/d/0B7xvKTVPU5rBX2dNV1QxTEtJaUU/view?usp=sharing)
- [PSD File](https://drive.google.com/file/d/0B7xvKTVPU5rBUm00TDNLZDhqYXc/view?usp=sharing)

#### Hub layout
- [Preview](https://drive.google.com/file/d/0B7xvKTVPU5rBSXFYUVBkRElyTHc/view?usp=sharing)
- [PSD File](https://drive.google.com/file/d/0B7xvKTVPU5rBRTM2dHRIOXBsZG8/view?usp=sharing)

#### Github page
- [Preview](https://drive.google.com/file/d/0B7xvKTVPU5rBU0ctaEtwZ0pFR3c/view?usp=sharing)
- [PSD File](https://drive.google.com/file/d/0B7xvKTVPU5rBSGgzTHpYem9Fclk/view?usp=sharing)

#### *Nice to have* features for Hub layout
- [Preview](https://drive.google.com/file/d/0B7xvKTVPU5rBS0t2dkhyZG9XWk0/view?usp=sharing)
- [PSD File](https://drive.google.com/file/d/0B7xvKTVPU5rBbHBUcEZ4ME83Nmc/view?usp=sharing)