# __b3da/EasyOpenSslBundle__

### __Symfony__ Bundle for easy __OpenSSL encryption, decryption and key pair generation.__


#### Dependencies:

* symfony/framework-standard-edition ">=2.8|~3.1"


### Instalation:

* create project with Symfony framework

* composer require b3da/eos-bundle "dev-master"

* `AppKernel.php:`
>```php
>new b3da\EasyOpenSslBundle\b3daEasyOpenSslBundle(),
>```

* `parameters.yml(.dist):`
>```yml
># EasyOpenSSL encryption method
>eos_enc_method: aes-256-cbc
># ...
>```

* (you can extend base `Client` and `Message` entities from `b3da\EasyOpenSslBundle` namespace)

* update your schema


### Usage:

* new service `b3da_easy_open_ssl.eos` is available for OpenSSL operations
>```php
>$eos = $this->get('b3da_easy_open_ssl.eos');
>```

* create Client example (`FooController`)
>```php
>$client = new Client();
>$client = $eos->generateKeyPairForClient($client);
>if (!$client) {
>    // err generating keypair
>}
>// persist Client with keys
>```

* encrypt data by Client's private key example (`FooController`)
>```php
>$message = $eos->encrypt($client, 'msg data .. foo bar baz');
>```

* decrypt data by Client's public key example (`FooController`)
>```php
>$decryptedData = $eos->decrypt($message);
>// $decryptedData === 'msg data .. foo bar baz';
>```
