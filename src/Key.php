<?php
namespace LucidTunes\Jaak;

use Jose\Component\Core\AlgorithmManager;
use Jose\Component\Core\Converter\StandardConverter;
use Jose\Component\Core\JWK;
use Jose\Component\KeyManagement\JWKFactory;
use Jose\Component\Signature\Algorithm\ES256;
use Jose\Component\Signature\JWS;
use Jose\Component\Signature\JWSBuilder;
use Jose\Component\Signature\JWSVerifier;
use Jose\Component\Signature\Serializer\CompactSerializer;
use Jose\Component\Signature\Serializer\JWSSerializerManager;

class Key
{
    /** Jaak EC keys Curve */
    const CURVE = 'P-256';
    /** Jaak keys signing ECDSA algorithm */
    const ALG = 'ES256';
    /** @var JWK key */
    protected $jwk;

    /**
     * Generate a Key using a new JWK
     *
     * @return Key
     */
    public static function create()
    {
        $key = JWKFactory::createECKey(self::CURVE, [
            "key_ops" => [
                "sign",
                "verify"
            ],
        ]);

        return new self($key);
    }

    /**
     * Generate a Key using a Json data structure
     *
     * @param string $json
     * @return Key
     */
    public static function createFromJWK(string $json = "{}")
    {
        $key = JWK::createFromJson($json);
        return new self($key);
    }

    /**
     * Generate a Key using an Array data structure
     *
     * @param array $values
     * @return Key
     */
    public static function createFromJWKArray(array $values = [])
    {
        $key = JWK::create($values);
        return new self($key);
    }

    /**
     * Constructor
     *
     * @param JWK $jwk
     */
    public function __construct(JWK $jwk)
    {
        $this->jwk = $jwk;
        Validators::isValidKey($this);
    }

    /**
     * Get the embedded JWK key's thumbprint. It is the equivalent of
     * <jaak-utils/modules/key.js> following code:
     *
     *      get id() {
     *          return this.toJWK()
     *              .then(normalise)
     *              .then(JSON.stringify)
     *              .then(Jose.Utils.arrayFromString)
     *              .then(sha256)
     *              .then(hash => base64url.encodeArray(hash))
     *      }
     *
     * @return string
     */
    public function getId() : string
    {
        return $this->toJWK()->thumbprint('sha256');
    }

    /**
     * Get the JWK public key, if any
     *
     * @return JWK
     */
    public function getPublicKey() : JWK
    {
        return $this->jwk->toPublic();
    }

    /**
     * Check if the JWK is private
     *
     * @return bool
     */
    public function isPrivate() : bool
    {
        return $this->jwk->has('d');
    }

    /**
     * Check if the JWK is public
     *
     * @return bool
     */
    public function isPublic() : bool
    {
        return !$this->isPrivate();
    }

    /**
     * Get the embedded Jose JWK
     *
     * @return JWK
     */
    public function toJWK()
    {
        return $this->jwk;
    }

    /**
     * Sign a payload using ECDSA's ES256 algorithm
     *
     * @param $payload
     * @param bool $getJWS
     * @return string|JWS
     * @throws \Exception
     */
    public function sign($payload, $getJWS = false)
    {
        $algorithmManager = AlgorithmManager::create([ new ES256() ]);
        $jwsBuilder = new JWSBuilder(new StandardConverter(), $algorithmManager);

        $jws = $jwsBuilder
                    ->create()
                    ->withPayload($payload)
                    ->addSignature($this->jwk, ['alg' => self::ALG, 'kid' => $this->getId()])
                    ->build();

        if ($getJWS) {
            return $jws;
        }

        $serializer = new CompactSerializer(new StandardConverter());
        return $serializer->serialize($jws, 0);
    }

    /**
     * Verify a signature token, presumably signed with the given Key->JWK
     *
     * @param string $token
     * @param Key $key
     * @return bool
     */
    public static function verifySignature(string $token, Key $key)
    {
        $serializerManager = JWSSerializerManager::create([new CompactSerializer(new StandardConverter())]);
        $algorithmManager = AlgorithmManager::create([ new ES256() ]);
        $jwsVerifier = new JWSVerifier($algorithmManager);

        try {
            $jws = $serializerManager->unserialize($token);
            return $jwsVerifier->verifyWithKey($jws, $key->toJWK(), 0);
        } catch (\Exception $e) {
            return false;
        }
    }
}