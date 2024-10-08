<?php



namespace config;

class Configuration
{
    private const CONFIG_FILE_PATH = '/appSettings.php';
    private static ?array $configurationData = null;

    public static function getConfiguration(array $keys): array
    {
        $configKeyValuePairs = self::getAllConfigurations();

        if (count($configKeyValuePairs) === 0) {
            return [];
        }

        $result = [];
        foreach ($keys as $key) {
            if (array_key_exists($key, $configKeyValuePairs)) {
                $result[$key] = $configKeyValuePairs[$key];
            } elseif (!empty(getenv($key))) {
                $result[$key] = getenv($key);
            }
        }

        return $result;
    }

    public static function getAllConfigurations(): array
    {
        if (self::$configurationData !== null) {
            return self::$configurationData;
        }

        require_once(__DIR__ . self::CONFIG_FILE_PATH);

        if ($confData) {
            self::$configurationData = $confData;
        }

        return $confData;
    }
}
