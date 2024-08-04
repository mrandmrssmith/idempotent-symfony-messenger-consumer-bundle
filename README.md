# Symfony Messenger idempotent consumer bundle

Bundle for symfony messenger which provide functionality to make your consumer idempotent.

It based on `mrandmrssmith/idempotent-consumer-bundle` and provide integration with symfony messenger.

You can you package `mrandmrssmith/idempotent-consumer-doctrine-persistence-bundle` to provide persistence layer using doctrine.

It uses messenger events to handle incoming and processed or failed messages.

## Installation

Add this package to your project
```shell
composer require mrandmrssmith/idempotent-symfony-messenger-consumer-bundle
```

## Usage
By default it will try check for all messages.

If you want, you can restrict the action so that it checks only messages from a particular transport or messages that are instances of a class/interface

To do this you need overwrite value of some parameters.
1. Configure supported messages
```yaml
parameters:
    mms.idempotent_consumer.messenger_bundle.supported_messages:
        - "App\Message\MyMessage"
        - "App\Message\MyMessageInterface"
```
2. Configure supported transports
```yaml
parameters:
    mms.idempotent_consumer.messenger_bundle.supported_transports:
        - 'my_transport_name'
        - 'other_transport_name'
```

if you configure both in first order it will check transport then message.

## Support

:hugs: Please consider contributing if you feel you can improve this package, otherwise submit an issue via the GitHub page and include as much
information as possible, including steps to reproduce, platform versions and anything else to help pinpoint the root cause.

## Contributing

:+1: If you do contribute, we thank you, but please review the [CONTRIBUTING](CONTRIBUTING.md) document to help us ensure the project
is kept consistent and easy to maintain.

## Versioning

:hourglass: This project will follow [Semantic Versioning 2.0.0](https://semver.org/spec/v2.0.0.html).

## Changes

:hammer_and_wrench: All project changes/releases are noted in the GitHub releases page and in the [CHANGELOG](CHANGELOG.md) file.

Following conventions laid out by [keep a changelog](https://keepachangelog.com/en/1.1.0/).
