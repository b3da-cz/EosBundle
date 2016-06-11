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

---

##### simple API for basic tasks (optional)

* import routes into `routing.yml`:
>```yaml
>b3da_easy_open_ssl:
>     resource: "@b3daEasyOpenSslBundle/Controller/"
>     type:     annotation
>     prefix:   /eos/
>```

* /eos/msg-api/client/create/ [POST]
>```json
>Response:
>
>{"status":"success","id":"3ead5521-2fbc-11e6-834a-f0def1ff5901"}
>
>or
>
>{"status":"error","details":"key pair generating failed"}
>```

* /eos/msg-api/client/export-public/{id}/ [GET]
>```json
>Response:
>
>{"status":"success","data":{"id":"3ead5521-2fbc-11e6-834a-f0def1ff5901","pubkey":"LS0tLS1CR .. S0tLQ=="}}
>
>or
>
>{"status":"error","details":"no client for id"}
>```

* /eos/msg-api/client/import-public/{id}/ [POST]
>```json
>Request:
>
>{"data":{"id":"3ead5521-2fbc-11e6-834a-f0def1ff5901","pubkey":"LS0tLS1CR .. S0tLQ=="}}
>
>Response:
>
>{"status":"success","id":"3ead5521-2fbc-11e6-834a-f0def1ff5901"}
>
>or
>
>{"status":"error","details":"no data to import"}
>```

* /eos/msg-api/msg/encrypt/{clientId}/{data}/ [GET]
>```json
>Response:
>
>{"status":"success","data":{"message":":774c6b663650636 .. 34346616f5901"}}
>
>or
>
>{"status":"error","details":"no client for id"}
>```

* /eos/msg-api/msg/decrypt/{data}/ [GET]
>```json
>Request:
>
>/eos/msg-api/msg/decrypt/{"message":":774c6b663650636 .. 34346616f5901"}/
>
>Response:
>
>{"status":"success","data":"foo bar"}
>
>or
>
>{"status":"error","details":"wrong format"}
>```