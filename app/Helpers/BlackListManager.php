<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use App\Models\BlackList as Blacklist;
use Illuminate\Http\Request;
use PhpParser\Node\Expr\Cast\String_;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use PhpOffice\PhpSpreadsheet\IOFactory;

class BlackListManager {

    const EXCEL_EXTENSIONS          = ['xls', 'xlsx'];
    const CONVERTED_XL_PATH         = 'app/public/blacklists/converted';
    const CSV_EXTENSION             = 'csv';
    const TXT_EXTENSION             = 'txt';
    const BLACKLIST_PATH            = 'app/public/blacklist/';
    const ENCRYPTION_KEY_VAL        = [
        'email'  => 0,
        'MD5'    => 1,
        'SHA1'   => 2,
        'SHA256' => 3
    ];
    const ENCRYPTION_COL_SEPARATOR  = ":";
    const ENCRYPTION_TYPE_SEPARATOR = "-";

    /**
     * create an array from $content to prepare to insert into new file
     *
     * @param string $content the content of the uploaded file
     * @return array
     */
    public function prepareContent($content): array
    {
        $content = explode("\n", $content);

        foreach($content as $key => $value) {
            $content[$key] = trim($value);
        }

        return $content;
    }

    /**
     * create a file and put content in it
     *
     * @param string $content
     * @param [type] $adv_id
     * @param [type] $ext
     * @return array
     */
    public function storeBlackList($content, $adv_id, $ext, $filename = false, $overwrite = false)
    {
        if(!$filename) { //if not given we have to create it
            $filename = sha1(rand(111111111, 999999999) . "_" . $adv_id) . '.' . $ext;
            Storage::disk('local')->put('public/blacklists/' . $filename, $content.PHP_EOL);
        } elseif(false !== $filename && !$overwrite) {
            Storage::disk('local')->append('public/blacklists/' . $filename, $content.PHP_EOL);
        } elseif($filename && $overwrite) {
            Storage::disk('local')->put('public/blacklists/' . $filename, $content.PHP_EOL);
        }

        return [Storage::path('public/blacklists/' . $filename), $filename];
    }

    public function checkSeparator($content)
    {
        if(strpos($content, ';') !== false) {
            return ';';
        } elseif(strpos($content, ',') !== false) {
            return ',';
        } elseif(strpos($content, ', ') !== false) {
            return ', ';
        } elseif(strpos($content, '|') !== false) {
            return '|';
        } elseif(strpos($content, '\t') !== false) {
            return '\t';
        } else {
            return ','; // Default, permit to handle alone columns
        }
    }

    public function manageEncryption($content, $separator)
    {
        
        $lineToCheck = (count($content) < 2) 
                        ? $content[0] 
                        : $content[1];

        $columns    = explode($separator, $lineToCheck);
        $encryption = "";
        $header     = "";
        
        foreach($columns as $key => $value) {
            $email = trim($value);
            
            if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $encryption .= $key . ":0-";
                $header     .= 'email,';
            }

            if(self::isValidMD5($email)) {
                $encryption .= $key . ":1-";
                $header     .= 'MD5,';
            }

            if(self::isValidSHA1($email)) {
                $encryption .= $key . ":2-";
                $header     .= 'SHA1,';
            }
           
            if(self::isValidSHA256($email)) {
                $encryption .= $key . ":3-";
                $header     .= 'SHA256,';
            }
        }
        $encryption = strval(rtrim($encryption, "-"));
        $header     = strval(rtrim($header, ","));
        $return     = [$encryption, $header];

