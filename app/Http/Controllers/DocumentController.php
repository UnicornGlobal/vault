<?php

namespace App\Http\Controllers;

use App\Document;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    /**
     * Save docs as encrypted blobs in the DB
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function saveDoc(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|string',
            'photo' => 'required|image|file|mimes:png,jpeg',
        ]);

        $file = $request->file('photo');

        if (is_null($file)) {
            throw new \Exception('No Document Provided');
        }

        $filename = sprintf('%s.%s', Uuid::generate(4)->string, $file->extension());

        $key = str_replace('-', '', Uuid::generate(4)->string);
        $encyptedFile = $this->encryptFile($file, $key);

        Storage::putFileAs(
            '',
            $encyptedFile,
            $filename
        );

        Storage::setVisibility($filename, 'private');

        $document = new Document();
        $document->_id = Uuid::generate(4)->string;
        $document->title = $request->get('title');
        $document->mimetype = $file->extension();
        $document->path = Storage::url($filename);
        $document->hash = md5_file($file->getRealPath());
        $document->file_key = encrypt($key);
        $document->created_by = Auth::user()->id;
        $document->updated_by = Auth::user()->id;
        $document->save();

        return response()->json($document, 201);
    }

    /**
     * Receives the document UUID, retrieves the file, decrypts it
     * then returns the image as a base64 string
     * @param $docId
     * @return string
     */
    public function retrieveDoc($docId)
    {
        $this->validateDocumentId($docId);

        $document = Document::loadFromUuid($docId);

        $contents = Storage::get($document->path);

        $key = decrypt($document->key);

        $image = $this->decryptFile($contents, $key);

        return base64_encode($image);
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
        return $encryptor->encryptString($file);
    }

    /**
     * Decrypts the uploaded file, given the key
     * @param UploadedFile $file
     * @param $key
     * @return string
     */
    private function decryptFile(UploadedFile $file, $key)
    {
        $encryptor = new Encrypter($key, Config::get('app.cipher'));
        return $encryptor->decrypt($file);
    }

}
