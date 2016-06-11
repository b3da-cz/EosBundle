<?php
/**
 * Created by PhpStorm.
 * User: Tomas Beran
 * Date: 11-Jun-16
 */

namespace b3da\EasyOpenSslBundle\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="eos_message")
 */
class Message
{
    /**
     * @var string $id
     *
     * @ORM\Column(type="guid")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     */
    protected $id;

    /**
     * @var string $data
     *
     * @ORM\Column(type="text")
     */
    private $data;

    /**
     * @var string $iv
     *
     * @ORM\Column(type="text")
     */
    private $iv;

    /**
     * @var string $key
     *
     * @ORM\Column(type="text")
     */
    private $key;

    /**
     * @var integer $clientId
     *
     * @ORM\Column(type="integer")
     */
    private $clientId;


    /**
     * Get id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set data
     *
     * @param string $data
     *
     * @return Message
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get data
     *
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set iv
     *
     * @param string $iv
     *
     * @return Message
     */
    public function setIv($iv)
    {
        $this->iv = $iv;

        return $this;
    }

    /**
     * Get iv
     *
     * @return string
     */
    public function getIv()
    {
        return $this->iv;
    }

    /**
     * Set key
     *
     * @param string $key
     *
     * @return Message
     */
    public function setKey($key)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * Get key
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Set clientId
     *
     * @param integer $clientId
     *
     * @return Message
     */
    public function setClientId($clientId = null)
    {
        $this->clientId = $clientId;

        return $this;
    }

    /**
     * Get clientId
     *
     * @return integer
     */
    public function getClientId()
    {
        return $this->clientId;
    }
}
