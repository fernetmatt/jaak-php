<?php

namespace Lucid\Jaak\Test;

use Lucid\Jaak\Playback\Application;

use Lucid\Jaak\Playback\Device;
use Lucid\Jaak\Utils\Key;
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
        $key = Key::fromJWK(self::testAppKeyJsonString);
        $this->assertInstanceOf(Application::class, Application::generate($key));

        $this->expectException(\TypeError::class);
        Application::generate(null);

        $this->expectException(\InvalidArgumentException::class);
        Application::generate($key, [ 'uri' => 'fail://thi-uri-will-fail@@@com']);
    }


    public function testRegisterDevice()
    {
        $key = Key::fromJWK(self::testAppKeyJsonString);
        $device = Device::createFromJson(self::testDeviceJsonString);
        $app = Application::generate($key);
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
}