        if(empty($encryption) || empty($header)) {
            return false;
        } else {
            return $return;
        }
    }

    /**
     * get all informations from $content
     *
     * @param array $content
     * @param string $delimiter
     * @return array
     */
    public function analyseFile($content, $delimiter) :array
    {
        $headers = $content[0];
        $headers = explode($delimiter, $headers);

        $nb_columns     = count($headers);
        $nb_lines       = count($content);
        $content_length = strlen(implode($content));
        $new_content    = implode(PHP_EOL, $content);

        return [$nb_columns, $nb_lines, $content_length, $new_content];
    }

    public function getFormattedNewLine(Blacklist $blacklist, $email, $hash) {
        $encryption      = $blacklist->encryption;
        $encryption_list = explode("-", $encryption);

        foreach($encryption_list as $key => $value) {
            $encryption_list [$key] = explode(":", $value);
            $encryption_order[]     = $encryption_list[$key][1];

            $new_line = "";
            
            foreach($encryption_order as $order) {
                if($order == $hash) {
                    $new_line .= $email . $blacklist->separator;
                } elseif($order == 0) {
                    $new_line .= $email . $blacklist->separator;
                } elseif($order == 1) {
                    $new_line .= md5($email) . $blacklist->separator;
                } elseif($order == 2) {
                    $new_line .= sha1($email) . $blacklist->separator;
                } elseif ($order == 3) {
                    $new_line .= hash('sha256',$email) . $blacklist->separator;
                }
            }
        }

        return rtrim($new_line, $blacklist->separator) . PHP_EOL;
    }

    public function findHash($entry) {
        if(strpos($entry, "@") !== false) {
            return 0;
        } elseif(self::isValidMD5($entry)) {
            return 1;
        } elseif(self::isValidSHA1($entry)) {
            return 2;
        } elseif(self::isValidSHA256($entry)) {
            return 3;
        } else {
            return false;
        }
    }

    public function isValidSHA1($s) {
        return (bool) preg_match("/^[a-fA-F0-9]{40}$/i", $s);
    }

    public function isValidMD5($s) {
        return (bool) preg_match("/^[a-fA-F0-9]{32}$/i", $s);
    }

    public function isValidSHA256($s) {
        return (bool) preg_match("/^[a-fA-F0-9]{64}$/i", $s);
    }

    /**
     * add new line to csv
     * 
     * @param string $filepath the file to modify
     * @param string $lineToAdd the new line to be added
     * @param Blacklist $blacklist the Blacklist associated with the file
     * @return bool
     */
    public function edit(string $filepath, string $lineToAdd, Blacklist $blacklist = null): bool
    {

        // if(file_put_contents($filepath, $lineToAdd.PHP_EOL, FILE_APPEND)) {
        if(file_put_contents($filepath, $lineToAdd, FILE_APPEND)) {
            if(null !== $blacklist) {
                $content = file_get_contents($filepath);
                $content = self::prepareContent($content);

                list($columns, $lines, $length, $content)  = self::analyseFile($content, $blacklist->separator);
                $blacklist->nb_columns     = $columns;
                $blacklist->nb_lines       = $lines;
                $blacklist->content_length = $length;
                $blacklist->save();

                return true;
            }

            return true;
        }

        return false;
    }

    public function changeFileExtension($askedExt, $originalExt, $filename)
    {
        $file          = public_path('storage/blacklists/'.$filename);
        $path          = Str::remove($filename, $file);
        $filenameNoExt = Str::remove($originalExt, $filename);
        $newFile       = $path.$filenameNoExt.$askedExt;

        return $newFile;
    }

    public function convertExcelToCsv(Request $request)
    {
        $filename = basename( $request->file->getClientOriginalName(), '.'.$request->file->extension() ).'.'.self::CSV_EXTENSION;
        File::ensureDirectoryExists(storage_path(self::CONVERTED_XL_PATH));

        $spreadsheet = IOFactory::load($request->file);
        $writer      = IOFactory::createWriter($spreadsheet, Str::ucfirst(self::CSV_EXTENSION));

        $writer->setSheetIndex(0);
        $writer->setDelimiter(',');
        $writer->setEnclosure('');
        $writer->save(storage_path(self::CONVERTED_XL_PATH.'/'.$filename));

        $fileContent = File::get(storage_path(self::CONVERTED_XL_PATH.'/'.$filename));

        return $fileContent;
    }
}