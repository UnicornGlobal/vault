<?php

namespace App\Http\Controllers;

use App\Document;
use App\Entity;
use Illuminate\Encryption\Encrypter;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Webpatser\Uuid\Uuid;

class DocumentController extends Controller
{
    /**
     * Save docs as encrypted blobs in the DB.
     *
     * @param Request $request
     *
     * @throws \Exception
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveDocument(Request $request)
    {
        $this->validate($request, [
            'entity_id'    => 'required',
            'document_key' => 'required|size:32',
            'document'     => 'required|mimes:jpg,png,jpeg,pdf|min:2|max:15000',
        ]);

        $fields = $request->only('entity_id', 'document_key', 'document');

        $file = $request->file('document');

        if (is_null($file)) {
            throw new \Exception('No Document Provided');
        }

        if ($fields['document_key'] === md5_file($file)) {
            throw new \Exception('Do not use the hash of the document as the key to the document!');
        }

        //Encrypt file into blob
        $encryptedFile = $this->encryptFile($file, $fields['document_key']);

        $entity = Entity::where('_id', $fields['entity_id'])->first();

        $document = new Document();
        $document->_id = Uuid::generate(4)->string;
        $document->entity_id = $entity->id;
        $document->mimetype = $file->getMimeType();
        $document->hash = md5_file($file);
        $document->size = filesize($file);
        $document->file = $encryptedFile;
        $document->save();

        File::delete($file);
        File::delete($encryptedFile);

        return response()->json($document, 200);
    }

    /**
     * Receives the document UUID, retrieves the file, decrypts it
     * then returns the image as a base64 string.
     *
     * @param Request $request
     * @param $documentId
     *
     * @throws \Exception
     *
     * @return string
     */
    public function retrieveDocument(Request $request, $documentId)
    {
        $this->validate($request, [
            'entity_id'    => 'required',
            'document_key' => 'required',
        ]);

        $fields = $request->only('entity_id', 'document_key');

        $document = Document::loadFromUuid($documentId);

        $decryptedDocument = $this->decryptFile($document->file, $fields['document_key']);

        if ($document->hash !== md5($decryptedDocument)) {
            throw new \Exception('Integrity mismatch, so refusing to return document. Contact support.');
        }

        header(sprintf('Content-Hash: %s', $document->hash));
        header(sprintf('Content-Type: %s', $document->mimetype));
        header(sprintf('Content-Length: %s', $document->size));
        echo $decryptedDocument;
        File::delete($document);
        die();
    }

    /**
     * Function to encrypt uploaded files with specific key for each upload. The key is
     * encrypted and stored in the DB.
     *
     * @param UploadedFile $file
     * @param $key
     *
     * @return File
     */
    private function encryptFile(UploadedFile $file, $key)
    {
        $encryptor = new Encrypter($key, Config::get('app.cipher'));
        $actualFile = File::get($file->getRealPath());

        return $encryptor->encrypt($actualFile, false);
    }

    /**
     * Decrypts the uploaded file, given the key.
     *
     * @param $encryptedDocument
     * @param $key
     *
     * @return string
     */
    private function decryptFile($encryptedDocument, $key)
    {
        $encryptor = new Encrypter($key, Config::get('app.cipher'));

        return $encryptor->decrypt($encryptedDocument, false);
    }
}
