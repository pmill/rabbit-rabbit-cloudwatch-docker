<?php

use Aws\CloudWatch\CloudWatchClient;
use pmill\RabbitRabbit\Conditions\GreaterThan;
use pmill\RabbitRabbit\ConsumerManager;
use pmill\RabbitRabbit\RabbitConfig;
use pmill\RabbitRabbitCloudWatch\CloudWatchRule;

require __DIR__ . '/vendor/autoload.php';

$configFilename = 'config.json';
if (isset($argv[1])) {
    $configFilename = $argv[1];
}

$config = getConfig($configFilename);
echo "Config loaded: " . json_encode($config) . PHP_EOL;

$rabbitConfig = new RabbitConfig([
    'baseUrl' => $config['rabbitmq']['host'],
    'username' => $config['rabbitmq']['username'],
    'password' => $config['rabbitmq']['password'],
]);
echo "RabbitMQ client created" . PHP_EOL;

$manager = new ConsumerManager($rabbitConfig);

$cloudWatchClient = new CloudWatchClient([
    'version' => 'latest',
    'region' => $config['cloudwatch']['region'], 'eu-west-1',
    'credentials' => [
        'key' => $config['cloudwatch']['key'],
        'secret' => $config['cloudwatch']['secret'],
    ],
]);

echo "CloudWatch client created" . PHP_EOL;

foreach ($config['metrics'] as $metricName => $metricData) {
    $manager->addRule(
        new CloudWatchRule(
            $metricData['vhost'],
            $metricData['queue'],
            $cloudWatchClient,
            $metricName
        ),
        new GreaterThan(0, true)
    );
}

if ($config['run'] === 'single') {
    echo "Running once..." . PHP_EOL;
    $manager->run();
    echo "Finished" . PHP_EOL;
} elseif ($config['run'] === 'daemon') {
    echo "Running every " . $config['interval'] . " seconds" . PHP_EOL;
    while (true) {
        $manager->run();
        sleep($config['interval']);
    }
}

/**
 * @param string $configFilename
 * @throws Exception
 */
function getConfig($configFilename) {
    if (!file_exists($configFilename)) {
        throw new \Exception('Could not find config file ' . $configFilename);
    }

    if (!is_readable($configFilename)) {
        throw new \Exception('Config file ' . $configFilename . ' is not readable');
    }

    $rawConfigJson = file_get_contents($configFilename);
    if ($rawConfigJson === false) {
        throw new \Exception('Failed to open config file ' . $configFilename);
    }

    $config = json_decode($rawConfigJson, true);
    if ($config === null) {
        throw new \Exception('Failed to parse config file ' . $configFilename);
    }

    return $config;
}
