<?php

use Slim\App;
use WebSK\Config\ConfWrapper;
use WebSK\DB\DBWrapper;
use WebSK\Skif\SkifServiceProvider;
use WebSK\Slim\Facade;

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/events.php';

// fix display non-latin chars correctly
// https://github.com/servocoder/RichFilemanager/issues/7
setlocale(LC_CTYPE, 'en_US.UTF-8');

// fix for undefined timezone in php.ini
// https://github.com/servocoder/RichFilemanager/issues/43
if(!ini_get('date.timezone')) {
    date_default_timezone_set('GMT');
}

$config = require_once realpath(__DIR__ . '/../../../config/config.php');
ConfWrapper::setConfig($config['settings']);

$app = new App($config);
Facade::setFacadeApplication($app);

$container = $app->getContainer();
SkifServiceProvider::register($container);

/** Set DBWrapper db service */
DBWrapper::setDbService(SkifServiceProvider::getDBService($container));


// This function is called for every server connection. It must return true.
//
// Implement this function to authenticate the user, for example to check a
// password login, or restrict client IP address.
//
// This function only authorizes the user to connect and/or load the initial page.
// Authorization for individual files or dirs is provided by the two functions below.
//
// NOTE: If using session variables, the session must be started first (session_start()).
function fm_authenticate()
{
    // Customize this code as desired.
    return true;

    // If this function returns false, the user will just see an error.
    // If this function returns an array with "redirect" key, the user will be redirected to the specified URL:
    // return ['redirect' => 'http://domain.my/login'];
}


// This function is called before any filesystem read operation, where
// $filepath is the file or directory being read. It must return true,
// otherwise the read operation will be denied.
//
// Implement this function to do custom individual-file permission checks, such as
// user/group authorization from a database, or session variables, or any other custom logic.
//
// Note that this is not the only permissions check that must pass. The read operation
// must also pass:
//   * Filesystem permissions (if any), e.g. POSIX `rwx` permissions on Linux
//   * The $filepath must be allowed according to config['patterns'] and config['extensions']
//
function fm_has_read_permission($filepath)
{
    // Customize this code as desired.
    return true;
}


// This function is called before any filesystem write operation, where
// $filepath is the file or directory being written to. It must return true,
// otherwise the write operation will be denied.
//
// Implement this function to do custom individual-file permission checks, such as
// user/group authorization from a database, or session variables, or any other custom logic.
//
// Note that this is not the only permissions check that must pass. The write operation
// must also pass:
//   * Filesystem permissions (if any), e.g. POSIX `rwx` permissions on Linux
//   * The $filepath must be allowed according to config['patterns'] and config['extensions']
//   * config['read_only'] must be set to false, otherwise all writes are disabled
//
function fm_has_write_permission($filepath)
{
    // Customize this code as desired.
    if (\WebSK\Auth\Auth::currentUserIsAdmin()) {
        return true;
    }

    return false;
}

