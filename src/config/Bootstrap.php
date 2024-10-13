<?php

namespace config;

use Exception;
use models\DbConfiguration;
use models\email\EmailServerConfiguration;
use models\RegistrationConfiguration;
use repository\language\LanguageRepository;
use repository\user\UserRepository;
use repository\vocabulary\VocabularyRepository;
use services\EncryptionService;
use services\user\auth\AuthService;
use services\user\EmailSenderService;
use services\user\UserService;
use WebApiCore\Container\AppBuilder;
use WebApiCore\Container\InstanceProvider;

class Bootstrap
{
    public static function addDatabase(AppBuilder $builder): AppBuilder
    {
        $dbConfiguration = Configuration::getConfiguration(
            ['DB_HOST', 'DB_NAME', 'DB_PORT', 'DB_USER', 'DB_USER_PASSWORD']
        );

        if (count($dbConfiguration) < 5) {
            throw new Exception('Not able to get database configuration from environment.');
        }

        $dbConfigurationInstance = new DbConfiguration(
            $dbConfiguration['DB_HOST'],
            $dbConfiguration['DB_NAME'],
            $dbConfiguration['DB_PORT'],
            $dbConfiguration['DB_USER'],
            $dbConfiguration['DB_USER_PASSWORD']
        );

        $builder->Container->bindScoped(
            DbConfiguration::class,
            fn(InstanceProvider $instanceProvider) => $dbConfigurationInstance
        );

        $builder->Container->bindScoped(
            Database::class,
            fn(InstanceProvider $instanceProvider) => $instanceProvider->build(Database::class)
        );

        return $builder;
    }

    public static function addServices(AppBuilder $builder): AppBuilder
    {
        $secretKey = self::getSecretKey();

        $builder->Container->bindScoped(
            EncryptionService::class,
            fn(InstanceProvider $instanceProvider) => new EncryptionService($secretKey)
        );

        $emailConf = self::getEmailServerConfiguration();

        $builder->Container->bindScoped(
            EmailSenderService::class,
            fn(InstanceProvider $instanceProvider) => new EmailSenderService($emailConf)
        );
        $builder->Container->bindScoped(
            AuthService::class,
            fn(InstanceProvider $instanceProvider) => $instanceProvider->build(AuthService::class)
        );

        $registationConf = self::getRegistrationConfiguration();

        $builder->Container->bindScoped(
            UserService::class,
            fn(InstanceProvider $instanceProvider) => new UserService(
                $registationConf,
                $instanceProvider->get(AuthService::class),
                $instanceProvider->get(UserRepository::class),
                $instanceProvider->get(EmailSenderService::class)
            )
        );

        return $builder;
    }

    public static function addRepositories(AppBuilder $builder): AppBuilder
    {
        $builder->Container->bindScoped(
            UserRepository::class,
            fn(InstanceProvider $instanceProvider) => $instanceProvider->build(UserRepository::class)
        );

        $builder->Container->bindScoped(
            VocabularyRepository::class,
            fn(InstanceProvider $instanceProvider) => $instanceProvider->build(VocabularyRepository::class)
        );

        $builder->Container->bindScoped(
            LanguageRepository::class,
            fn(InstanceProvider $instanceProvider) => $instanceProvider->build(LanguageRepository::class)
        );

        return $builder;
    }


    private static function getEmailServerConfiguration(): EmailServerConfiguration
    {
        $confArray = Configuration::getConfiguration(
            ['EMAIL_SERVER', 'EMAIL_SENDER_ADR', 'EMAIL_SENDER_NM', 'EMAIL_SENDER_PSWD', 'EMAIL_PORT']
        );

        if (count($confArray) < 5) {
            throw new Exception('Not able to get email server configuration from environment.');
        }

        $conf = new EmailServerConfiguration();
        $conf->Server = $confArray['EMAIL_SERVER'];
        $conf->SenderAddress = $confArray['EMAIL_SENDER_ADR'];
        $conf->SenderName = $confArray['EMAIL_SENDER_NM'];
        $conf->SenderPassword = $confArray['EMAIL_SENDER_PSWD'];
        $conf->Port = $confArray['EMAIL_PORT'];

        return $conf;
    }

    private static function getRegistrationConfiguration(): RegistrationConfiguration
    {
        $confArray = Configuration::getConfiguration(
            ['VERIFICATION_LINK','LOGIN_LINK', 'EMAIL_VERIFICATION'],
        );

        if (count($confArray) < 2) {
            throw new Exception('Not able to get registration configuration from environment.');
        }

        $conf = new RegistrationConfiguration();
        $conf->VerificationClientLink = $confArray['VERIFICATION_LINK'];
        $conf->LoginClientLink = $confArray['LOGIN_LINK'];
        $conf->IsEmailVerificationRequired = $confArray['EMAIL_VERIFICATION'];

        return $conf;
    }

    private static function getSecretKey(): string
    {
        $conf = Configuration::getConfiguration(['APP_SECRET']);

        if (count($conf) === 0) {
            throw new Exception('Not able to get secrete key from configuration.');
        }

        return $conf['APP_SECRET'];
    }
}
