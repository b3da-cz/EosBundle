<?php
/**
 * Created by PhpStorm.
 * User: Tomas Beran
 * Date: 11-Jun-16
 */

namespace b3da\EasyOpenSslBundle\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="ClientRepository")
 * @ORM\Table(name="eos_client")
 */
class Client
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
     * @var string $privKey
     *
     * @ORM\Column(type="text")
     */
    private $privKey;

    /**
     * @var string $pubKey
     *
     * @ORM\Column(type="text")
     */
    private $pubKey;

    /**
     * @var Message $message
     *
     * @ORM\OneToMany(targetEntity="Message", mappedBy="client")
     */
    private $message;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->message = new ArrayCollection();
    }

    /**
     * Set id
     *
     * @param string $id
     *
     * @return Client
     */
    public function setId($id=null)
    {
        $this->id = $id;
        return $this;
    }

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
     * Set privKey
     *
     * @param string $privKey
     *
     * @return Client
     */
    public function setPrivKey($privKey)
    {
        $this->privKey = $privKey;

        return $this;
    }

    /**
     * Get privKey
     *
     * @return string
     */
    public function getPrivKey()
    {
        return $this->privKey;
    }

    /**
     * Set pubKey
     *
     * @param string $pubKey
     *
     * @return Client
     */
    public function setPubKey($pubKey)
    {
        $this->pubKey = $pubKey;

        return $this;
    }

    /**
     * Get pubKey
     *
     * @return string
     */
    public function getPubKey()
    {
        return $this->pubKey;
    }

    /**
     * Add message
     *
     * @param \b3da\EasyOpenSslBundle\Entity\Message $message
     *
     * @return Client
     */
    public function addMessage(\b3da\EasyOpenSslBundle\Entity\Message $message)
    {
        $this->message[] = $message;
        $message->setClientId($this->id);

        return $this;
    }

    /**
     * Remove message
     *
     * @param \b3da\EasyOpenSslBundle\Entity\Message $message
     */
    public function removeMessage(\b3da\EasyOpenSslBundle\Entity\Message $message)
    {
        $this->message->removeElement($message);
        $message->setClientId(null);
    }

    /**
     * Get message
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMessage()
    {
        return $this->message;
    }
}
