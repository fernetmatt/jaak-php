<?php

namespace LucidTunes\Jaak\Test;

use LucidTunes\Jaak\Application;
use LucidTunes\Jaak\Device;
use LucidTunes\Jaak\Key;
use PHPUnit\Framework\TestCase;

class ApplicationTest extends TestCase
{
    public function testGenerate()
    {
        $key = Key::createFromJWK(testAppKeyJsonString);
        $application = Application::create($key);
        $this->assertInstanceOf(Application::class, $application);

        $this->expectException(\TypeError::class);
        Application::create(null);

        $this->expectException(\InvalidArgumentException::class);
        Application::create($key, [ 'uri' => 'fail://this-uri-will-fail@@@com']);
    }


    public function testRegisterDevice()
    {
        $key = Key::createFromJWK(testAppKeyJsonString);
        $app = Application::create($key);
        $device = Device::createFromJson(testDeviceJsonString);
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

        $app = Application::create(Key::createFromJWK(testAppKeyJsonString));

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
        $application = Application::create(Key::createFromJWK(testAppKeyJsonString));
        $tracks = $application->listTracks();
        $this->assertIsArray($tracks);
    }


}
