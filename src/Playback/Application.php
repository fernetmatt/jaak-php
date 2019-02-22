<?php
namespace Lucid\Jaak\Playback;

use GuzzleHttp\Client;
use Lucid\Jaak\Utils\Key;
use Lucid\Jaak\Utils\Validators;

class Application
{
    /** @var Key */
    protected $key;

    /** @var array */
    protected $options;

    /**
     * Generate a Application using a Key and an (optional) set of options
     *
     * @param Key $key
     * @param array $options
     * @return Application
     */
    public static function generate(Key $key, array $options = [])
    {
        Validators::isValidKey($key);
        Validators::isValidApplicationOptionsSet($options);
        return new self($key, $options);
    }

    /**
     * Constructor
     *
     * @param Key $key
     * @param array $options
     */
    private function __construct(Key $key, array $options)
    {
        $this->key = $key;
        $this->options = array_merge(['uri' => 'https://playback.beta.jaak.io'], $options);
        // TODO: move default URL outside the source code - manage it via ENV etc.
    }

    /**
     * Register Device API call
     *
     * @param Device $device
     * @return Device
     * @throws \Exception
     */
    public function registerDevice(Device $device) : Device
    {
        Validators::isValidDevice($device);
        $response = Application::request([
            'query' =>
                'mutation RegisterDevice($input: RegisterDeviceInput!) {
                    registerDevice(input: $input) {
                      alreadyRegistered
                      device {
                        id
                        createdAt
                      }
                    }
                  }',
            'variables' => [
                'input' => $device
            ]
        ], $this);

        if (!empty($response['data']['registerDevice']['device']['id'] &&
            !empty($response['data']['registerDevice']['device']['createdAt']))
        ) {
            $device->setId($response['data']['registerDevice']['device']['id']);
            $device->setCreatedAt(new \DateTime(
                $response['data']['registerDevice']['device']['createdAt'],
                new \DateTimeZone('UTC')
            ));
        } else {
            throw new \Exception('Registered Device data sent back by Jaak'. 'are incomplete');
        }

        return clone $device;
    }

    /**
     * Send a GraphQL request to JAAK
     *
     * @param array $payload
     * @param Application $app
     * @return array
     * @throws \Exception
     */
    private static function request(array $payload, Application $app) : array
    {
        $jsonPayload = json_encode($payload);
        $client = new Client(['base_uri' => $app->options['uri']]);
        $token = $app->key->sign($jsonPayload);

        // TODO investigate async implementation for this request
        $res = $client->post('/graphql', [
            'headers' => ['Content-Type' => 'Application/Jose'],
            'body' => $token,
            'debug' => false
        ]);

        if ($res->getStatusCode() != 200 && $res->getStatusCode() != 201) {
            throw new \Exception('Jaak API error response: ' . $res->getStatusCode());
        }

        $responseData = json_decode($res->getBody()->getContents(), true);
        if ($responseData === null) {
            throw new \Exception('Problem converting Jaak API response JSON');
        }

        return $responseData;
    }

}