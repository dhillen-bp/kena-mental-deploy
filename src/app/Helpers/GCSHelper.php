<?php

namespace App\Helpers;

use Google\Cloud\Storage\StorageClient;

class GCSHelper
{
    public static function uploadFile($bucketName, $folderName, $request, $columnName, $fileName)
    {
        $storage = new StorageClient([
            'keyFilePath' => base_path(env('GOOGLE_APPLICATION_CREDENTIALS')),
        ]);

        $bucket = $storage->bucket($bucketName);

        if ($request->hasFile($columnName)) {
            $image = $request->file($columnName);
            $imageExtension = $image->getClientOriginalExtension();
            $imageName = $fileName . '.' . $imageExtension;

            $objectName = $folderName . '/' . $imageName;
            $bucket->upload(
                file_get_contents($image->getRealPath()),
                ['name' => $objectName]
            );

            return $imageName; // Anda bisa mereturn nama gambar yang diupload jika diperlukan
        }

        return $imageName = ''; // Jika tidak ada file yang diupload, Anda bisa mereturn null atau nilai lain yang sesuai.
    }

    public static function deleteFile($bucketName, $folderName, $fileName)
    {
        $storage = new StorageClient([
            'keyFilePath' => env('GOOGLE_APPLICATION_CREDENTIALS'),
        ]);

        $bucket = $storage->bucket($bucketName);
        $objectName = $folderName . '/' . $fileName;
        $object = $bucket->object($objectName);

        if ($object->exists()) {
            $object->delete();
        }
    }
}
