<?php
namespace Agora\Core\Config;

class Config
{
    private $config;

    public function __construct($configFile)
    {
        if (is_string($configFile)) {
            if (!file_exists($configFile)) {
                throw new \Exception("Configuration file not found: " . $configFile);
            }
            $content = file_get_contents($configFile);
        } else {
            throw new \Exception("Config file path must be a string");
        }

        $this->config = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception("Invalid configuration file format: " . json_last_error_msg());
        }
    }

    public function get($key, $default = null)
    {
        $keys = explode('.', $key);
        $value = $this->config;

        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return $default;
            }
            $value = $value[$k];
        }

        return $value;
    }
}