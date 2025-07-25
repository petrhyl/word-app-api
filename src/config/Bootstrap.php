<?php

namespace config;

use Exception;
use repository\database\DbConfiguration;
use services\message\sender\email\EmailSenderConfiguration;
use repository\database\Database;
use repository\language\LanguageRepository;
use repository\user\UserRepository;
use repository\vocabulary\VocabularyRepository;
use services\EncryptionService;
use services\user\auth\AuthService;
use services\message\UserMessageService;
use services\user\auth\configuration\AuthConfiguration;
use services\user\UserService;
use WebApiCore\Configuration\ConfigurationManager;
use WebApiCore\Container\Container;
use WebApiCore\Container\Instance\Provider\InstanceProvider;

class Bootstrap
{
    public static function bootstrapApp(Container $container, ConfigurationManager $config): void
    {
        self::addConfiguration($container, $config);
        self::addDatabase($container);
        self::addServices($container, $config);
    }

    public static function addConfiguration(Container $container, ConfigurationManager $config): void
    {
        $container->configure(DbConfiguration::class, 'db', $config);
        $container->configure(AuthConfiguration::class, 'auth', $config);
        $container->configure(EmailSenderConfiguration::class, 'sender.email', $config);
    }

    public static function addDatabase(Container $container): void
    {
        $container->bindScoped(Database::class);
    }

    public static function addServices(Container $container, ConfigurationManager $config): void
    {
        $secretKey = self::getSecretKey($config);

        $container->bindScoped(
            EncryptionService::class,
            fn(InstanceProvider $instanceProvider) => new EncryptionService($secretKey)
        );

        $container->bindScoped(UserMessageService::class);
        $container->bindScoped(AuthService::class);
        $container->bindScoped(UserService::class);
    }

    public static function addRepositories(Container $container): void
    {
        $container->bindScoped(
            UserRepository::class,
            fn(InstanceProvider $instanceProvider) => $instanceProvider->build(UserRepository::class)
        );


        $container->bindScoped(
            VocabularyRepository::class,
            fn(InstanceProvider $instanceProvider) => $instanceProvider->build(VocabularyRepository::class)
        );

        $container->bindScoped(
            LanguageRepository::class,
            fn(InstanceProvider $instanceProvider) => $instanceProvider->build(LanguageRepository::class)
        );
    }

    private static function getSecretKey(ConfigurationManager $conf): string
    {
        $section = $conf->getSection("appSecret");

        if (empty($section)) {
            throw new Exception('Not able to get secrete key from configuration.');
        }

        if (!array_key_exists('appSecret', $section)) {
            throw new Exception('Not able to get secrete key from configuration.');
        }

        return $section['appSecret'];
    }
}
