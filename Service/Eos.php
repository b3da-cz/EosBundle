<?php

namespace b3da\EasyOpenSslBundle\Service;


use b3da\EasyOpenSslBundle\Entity\Client;
use b3da\EasyOpenSslBundle\Entity\Message;
use Doctrine\ORM\EntityManagerInterface;

class Eos
{
    /**
     * @var string $enc_method
     */
    private $enc_method;

    /**
     * @var EntityManagerInterface $em
     */
    private $em;

    /**
     * Eos constructor.
     * @param EntityManagerInterface $entityManagerInterface
     * @param string $enc_method
     */
    public function __construct(EntityManagerInterface $entityManagerInterface, $enc_method = 'aes-256-cbc') {
        $this->em = $entityManagerInterface;
        $this->enc_method = $enc_method;
    }

    /**
     * @param Client $client
     * @param string $digest_alg
     * @param int $private_key_bits
     * @param $private_key_type
     * @return Client|null
     */
    public static function generateKeyPairForClient(Client $client, $digest_alg = 'sha256', $private_key_bits = 4096, $private_key_type = OPENSSL_KEYTYPE_RSA) {
        try {
            if(!$client) {
                return null;
            }

            $config = [
                'digest_alg' => $digest_alg,
                'private_key_bits' => $private_key_bits,
                'private_key_type' => $private_key_type,
            ];

            $keyPair = openssl_pkey_new($config);

            $privKey = '';
            openssl_pkey_export($keyPair, $privKey);

            $pubKey = openssl_pkey_get_details($keyPair);
            $pubKey = $pubKey["key"];

            $client->setPubKey($pubKey);
            $client->setPrivKey($privKey);

            return $client;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @param Client $client
     * @param $data
     * @return Message|null
     */
    public function encrypt(Client $client, $data) {
        if (!$client) {
            return null;
        }

        try {

            if (is_array($data)) {
                $data = json_encode($data);
            }

            $privKey = $client->getPrivKey();

            $plainEncryptionKey = $this->getRndPseudoBytes();

            $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($this->enc_method));

            $encryptedData = openssl_encrypt($data, $this->enc_method, $plainEncryptionKey, 0, $iv);

            $encryptedEncryptionKey = '';
            openssl_private_encrypt($plainEncryptionKey, $encryptedEncryptionKey, $privKey);

            $message = new Message();
            $message->setClientId($client->getId());
            $message->setData(bin2hex($encryptedData));
            $message->setIv(bin2hex($iv));
            $message->setKey(bin2hex($encryptedEncryptionKey));

            return $message;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @param Message $message
     * @return string|null
     */
    public function decrypt(Message $message) {
        $clientId = $message->getClientId();
        $encryptedData = hex2bin($message->getData());
        $iv = hex2bin($message->getIv());
        $encryptedEncryptionKey = hex2bin($message->getKey());

        $client = $this->em->getRepository(Client::class)->find($clientId);

        if(!$client) {
            return null;
        }

        try {
            $pubKey = $client->getPubKey();

            $decryptedEncryptionKey = '';
            openssl_public_decrypt($encryptedEncryptionKey, $decryptedEncryptionKey, $pubKey);

            $decryptedData = openssl_decrypt($encryptedData, $this->enc_method, $decryptedEncryptionKey, 0, $iv);

            return $decryptedData;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @param int $bytes
     * @return string
     */
    public function getRndPseudoBytes($bytes = 32)
    {
        $isCryptographicSecure = false;
        $rndPseudoBytes = '';
        $i = 0;
        while (!$isCryptographicSecure) {
            if($i > 1000) {
                $rndPseudoBytes = openssl_random_pseudo_bytes($bytes++, $isCryptographicSecure);
            } else {
                $rndPseudoBytes = openssl_random_pseudo_bytes($bytes, $isCryptographicSecure);
            }
            $i++;
        }
        return $rndPseudoBytes;
    }
}