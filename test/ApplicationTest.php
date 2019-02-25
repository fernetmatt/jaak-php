<?php

namespace Lucid\Jaak\Test;

use Lucid\Jaak\Application;
use Lucid\Jaak\Device;
use Lucid\Jaak\Key;
use PHPUnit\Framework\TestCase;

class ApplicationTest extends TestCase
{
    const testAppKeyJsonString = '
        {
            "crv": "P-256",
            "d": "ozNy7ssoATV32v4JhhUdJ7pcGKa1hLf4D7w3QtWH1Ow",
            "ext": true,
            "key_ops": [
                "sign"
            ],
            "kty": "EC",
            "x": "y24CuLgK-DvroU_wX6QiCTqV6Z8v2Z-sBvBuDYb9ZXU",
            "y": "d4LMbtBEoGzAShrSXgpbNAm3h6REXGwLnnkHRXeIKdk"
        }';

    const testDeviceJsonString = '
        {
            "name": "matte",
            "key": {
                "crv": "P-256",
                "ext": true,
                "key_ops": [
                  "verify"
                ],
                "kty": "EC",
                "x": "nz37CYUQYPjLDOkC8OMlepEn1e_EOI3YXC6vJfAGK0s",
                "y": "Dp-BrQ2dZyl1whFJZl7b-QTipEcLW-_hQWRoiatg1TQ"
            }
        }';

    public function testGenerate()
    {
        $key = Key::createFromJWK(self::testAppKeyJsonString);
        $application = Application::create($key);
        $this->assertInstanceOf(Application::class, $application);

        $this->expectException(\TypeError::class);
        Application::create(null);

        $this->expectException(\InvalidArgumentException::class);
        Application::create($key, [ 'uri' => 'fail://this-uri-will-fail@@@com']);
    }


    public function testRegisterDevice()
    {
        $key = Key::createFromJWK(self::testAppKeyJsonString);
        $app = Application::create($key);
        $device = Device::createFromJson(self::testDeviceJsonString);
        $device->setConsumerId('my-system-user-id');

        try {
            $device = $app->registerDevice($device);
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }

        $this->assertNotEmpty($device->getId());
        $this->assertIsString($device->getId());
        $this->assertNotEmpty($device->getCreatedAt());
        $this->assertInstanceOf(\DateTime::class, $device->getCreatedAt());
    }

    public function testRegisterNewDevice()
    {
        $devKey = Key::create();
        $device = Device::createWithNameAndKey('exnovo', $devKey);

        $app = Application::create(Key::createFromJWK(self::testAppKeyJsonString));

        try {
            $device = $app->registerDevice($device);
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }

        $this->assertInstanceOf(Device::class, $device);
        $this->assertNotEmpty($device->getId());
        $this->assertIsString($device->getId());
        $this->assertNotEmpty($device->getCreatedAt());
        $this->assertInstanceOf(\DateTime::class, $device->getCreatedAt());
    }

    public function testListTracks()
    {
        $application = Application::create(Key::createFromJWK(self::testAppKeyJsonString));
        $tracks = $application->listTracks();
        $this->assertIsArray($tracks);
    }


}
