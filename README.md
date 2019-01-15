## Vault

Secure, Compliant Document Storage

### Overview

There are significant legislative requirements for anyone operating as
an FSP (Financial Services Provider) and for companies that deal with
finance or money across various industries, from banks and credit
institutions, arttorneys, real estate agents, amongst others.

FICA (Financial Intelligence Center Act) is South African legislation
that requires certain documents, such as proof-of-address and copies
of IDs or Passports (amongst others).

KYC (Know Your Customer) is required in all FATF (Financial Action Task
Force) member countries. There are additional requirements when dealing
with PEPs (Politically Exposed Persons) which requires an enhanced due
diligence process.

AML (Anti Money Laundering) and CFT (Countering Financing of Terrorism)
is legislation that requires certain types of transactions be reported
to the FIC (Financial Intelligence Center).

These are just a few of the requirements in South Africa, and there are
many more depending on your country of operation.

Penalties for non compliance range from $700,000 to $7,000,000 as well
as between 5 and 15 years prison time.

These requirements ensure that businesses in the right industries are
involved and compliant, which requires them to collect and store images,
documents, and other metadata on individuals, and have them available
for scrutiny if the FIC or the SARB (South African Reserve Bank) ask
for them.

SARB issues, on average, $5,000,000 in fines per year.

These caches of personal information are prime targets for malicious
actors, and we have already seen several data breaches and leaks over
the past few years.

There is now new legislation coming into effect called POPI (Protection
of Personal Information) which will require strict adhearance, with
massive penalties for non-compliance. A data breach could mean financial
ruin.

The purpose of this Software is to provide a safe, secure, centralized,
encrypted, and heavily access controlled mechanism with which you can
store and retrieve documents on behalf of entities.

## Security Measures

### Headers

Headers required in all requests set when manually registering client/user

The following headers must be present on all calls

```
App: <application-guid-from-env>
Client: <user-app_id>
Authrorization: <user-app_key>
```

### IP Address

All instances of Vault are locked to being accessible by a single IP.

Vault should not run on the open internet, but should be deployed on the
same internal network as the service that is consuming it **not on the
same machine, the same _network_**

## Endpoints

### Entity Registration

You use the entity registration in order to assign a unique key and
secret for an entity you are registering.

We store the key, and the secret us utilized like a password (a hash is
stored and checked against when accessing the entity).

There is an additional header requirement, the `Registration-Access-Key`
that is set in the .env file.

This key ensures that only authorized parties may generate entities.

#### Request

POST `/entity`

```
App: xxx
Client: xxx
Authorization: xxx
Registration-Access-Key: xxx
```

```
{
    "key": "some-key",
    "secret": "some-secret"
}
```

#### Response

```
{
    "_id": "9b40c7a4-4612-425d-813e-03278b3b7ea0",
    "key": "some-key",
    "access_key": "0cf6141b-b1d3-446d-b76f-9e44ca65053c",
    "encode_key": "a51c670b-f3e6-4b26-8fd5-ae3240786955",
    "decode_key": "5876ed8f-7170-45ff-8cf9-7258726223db"
}
```

### Document Uploading

Once you've registered an entity you're able to add and retrieve
documents on their behalf.

There are additional header requirements on this call. Once an entity
has been registered it is assigned an `Entity-Access-Key` which is
required on all requests made for an entity.

When you register an entity you also receive an encoding key, and a
decoding key. The relevant one (in this case, encoding) must be send in
the `action` parameter.

You must also generate and send a 32 digit alphanumeric value with your
request which can be thought of as the password to the document. This
value is never persisted in the Vault (not even hashed) which ensures
that the Vault cannot decrypt documents without being provided the info
it needs by the client.

An example of a request:

```
POST /api/document/upload HTTP/1.1
Host: localhost:8008
App: 96DDB8FF-3DD6-4485-AE78-61B7A2A550CC
Client: 32A46C38-20F2-4A08-9392-B00DCF91DB0A
Authorization: 96BD3923-2AA7-487E-856A-67057F5267D8
Entity-Access-Key: 0cf6141b-b1d3-446d-b76f-9e44ca65053c
Content-Type: multipart/form-data; boundary=----WebKitFormBoundary7MA4YWxkTrZu0gW

------WebKitFormBoundary7MA4YWxkTrZu0gW
Content-Disposition: form-data; name="entity_id"

9b40c7a4-4612-425d-813e-03278b3b7ea0
------WebKitFormBoundary7MA4YWxkTrZu0gW
Content-Disposition: form-data; name="key"

some-key-235
------WebKitFormBoundary7MA4YWxkTrZu0gW
Content-Disposition: form-data; name="secret"

some-secret
------WebKitFormBoundary7MA4YWxkTrZu0gW
Content-Disposition: form-data; name="action"

a51c670b-f3e6-4b26-8fd5-ae3240786955
------WebKitFormBoundary7MA4YWxkTrZu0gW
Content-Disposition: form-data; name="document"; filename="example.png"
Content-Type: image/png

------WebKitFormBoundary7MA4YWxkTrZu0gW
Content-Disposition: form-data; name="document_key"

ZKnBMNfTBrTFLtMD916uV2ogw6Vvopw8
------WebKitFormBoundary7MA4YWxkTrZu0gW--
```

