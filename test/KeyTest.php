<?php

namespace LucidTunes\Jaak\Test;

use LucidTunes\Jaak\Key;
use LucidTunes\Jaak\Validators;
use PHPUnit\Framework\TestCase;

class KeyTest extends TestCase
{
    const testJwkJsonString = '{
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

    public function test__construct()
    {
        $key = Key::create();
        $valid = Validators::isValidKey($key);
        $this->assertTrue($valid);
    }


    public function testGetId()
    {
        $key = Key::createFromJWK(self::testJwkJsonString);
        $this->assertEquals("MXiegyEH_lrrexWCx9_rfXlG2qrgyMHG5tJ2_EJYDIU", $key->getId());
    }

    public function testIsPrivate()
    {
        $key = Key::createFromJWK(self::testJwkJsonString);
        $this->assertTrue($key->isPrivate(), true);
    }

    public function testIsPublic()
    {
        $values = json_decode(self::testJwkJsonString, true);
        unset($values['d']);
        $key = Key::createFromJWKArray($values);
        $this->assertTrue($key->isPublic() && !$key->isPrivate(), true);
    }

    public function testGetPublicKey()
    {
        $key = Key::createFromJWK(self::testJwkJsonString);
        self::assertFalse($key->getPublicKey()->has('d'));
    }

    public function testSign()
    {
        $key = Key::create();

        $payload = [
            'deviceId' => 1,
            'licenseId' => 2,
            'nonce' => 3
        ];

        $payload = json_encode($payload);

        $token = $key->sign($payload);
        $result = Key::verifySignature($token, $key);
        $this->assertTrue($result);
    }


    public function testFromJWK()
    {
        $key = Key::createFromJWK(self::testJwkJsonString);
        $this->assertInstanceOf(Key::class, $key);
    }

    public function testGenerate()
    {
        $key = Key::create();
        $this->assertEquals($key->toJWK()->get('kty'), 'EC');
        $this->assertEquals($key->toJWK()->get('crv'), Key::CURVE);
        $this->assertArrayHasKey('sign', array_flip($key->toJWK()->get('key_ops')));
        $this->assertArrayHasKey('verify', array_flip($key->toJWK()->get('key_ops')));
    }


}