$config = [
    /**
     * Configure Logger class
     */
    "logger" => [
        "enabled" => true,
        /**
         * Default value "null".
         * Full path to log file, e.g. "/var/log/filemanager/logfile".
         * By default the application writes logs to "filemanager.log" file that located at sys_get_temp_dir()
         */
        "file" => null,
    ],
    /**
     * General options section
     */
    "options" => [
        /**
         * Default value "true".
         * By default the application will search `fileRoot` folder under server root folder.
         * Set value to "false" in case the `fileRoot` folder located outside server root folder.
         * If `fileRoot` options is set to "false", `serverRoot` value is ignored - always "true".
         */
        "serverRoot" => false,
        /**
         * Default value "false". Path to the user storage folder.
         * By default the application will determine the path itself based on $_SERVER['DOCUMENT_ROOT'].
         * You can set specific path to user storage folder with the following rules:
         * - absolute path in case `serverRoot` set to "false", e.g. "/var/www/html/filemanager/userfiles/"
         * - relative path in case `serverRoot` set to "true", e.g. "/filemanager/userfiles/"
         */
        "fileRoot" => '/var/www/lib.muctr.ru/public/files/',
        /**
         * The maximum allowed root folder total size (in Bytes). If set to "false", no size limitations applied.
         */
        "fileRootSizeLimit" => false,
        /**
         * Default value "false". Deny non-latin characters in file/folder names.
         * PHP requires INTL extension installed, otherwise all non-latin characters will be stripped.
         */
        "charsLatinOnly" => false,
    ],
    /**
     * Security section
     */
    "security" => [
        /**
         * Default value "false". Allow write operations.
         * Set value to "true" to disable all modifications to the filesystem, including thumbnail generation.
         */
        "readOnly" => false,
        /**
         * Default value "true".
         * Sanitize file/folder name, replaces gaps and some other special chars.
         */
        "normalizeFilename" => true,
        /**
         * Filename extensions are compared against this list, after the right-most dot '.'
         * Matched files will be filtered from listing results, and will be restricted from all file operations (both read and write).
         */
        "extensions" => [
            /**
             * Default value "ALLOW_LIST". Takes value "ALLOW_LIST" / "DISALLOW_LIST".
             * If is set to "ALLOW_LIST", only files with extensions that match `restrictions` list will be allowed, all other files are forbidden.
             * If is set to "DISALLOW_LIST", all files are allowed except of files with extensions that match `restrictions` list.
             */
            "policy" => "ALLOW_LIST",
            /**
             * Default value "true".
             * Whether extension comparison should be case sensitive.
             */
            "ignoreCase" => true,
            /**
             * List of allowed / disallowed extensions, depending on the `policy` value.
             * To allow / disallow files without extension, add / remove the empty string "" to / from this list.
             */
            "restrictions" => [
                "",
                "jpg",
                "jpe",
                "jpeg",
                "gif",
                "png",
                "svg",
                "txt",
                "pdf",
                "odp",
                "ods",
                "odt",
                "rtf",
                "doc",
                "docx",
                "xls",
                "xlsx",
                "ppt",
                "pptx",
                "csv",
                "ogv",
                "avi",
                "mkv",
                "mp4",
                "webm",
                "m4v",
                "ogg",
                "mp3",
                "wav",
                "zip",
                "md",
            ],
        ],
        /**
         * Files and folders paths relative to the user storage folder (see `fileRoot`) are compared against this list.
         * Matched items will be filtered from listing results, and will be restricted from all file operations (both read and write).
         */
        "patterns" => [
            /**
             * Default value "ALLOW_LIST". Takes value "ALLOW_LIST" / "DISALLOW_LIST".
             * If is set to "ALLOW_LIST", only files and folders that match `restrictions` list will be allowed, all other files are forbidden.
             * If is set to "DISALLOW_LIST", all files and folders are allowed except of ones that match `restrictions` list.
             */
            "policy" => "DISALLOW_LIST",
            /**
             * Default value "true".
             * Whether patterns comparison should be case sensitive.
             */
            "ignoreCase" => true,
            /**
             * List of allowed / disallowed patterns, depending on the `policy` value.
             */
            "restrictions" => [
                // files
                "*/.htaccess",
                "*/web.config",
                // folders
                "*/.CDN_ACCESS_LOGS/*",
            ],
        ],
        /**
         * Rules for symbolic links that point to files/folders OUTSIDE the `fileroot` folder.
         * Targets of symbolic links INSIDE the `fileroot` folder are allowed by default.
         */
        "symlinks" => [
            /**
             * Default value "false".
             * Allow to link ANY path when set to "true" - quite unsecure.
             * Target path will be restricted only by OS permissions.
             */
            "allowAll" => false,
            /**
             * List of files/folders that can be linked with symlinks.
             * All contents of listed folder are allowed to be linked as well.
             * Use absolute server paths.
             */
            "allowPaths" => [],
        ],
    ],
    /**
     * Upload section
     */
    "upload" => [
        /**
         * Default value "16000000" (16 MB).
         * The maximum allowed file size (in Bytes). If set to "false", no size limitations applied.
         * See https://github.com/blueimp/jQuery-File-Upload/wiki/Options#maxfilesize.
         */
        "fileSizeLimit" => 100000000,
        /**
         * Default value "false".
         * If set to "true" files will be overwritten on uploads if they have same names, otherwise an index will be added.
         */
        "overwrite" => false,
        /**
         * Upload parameter name, that is expected to contains uploaded file data - $_FILES[paramName].
         * Good usecase example is CKEditor image upload plugin, that sends files within "upload" name.
         */
        "paramName" => "upload",
    ],
    /**
     * Images section
     */
    "images" => [
        /**
         * Uploaded image settings.
         * To disable resize set both `maxWidth` and `maxHeight` to "false".
         */
        "main" => [
            /**
             * Default value "true".
             * Automatically rotate images based on EXIF meta data.
             */
            "autoOrient" => true,
            /**
             * Default value "1280".
             * Resize maximum width in pixels. Takes integer values or "false".
             */
            "maxWidth" => false,
            /**
             * Default value "1024".
             * Resize maximum height in pixels. Takes integer values or "false".
             */
            "maxHeight" => false,
        ],
        /**
         * Thumbnail creation settings of uploaded image.
         */
        "thumbnail" => [
            /**
             * Default value "true".
             * Generate thumbnails using PHP to increase performance on listing directory.
             */
            "enabled" => true,
            /**
             * Default value "true".
             * If set to "false", it will generate thumbnail each time the image is requested. Decreased performance.
             */
            "cache" => true,
            /**
             * Default value "_thumbs/".
             * Folder to store thumbnails, invisible via filemanager.
             * If you want to make it visible, just remove it from `excluded_dirs` configuration option.
             */
            "dir" => "_thumbs/",
            /**
             * Default value "true".
             * Crop thumbnails. Set dimensions below to create square thumbnails of a particular size.
             */
            "crop" => true,
            /**
             * Default value "64".
             * Maximum crop width in pixels.
             */
            "maxWidth" => 100,
            /**
             * Default value "64".
             * Maximum crop height in pixels.
             */
            "maxHeight" => 100,
        ]
    ],
    /**
     * Default mode while creating new folder.
     */
    "mkdir_mode" => 0755,
];

$app = new \RFM\Application();

// uncomment to use events
//$app->registerEventsListeners();

$local = new \RFM\Repository\Local\Storage($config);

// example to setup files root folder
//$local->setRoot('userfiles', true, true);

$app->setStorage($local);

// set application API
$app->api = new RFM\Api\LocalApi();

$app->run();