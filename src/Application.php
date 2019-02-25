<?php
namespace Lucid\Jaak;

use Lucid\Jaak\GraphQL\Query;

class Application
{
    /** @var Key (wrapping Jaak's AppKey */
    protected $key;

    /** @var array */
    protected $options;

    /** @var /GuzzleHttp/Client */
    protected $httpClient;

    /**
     * Generate a Application using a Key and an (optional) set of options
     *
     * @param Key (wrapping an AppKey) $key
     * @param array $options
     * @return Application
     */
    public static function create(Key $key, array $options = [])
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
        $this->httpClient = new \GuzzleHttp\Client(['base_uri' => $this->options['uri']]);
    }

    /**
     * Make a request against Jaak API
     *
     * @param $payload
     * @return Response
     * @throws \Exception
     */
    public function request($payload) : Response
    {
        $jsonPayload = json_encode($payload);
        $token = $this->key->sign($jsonPayload);
        $res = $this->httpClient->post('/graphql', [
            'headers' => ['Content-Type' => 'Application/Jose'],
            'body' => $token,
            'debug' => false
        ]);

        if ($res->getStatusCode() != 200 && $res->getStatusCode() != 201) {
            throw new \Exception('Jaak API error response: ' . $res->getStatusCode());
        }

        $responseData = json_decode($res->getBody()->getContents(), false);
        if ($responseData === null) {
            throw new \Exception('Problem converting Jaak API response JSON');
        }

        $responseObj = new Response($responseData);
        if ($responseObj->hasErrors()) {
            throw new \Exception('Errors in Jaak Response: ' . current($responseObj->errors())->message);
        }

        return $responseObj;
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
        $response = $this->request([
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
        ]);

        if (isset($response->data()->registerDevice->device->id) &&
            isset($response->data()->registerDevice->device->createdAt)) {

            $device->setId($response->data()->registerDevice->device->id);
            $device->setCreatedAt(new \DateTime(
                $response->data()->registerDevice->device->createdAt,
                new \DateTimeZone('UTC')
            ));

        } else {
            throw new \Exception('Registered Device data sent back by Jaak is incomplete');
        }

        return clone $device;
    }


    /**
     * List available Tracks associated to the JaakApi via https://beta.jaak.io
     * @return array
     * @throws \Exception
     */
    public function listTracks() : array
    {
        $response = $this->request(['query' => Query::ListTracks]);

        if (!isset($response->data()->application->tracks->edges)) {
            throw new \Exception('Invalid Query ListTracks response');
        }

        return $response->data()->application->tracks->edges;
    }

    public function listApplication()
    {
        return $this
            ->request(['query' => Query::ListApplication])
            ->data()->application;
    }



}