#### Returns
```
{
    "_id": "3e6fcc3b-4c95-44c3-bd69-95e29a619bf9",
    "hash": "204445e174cdf6c3c3e27e73fd8226e5",
    "size": 1282242
}
```

You can verify the hash and size with the file that you initially
uploaded in order to confirm integrity.

### Document Downloading

For these calls, much like the encoding request, you must send the
_decoding_ key that was issues in the entity registration.

Keeping these keys seperate (along with your entity registration key)
allows full segregation of all actions in the system. This way you
can ensure that access to information is strictly controlled, and on
a need-to-know basis.

It is recommended that these keys get distributed amongst multiple
individuals or roles, so that no one person can perform all actions
in the system.

Headers

```
App: xxx
Client: xxx
Authorization: xxx
Entity-Access-Key: xxx
```

POST `/document/{document_id}` (the `_id`) returned on upload

```
{
    "entity_id": "9b40c7a4-4612-425d-813e-03278b3b7ea0",
    "key": "some-key-235",
    "secret": "some-secret",
    "document_key": "ZKnBMNfTBrTFLtMD916uV2ogw6Vvopw8",
    "action": "5876ed8f-7170-45ff-8cf9-7258726223db"
}
```

#### Returns

Will return the raw document, as it was uploaded.

# Server Config

SSL is a prerequisite.

Generate a self signed certificate for apache.

The reason for this is that vault *may only ever run on an internal network* and
it *may only communicate over ssl*.

Since you can't generate an ssl certificate via letsencrypt etc you need to
generate your own for use on your internal network.

Since no browsers will ever connect to vault this is fine, as it's the browsers
that do the warning notices and this is api to api.

TLS/SSL works by using a combination of a public certificate and a private key.
The SSL key is kept secret on the server. It is used to encrypt content sent to
clients. The SSL certificate is publicly shared with anyone requesting the
content. It can be used to decrypt the content signed by the associated SSL key.

Issue a 4096 bit certificate valid for a thousand years.

```
sudo openssl req -x509 -nodes -days 365000 -newkey rsa:4096 -keyout /etc/ssl/private/vault-ssl.key -out /etc/ssl/certs/vault-ssl.cert
```

Next create a strong Diffie-Hellman group, which is used in negotiating Perfect
Forward Secrecy with clients

```
sudo openssl dhparam -out /etc/ssl/certs/dhparam.pem 4096
```

This takes a long time.


Configure your SSL params

```
/etc/apache2/conf-available/ssl-params.conf
```

```
# from https://cipherli.st/
# and https://raymii.org/s/tutorials/Strong_SSL_Security_On_Apache2.html

SSLCipherSuite EECDH+AESGCM:EDH+AESGCM:AES256+EECDH:AES256+EDH
SSLProtocol All -SSLv2 -SSLv3
SSLHonorCipherOrder On
# Disable preloading HSTS for now.  You can use the commented out header line that includes
# the "preload" directive if you understand the implications.
#Header always set Strict-Transport-Security "max-age=63072000; includeSubdomains; preload"
Header always set Strict-Transport-Security "max-age=63072000; includeSubdomains"
Header always set X-Frame-Options DENY
Header always set X-Content-Type-Options nosniff
# Requires Apache >= 2.4
SSLCompression off
SSLSessionTickets Off
SSLUseStapling on
SSLStaplingCache "shmcb:logs/stapling-cache(150000)"

SSLOpenSSLConfCmd DHParameters "/etc/ssl/certs/dhparam.pem"
```

Update default vhost

```
sudo vim /etc/apache2/sites-available/default-ssl.conf
```

```
<IfModule mod_ssl.c>
        <VirtualHost _default_:443>
                ServerAdmin admin@vault.co.za
                ServerName 1.1.1.1

                DocumentRoot /srv/vault/public

                ErrorLog ${APACHE_LOG_DIR}/error.log
                CustomLog ${APACHE_LOG_DIR}/access.log combined

                SSLEngine on

                SSLCertificateFile      /etc/ssl/certs/vault-ssl.cert
                SSLCertificateKeyFile /etc/ssl/private/vault-ssl.key

                <FilesMatch "\.(cgi|shtml|phtml|php)$">
                                SSLOptions +StdEnvVars
                </FilesMatch>
                <Directory /usr/lib/cgi-bin>
                                SSLOptions +StdEnvVars
                </Directory>

        </VirtualHost>
</IfModule>
```

Add a redirect to the default port 80

```
sudo vim /etc/apache2/sites-available/000-default.conf
```

Add `Redirect permanent "/" "https://your_domain_or_IP/"` to your vhost

Enable 80 and 443 on the firewall

Enable the right extensions

```
sudo a2enmod ssl
sudo a2enmod headers
sudo a2ensite default-ssl
sudo a2enconf ssl-params
```

Test

```
sudo apache2ctl configtest
```

Reload

```
sudo systemctl restart apache2
```

# Authors

Add yourself here if you contribute.

* [Brian Maiyo](https://github.com/kiproping)
* [Darryn Ten](https://github.com/darrynten)
* [Unicorn Global et al](https://github.com/UnicornGlobal)
