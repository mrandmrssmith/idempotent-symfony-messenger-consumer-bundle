# Symfony Messenger JSON Serializer

A JSON serializer for external messages from Symfony Messenger.
This will allow you to have one single JSON serializer for all your external message. You only need to register the service, alias it and mention which message you would like to be deserialized.

## Installation

Add this package to your project
```shell
composer require mrandmrssmith/symfony-messenger-json-serializer
```

## Usage
1. Configure Messenger
```yaml
framework:
    messenger:
        transports:
            external:
                dsn: '%env(MESSENGER_DSN)%'
                serializer: external_message.messenger.serializer

        routing:
             'MrAndMrsSmith\Queue\ExternalMessage': external
```
2. Configure your serializer
```yaml
  external_message.messenger.serializer:
    class: MrAndMrsSmith\SymfonyMessengerJSONSerializer\Serializer\MessengerJSONSerializer
    factory: [MrAndMrsSmith\SymfonyMessengerJSONSerializer\Serializer\MessengerJSONSerializerFactory, 'create']
    arguments:
      $serializer: '@serializer'
      $messageClassResolver:  MrAndMrsSmith\Queue\ExternalMessage    

```
That will create a serializer with default resolver which will use the message class name to deserialize the message.
But you can create your own resolver and pass it to the serializer. Your resolver should implement `MessageClassResolver` and you can implement your own logic to resolve class name base on data in message for example get event name from header
```php
class MyMessageClassResolver implements MessageClassResolver
{
    public function resolveClass(array $encodedEnvelope): string;
    {
      // your logic to resolve class name
    }
}
```
```yaml
  external_message.messenger.serializer:
    class: MrAndMrsSmith\SymfonyMessengerJSONSerializer\Serializer\MessengerJSONSerializer
    arguments:
      $serializer: '@serializer'
      $messageClassResolver: '@my_message_class_resolver'    

  my_message_class_resolver:
    class: MyMessageClassResolver
```


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
