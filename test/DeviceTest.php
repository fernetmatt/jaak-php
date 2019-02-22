<?php

namespace Lucid\Jaak\Test;

use Lucid\Jaak\Playback\Device;
use PHPUnit\Framework\TestCase;

class DeviceTest extends TestCase
{
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
        }
    ';

    public function testFromJson()
    {
        $device = Device::createFromJson(self::testDeviceJsonString);
        $this->assertEquals('matte', $device->getName());
        $this->assertEquals('nz37CYUQYPjLDOkC8OMlepEn1e_EOI3YXC6vJfAGK0s', $device->getKey()->toJWK()->get('x'));
        $this->assertEquals('Dp-BrQ2dZyl1whFJZl7b-QTipEcLW-_hQWRoiatg1TQ', $device->getKey()->toJWK()->get('y'));
    }

    public function testFromBadJson()
    {
        $this->expectException(\InvalidArgumentException::class);
        Device::createFromJson(str_replace('[', 'BAD', self::testDeviceJsonString));
    }

    public function testFromIncompleteJson()
    {
        $this->expectException(\InvalidArgumentException::class);
        Device::createFromJson(str_replace('key', 'zzz', self::testDeviceJsonString));
    }
}
