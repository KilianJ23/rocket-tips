<?php

// Destination path of uploaded files
const UPLOADS_DIR = 'public/uploads/';

// Extensions accepted for images
const FILE_EXT_IMG = ['jpg','jpeg','gif','png', 'webp'];

// MIME_TYPES constant used to check uploaded files
const MIME_TYPES = array(

    'txt'  => 'text/plain',
    'htm'  => 'text/html',
    'html' => 'text/html',
    'php'  => 'text/html',
    'css'  => 'text/css',
    'js'   => 'application/javascript',
    'json' => 'application/json',
    'xml'  => 'application/xml',
    'swf'  => 'application/x-shockwave-flash',
    'flv'  => 'video/x-flv',

    // images
    'png'  => 'image/png',
    'jpe'  => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'jpg'  => 'image/jpeg',
    'gif'  => 'image/gif',
    'bmp'  => 'image/bmp',
    'ico'  => 'image/vnd.microsoft.icon',
    'tiff' => 'image/tiff',
    'tif'  => 'image/tiff',
    'svg'  => 'image/svg+xml',
    'svgz' => 'image/svg+xml',

    // archives
    'zip'  => 'application/zip',
    'rar'  => 'application/x-rar-compressed',
    'exe'  => 'application/x-msdownload',
    'msi'  => 'application/x-msdownload',
    'cab'  => 'application/vnd.ms-cab-compressed',

    // audio/video
    'mp3'  => 'audio/mpeg',
    'qt'   => 'video/quicktime',
    'mov'  => 'video/quicktime',

    // adobe
    'pdf'  => 'application/pdf',
    'psd'  => 'image/vnd.adobe.photoshop',
    'ai'   => 'application/postscript',
    'eps'  => 'application/postscript',
    'ps'   => 'application/postscript',

    // ms office
    'doc'  => 'application/msword',
    'rtf'  => 'application/rtf',
    'xls'  => 'application/vnd.ms-excel',
    'ppt'  => 'application/vnd.ms-powerpoint',

    // open office
    'odt'  => 'application/vnd.oasis.opendocument.text',
    'ods'  => 'application/vnd.oasis.opendocument.spreadsheet',
);

class Uploads{

    public function uploadFile(array $file, string $dossier = '', array &$errors, string $folder = UPLOADS_DIR, array $fileExtensions = FILE_EXT_IMG) {

        // Retrieve error and valid messages
        $errorMessages = (new \Models\ErrorMessages())->getMessages();

        $filename = '';

        // We get the file extension to check if it is in $fileExtensions
        $tmpNameArray = explode(".", $file["name"]);
        $tmpExt = end($tmpNameArray);

        if ($file["error"] === UPLOAD_ERR_OK) {
            $tmpName = $file["tmp_name"];

            if(in_array($tmpExt,$fileExtensions)) {
                $filename = uniqid().'-'.basename($file["name"]);
                
                }
                // "mime_content_type()" detects the content type of a file.
                // We check the file contents to see if it belongs to authorized MIMES.
                if(!in_array(mime_content_type($folder.$dossier."/".$filename), MIME_TYPES, true)) {
                    $errors[] = $errorMessages[19]; // The file was not saved correctly because its contents do not match its extension !
                }
                else{
                    if(!move_uploaded_file($tmpName, $folder.$dossier."/".$filename)) {
                    $errors[] = $errorMessages[18]; // The file was not saved correctly !
                }
            }
            else{
                $errors[] = $errorMessages[20]; // This type of file is not allowed !
            }
        }
        else if($file["error"] == UPLOAD_ERR_INI_SIZE || $file["error"] == UPLOAD_ERR_FORM_SIZE) {
            $errors[] = $errorMessages[21]; // File too large
        }
        else {
            $errors[] = $errorMessages[22]; // An error occurred while downloading the file!
        }
        return $filename;
    }
}