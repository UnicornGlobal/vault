## Vault

Secure, Compliant Document Storage

### Headers

Headers required in all requests set when manually registering client/user
ClientMiddleware also checks for the IP address
```
{
  "app_id":"string"
  "app_key" "string"
}
```

### Entity Registration

#### URL

POST `/entity`

```
{
  "access_key": "GUID", 
  "access_secret": "GUID", 
}
```

#### Returns

```
{
   "_id": "GUID",
   "access_key": "GUID",
   "encoding_key": "GUID",
   "decoding_key": "GUID"
}
```

### Document Uploading

POST `/document/upload`


```
{
  "_id": "our _id we returned on entity registration",
  "key": "the key the client provided during entity registration",
  "secret": "the hashed secret for the entity from registration that we check",
  "action": "bad name [just an example] but will be the encrypt_key for an upload",
  "password": "the-password-for-the-document",
  "doc": "the-document"
}
```

#### Returns
```
{
  "_id": "document-id-used-to-access-docs-in-decrypt",
  "hash": "the-hash-of-decrypted-document"
}
```

### Document Downloading

POST `/document/{document_id}` (the _id) returned on upload

```
{
  "_id": "_id on entity we returned in registration",
  "key": "the key the client provided during entity registration",
  "secret": "the hashed secret for the entity from registration that we check",
  "action": "bad name [just an example] but will be the decrypt_key received during registration",
  "password": "the-password-for-the-document",
  "hash": "the-hash-received-after-upload"
}
```

#### Returns
```
Base 64 encoded string, basically binary of the document
```
