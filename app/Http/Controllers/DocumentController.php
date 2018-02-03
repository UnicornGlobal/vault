<?php

namespace App\Http\Controllers;

use App\Document;
use App\Entity;
use Webpatser\Uuid\Uuid;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Facades\Config;

class DocumentController extends Controller
{
    /**
     * Save docs as encrypted blobs in the DB
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function saveDocument(Request $request)
    {
        $this->validate($request, [
            'entity_id' => 'required',
            'encrypt_secret' => 'required',
            'document_key' => 'required',
            'document' => 'required|mimes:jpg,png,jpeg,pdf'
        ]);

        $fields = $request->only('entity_id', 'encrypt_secret', 'document_key', 'document');

        $file = $request->file('document');

        if (is_null($file)) {
            throw new \Exception('No Document Provided');
        }

        $entity = Entity::loadFromUuid($fields['entity_id']);
        if(!$fields['encrypt_secret'] === $entity->encoding_key) {
            throw new \Exception('There was a problem validating the encrypt key');
        }

        //Encrypt file into blob
        $encyptedFile = $this->encryptFile($file, $fields['document_key']);

        $document = new Document();
        $document->_id = Uuid::generate(4)->string;
        $document->entity_id = $fields['entity_id'];
        $document->mimetype = $file->extension();
        $document->hash = md5_file($file);
        $document->blob = $encyptedFile;
        $document->save();

        return response()->json($document, 200);
    }

    /**
     * Receives the document UUID, retrieves the file, decrypts it
     * then returns the image as a base64 string
     * @param Request $request
     * @param $documentId
     * @return string
     * @throws \Exception
     */
    public function retrieveDocument(Request $request, $documentId)
    {
        $this->validate($request, [
            'entity_id' => 'required',
            'decrypt_secret' => 'required',
            'document_key' => 'required',
            'hash' => 'required'
        ]);

        $fields = $request->only('entity_id', 'decrypt_secret', 'document_key', 'hash');

        $entity = Entity::loadFromUuid($fields['entity_id']);
        if(!$fields['decrypt_secret'] === $entity->decoding_key) {
            throw new \Exception('There was a problem validating the decrypt key');
        }

        $document = Document::loadFromUuid($documentId);

        $decryptedDocument = $this->decryptFile($document->blob, $fields['document_key']);

        return base64_encode($decryptedDocument);
    }

    /**
     * Function to encrypt uploaded files with specific key for each upload. The key is
     * encrypted and stored in the DB.
     * @param UploadedFile $file
     * @param $key
     * @return File
     */
    private function encryptFile(UploadedFile $file, $key)
    {
        $encryptor = new Encrypter($key, Config::get('app.cipher'));
        $actualFile = File::get($file->getRealPath());
        return $encryptor->encrypt($actualFile, false);
    }

    /**
     * Decrypts the uploaded file, given the key
     * @param $encryptedDocument
     * @param $key
     * @return string
     */
    private function decryptFile($encryptedDocument, $key)
    {
        $encryptor = new Encrypter($key, Config::get('app.cipher'));
        return $encryptor->decrypt($encryptedDocument, false);
    }

}
