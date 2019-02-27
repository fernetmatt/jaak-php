<?php

namespace LucidTunes\Jaak\Test;

use LucidTunes\Jaak\Device;
use PHPUnit\Framework\TestCase;

class DeviceTest extends TestCase
{
    public function testFromJson()
    {
        $device = Device::createFromJson(testDeviceJsonString);
        $jsonKey = json_decode(testDeviceJsonString);
        $this->assertEquals($jsonKey->name, $device->getName());
        $this->assertEquals($jsonKey->key->x, $device->getKey()->toJWK()->get('x'));
        $this->assertEquals($jsonKey->key->y, $device->getKey()->toJWK()->get('y'));
    }

    public function testFromBadJson()
    {
        $this->expectException(\InvalidArgumentException::class);
        Device::createFromJson(str_replace('[', 'BAD', testDeviceJsonString));
    }

    public function testFromIncompleteJson()
    {
        $this->expectException(\InvalidArgumentException::class);
        Device::createFromJson(str_replace('key', 'zzz', testDeviceJsonString));
    }
}
