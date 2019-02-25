<?php
namespace Lucid\Jaak;

use Jose\Component\Core\JWK;

class Validators
{
    public static function isValidKey(Key $key)
    {
        return self::isValidJWK($key->toJWK(), $key->isPrivate());
    }

    private static function isValidJWK(JWK $key, bool $isPrivate = false )
    {
        if ($key === null) {
            throw new \InvalidArgumentException('Expected Key to be a JWK, but it is not');
        }

        if ($isPrivate && $key->has('d') === false) {
            throw new \InvalidArgumentException('Expected Key to be private, but it is public');
        }

        if ($isPrivate === false && $key->has('d')){
            throw new \InvalidArgumentException('Expected Key to be public, but it is private');
        }

        if (strcmp($key->get('kty'), 'EC') !== 0) {
            throw new \InvalidArgumentException('Expected Key to be Elliptic Curve key, but it is not');
        }

        if (strcmp($key->get('crv'), Key::CURVE) !== 0) {
            throw new \InvalidArgumentException('Expected EC Key to be P-256 Curve, but it is not');
        }

        return true;
    }

    public static function isValidDeviceDataset(array $values)
    {
        if (empty($values['name'])) {
            throw new \InvalidArgumentException('Missing device name');
        }

        if (empty($values['key'])) {
            throw new \InvalidArgumentException('Missing device key');
        } else {

            if (empty($values['key']['kty'])) {
                throw new \InvalidArgumentException('Missing JWK kty field');
            }

            if (empty($values['key']['crv'])) {
                throw new \InvalidArgumentException('Missing JWK crv field');
            }

            if (empty($values['key']['x'])) {
                throw new \InvalidArgumentException('Missing JWK x field');
            }

            if (empty($values['key']['y'])) {
                throw new \InvalidArgumentException('Missing JWK y field');
            }
        }

        // TODO: do i need to enable this check?
        /*if (empty($values['consumerId'])) {
            throw new \InvalidArgumentException('Missing device consumerId');
        }*/

        return true;
    }

    public static function isValidDevice(Device $device)
    {
        if (empty($device->getName())) {
            throw new \DomainException('Device has an invalid name');
        }

        if ($device->isPaired() && empty($device->getConsumerId())) {
            throw new \DomainException('Device marked as paired but has an invalid consumer id');
        }

        if (empty($device->getKey()) || self::isValidKey($device->getKey()) === false) {
            throw new \DomainException('Device has invalid Key');
        }

        return true;
    }

    public static function isValidApplicationOptionsSet($options = [])
    {
        if (!empty($options['uri']) && !filter_var($options['uri'], FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException('Invalid Jaak API URL');
        }

        return true;
    }

}