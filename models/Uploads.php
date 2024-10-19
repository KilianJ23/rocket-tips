<?php

// Destination path of uploaded files
// const UPLOADS_DIR = 'public/uploads/';
const UPLOADS_DIR = 'assets/img/'; /*'assets/img/';*/

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
    'webp' => 'image/webp',
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

    
    public function uploadFile(array $file, string $dossier = '', string $route, string $folder = UPLOADS_DIR, array $fileExtensions = FILE_EXT_IMG) {

    $filename = '';

    // We get the file extension to check if it is in $fileExtensions
    $tmpNameArray = explode(".", $file["name"]);
    $tmpExt = end($tmpNameArray);

    if ($file["error"] === UPLOAD_ERR_OK) {
        $tmpName = $file["tmp_name"];

        // Check if the file extension is allowed
        if (in_array($tmpExt, $fileExtensions)) {
            $filename = uniqid() . '-' . basename($file["name"]);
            /*$filename = $dossier . '/' . basename($file["name"]);*/

            // Verify MIME type
            if (!in_array(mime_content_type($tmpName), MIME_TYPES, true)) {
                $_SESSION['error_message'][] = "L'extension du fichier ne correspond pas à son type."; // The file was not saved correctly because its contents do not match its extension!
                $this->redirect($route);
                
            } else {
                // Move the file to the destination folder
                if (!move_uploaded_file($tmpName, $folder . $dossier . "/" . $filename)) {
                    $_SESSION['error_message'][] = "Le fichier n'a pas pu être enregistré."; // The file was not saved correctly!
                    $this->redirect($route);
                }
            }
        } else {
            $_SESSION['error_message'][] = "Ce format de fichier n'est pas supporté"; // This type of file is not allowed!
            $this->redirect($route);
        }
        
    } else if ($file["error"] == UPLOAD_ERR_INI_SIZE || $file["error"] == UPLOAD_ERR_FORM_SIZE) {
        $_SESSION['error_message'][] = "Le fichier est trop large"; // File too large
        $this->redirect($route);
        
    } else {
        $_SESSION['error_message'][] = "Une erreur s'est produite lors du téléchargement du fichier."; // An error occurred while downloading the file!
        $this->redirect($route);
    }

    return $folder . $dossier . "/" . $filename;
}

    
}