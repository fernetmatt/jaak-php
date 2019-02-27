<?php

namespace LucidTunes\Jaak\Test;

use LucidTunes\Jaak\Key;
use LucidTunes\Jaak\Validators;
use PHPUnit\Framework\TestCase;

class KeyTest extends TestCase
{
    public function test__construct()
    {
        $key = Key::create();
        $valid = Validators::isValidKey($key);
        $this->assertTrue($valid);
    }


    public function testGetId()
    {
        $key = Key::createFromJWK(testAppKeyJsonString);
        $this->assertNotEmpty($key->getId());
    }

    public function testIsPrivate()
    {
        $key = Key::createFromJWK(testAppKeyJsonString);
        $this->assertTrue($key->isPrivate(), true);
    }

    public function testIsPublic()
    {
        $values = json_decode(testAppKeyJsonString, true);
        unset($values['d']);
        $key = Key::createFromJWKArray($values);
        $this->assertTrue($key->isPublic() && !$key->isPrivate(), true);
    }

    public function testGetPublicKey()
    {
        $key = Key::createFromJWK(testAppKeyJsonString);
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
        $key = Key::createFromJWK(testAppKeyJsonString);
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
