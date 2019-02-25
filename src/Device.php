<?php
namespace Lucid\Jaak;

class Device implements \JsonSerializable
{
    /**
     * Device arbitrary name
     * @var string
     */
    protected $name;

    /**
     * Your backend User ID
     * @var string
     */
    protected $consumerId;

    /**
     * Library JWK Wrapper (wrapping a DeviceKey)
     * @var Key
     */
    protected $key;

    /**
     * Indicate if this device is paired to a real User
     * @var boolean
     */
    protected $paired;

    /**
     * Jaak Device Id
     * @var string
     */
    protected $id;

    /**
     * Jaak creation datetime
     * @var \DateTime
     */
    protected $createdAt;


    /**
     * Generate a Device using a json data structure
     *
     * @param string $json
     * @return Device
     */
    public static function createFromJson(string $json = '{}') : Device
    {
        $deviceJson = json_decode($json, true);

        if ($deviceJson === null) {
            throw new \InvalidArgumentException('Bad Json for Device');
        }

        Validators::isValidDeviceDataset($deviceJson);

        $key = Key::createFromJWKArray($deviceJson['key']);
        Validators::isValidKey($key);

        $device = Device::createWithNameAndKey($deviceJson['name'], $key);
        Validators::isValidDevice($device);

        return $device;
    }

    public static function createWithNameAndKey(string $name, Key $key)
    {
        $device = new self();
        $device->setname($name);
        $device->setKey($key);
        return $device;
    }

    /**
     * Constructor
     *
     */
    public function __construct()
    {
        $this->name = '';
        $this->key = null;
        $this->consumerId = '';
        $this->paired = false;
        $this->id = '';
        $this->createdAt = null;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * Get the Consumer Id a.k.a. the outer User Id
     *
     * @return string
     */
    public function getConsumerId(): string
    {
        return $this->consumerId;
    }

    /**
     * Set the Consumer Id a.k.a. the outer User Id
     * @param $userId
     * @return Device
     */
    public function setConsumerId($userId) : self
    {
        $this->consumerId = $userId;
        return $this;
    }

    /**
     * @return Key
     */
    public function getKey(): Key
    {
        return $this->key;
    }

    /**
     * @param Key $key
     */
    public function setKey(Key $key): void
    {
        $this->key = $key;
    }

    /**
     * Check if the Device is paired to an outer User Id
     *
     * @return bool
     */
    public function isPaired(): bool
    {
        return $this->paired;
    }

    /**
     * Set the flag to indicate if the Device is paired to an outer User Id
     *
     * @param bool $paired
     */
    public function setPaired(bool $paired): void
    {
        $this->paired = $paired;
    }

    /**
     * @return string
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * JsonSerialize
     *
     * @return array|mixed
     */
    public function jsonSerialize()
    {
        return [
          'name' => $this->name,
          'key' => $this->key->toJWK(),
          'consumerId' => $this->consumerId
        ];
    }
}