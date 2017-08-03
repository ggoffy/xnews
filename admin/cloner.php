<?php
/**
 * Module Cloner file
 *
 * Enable webmasters to clone the news module.
 *
 * NOTE : Please give credits if you copy this code !
 *
 * @package       News
 * @author        DNPROSSI
 * @copyright (c) DNPROSSI
 */

function nw_cloneFileFolder($path, $patterns)
{
    $patKeys   = array_keys($patterns);
    $patValues = array_values($patterns);

    // work around for PHP < 5.0.x
    if (!function_exists('file_put_contents')) {
        function file_put_contents($filename, $data, $file_append = false)
        {
            $fp = fopen($filename, (!$file_append ? 'w+' : 'a+'));
            if (!$fp) {
                trigger_error('file_put_contents cannot write in file.', E_USER_ERROR);

                return;
            }
            fputs($fp, $data);
            fclose($fp);
        }
    }

    $newpath = str_replace($patKeys[0], $patValues[0], $path);

    if (is_dir($path)) {
        // create new dir
        if (!is_dir($newpath)) {
            mkdir($newpath);
        }
        // check all files in dir, and process them
        if ($handle = opendir($path)) {
            while ($file = readdir($handle)) {
                if ($file != '.' && $file != '..') {
                    nw_cloneFileFolder("$path/$file", $patterns);
                }
            }
            closedir($handle);
        }
    } else {
        //trigger_error('in else', E_USER_ERROR);
        if (preg_match('/(.jpg|.gif|.png|.zip)$/i', $path)) {
            copy($path, $newpath);
            @chmod($newpath, 0755);
        } else {
            $path_info = pathinfo($path);
            $path_ext  = $path_info['extension'];
            //trigger_error($path . " -------- " . $path_ext, E_USER_WARNING);
            //trigger_error($path , E_USER_WARNING);
            $content = file_get_contents($path);
            if ($path_ext != 'txt') {
                for ($i = 0; $i < sizeof($patterns); ++$i) {
                    $content = str_replace($patKeys[$i], $patValues[$i], $content);
                }
            }
            file_put_contents($newpath, $content);
            @chmod($newpath, 0755);
            //trigger_error($path. ' ---- ' .$newpath , E_USER_WARNING);
        }
    }
}

//DNPROSSI
function nw_clonefilename($path, $old_subprefix, $new_subprefix)
{
    for ($i = 0; $i <= 1; $i++) {
        if ($handle = opendir($path[$i])) {
            while ($file = readdir($handle)) {
                if ($file != '.' && $file != '..') {
                    $newfilename = str_replace($old_subprefix, $new_subprefix, $file);
                    @rename($path[$i] . $file, $path[$i] . $newfilename);
                }
            }
            closedir($handle);
        }
    }
}

//DNPROSSI
function nw_deleteclonefile($path, $new_subprefix)
{
    for ($i = 0; $i <= 1; $i++) {
        if ($handle = opendir($path[$i])) {
            while ($file = readdir($handle)) {
                if ($file != '.' && $file != '..') {
                    $pos = strpos($file, $new_subprefix);
                    if ($pos !== false) {
                        //trigger_error($file. ' ---- Deleted' , E_USER_WARNING);
                        @unlink($path[$i] . $file);
                    }
                }
            }
            closedir($handle);
        }
    }
}

//DNPROSSI
function nw_clonecopyfile($srcpath, $destpath, $filename)
{
    if ($handle = opendir($srcpath)) {
        if ($filename == '') {
            while ($file = readdir($handle)) {
                if ($file != '.' && $file != '..') {
                    @copy($srcpath . $file, $destpath . $file);
                }
            }
        } else {
            if (file_exists($srcpath . $filename)) {
                @copy($srcpath . $filename, $destpath . $filename);
            }
        }
        closedir($handle);
    }
}

// ------------ recursive PHP functions -------------
// recursive_remove_directory( directory to delete, empty )
// expects path to directory and optional true / false to empty
// of course PHP has to have the rights to delete the directory
// you specify and all files and folders inside the directory
// ------------------------------------------------------------

// to use this function to totally remove a directory, write:
// nw_removewholeclone('path/to/directory/to/delete');

// to use this function to empty a directory, write:
// nw_removewholeclone('path/to/full_directory', true);

function nw_removewholeclone($directory, $empty = false)
{
    // if the path has a slash at the end we remove it here
    if (substr($directory, -1) == '/') {
        $directory = substr($directory, 0, -1);
    }

    // if the path is not valid or is not a directory ...
    if (!file_exists($directory) || !is_dir($directory)) {
        // ... we return false and exit the function
        return false;
        // ... if the path is not readable
    } elseif (!is_readable($directory)) {
        // ... we return false and exit the function
        return false;
        // ... else if the path is readable
    } else {
        // we open the directory
        $handle = opendir($directory);
        // and scan through the items inside
        while (false !== ($item = readdir($handle))) {
            // if the filepointer is not the current directory
            // or the parent directory
            if ($item != '.' && $item != '..') {
                // we build the new path to delete
                $path = $directory . '/' . $item;
                // if the new path is a directory
                if (is_dir($path)) {
                    // we call this function with the new path
                    nw_removewholeclone($path);
                    // if the new path is a file
                } else {
                    // we remove the file
                    @unlink($path);
                }
            }
        }
        // close the directory
        closedir($handle);
        // if the option to empty is not set to true
        if ($empty === false) {
            // try to delete the now empty directory
            if (!rmdir($directory)) {
                // return false if not possible
                return false;
            }
        }

        // return success
        return true;
    }
}