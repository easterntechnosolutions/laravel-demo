<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

trait UploadTrait
{
    protected $s3;

    public static $diskName = "";

    public static function bootUploadTrait()
    {
        static::$diskName = config('s3.filesystem_driver');
    }

    /**
     * common function for get the tinify secret key of project.
     *
     * @param bool $tinifyKey - pass you custom key
     */
    public function setTinify($tinifyKey = false)
    {
        if (!$tinifyKey)
            \Tinify\Tinify::setKey(config('constants.tinify_key'));
        else
            \Tinify\Tinify::setKey($tinifyKey);

        $this->s3 = Storage::disk('s3');
    }

    /**
     * Upload in Bucket
     */
    public function uploadInBucket($image, $folder, $isImage)
    {
        $imagePath = $folder . str_replace(config('s3.aws_temporary_folder_name'), '', $image);
        $this->copyUploadFile($image, $imagePath, $isImage); // copy file from tmp location to our custom path
        return $imagePath;
    }

    /**
     * Upload In Local
     */
    public function uploadInLocal(UploadedFile $uploadedFile, $folder)
    { // $folder = target folder
        $ext = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_EXTENSION); // original file extension
        $file_name = User::getRandomString(40) . '.' . $ext; // generate filename with auto generate name and original file extension
        return $uploadedFile->storeAs($folder, $file_name); // saving method with custom generated filename with original file extension
    }

    public function deleteOne($path)
    {
        $path = str_replace(trim(Storage::url('/'), '/'), "", $path);
        if (Storage::exists($path)) {
            Storage::delete($path);
        }
    }

    public function is_file_exists($path)
    {
        if (!is_null($path) && Storage::exists($path)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     *  Optimize Image using Tinify
     *
     * @param $uploadedFile
     * @param $realPath
     * @param $width
     * @param $height
     * @param boolean $isImage
     * @return array
     */
    public function resizeImages($image, $realPath, $width, $height, $isImage = true)
    {
        $original = null;
        $thumbPath = null;
        $realPath = config('s3.excluded_s3_directories_in_listing.0') . '/' . $realPath;

        if (static::$diskName == 's3') { // In S3
            $path = $this->uploadInBucket($image, $realPath, $isImage);

            if ($isImage) {
                $thumbPath = $realPath . '/thumbs';

                $this->setTinify();
                $source = \Tinify\fromFile($this->s3->url($path));
                $source->store($this->storeInBucket($path));

                // Create thumb
                $resized = $source->resize($this->getTinifyThumbArray($width, $height));

                $resized->store($this->storeInBucket($thumbPath));
            }
        } else { // In local

            $path = $this->uploadInLocal($image, $realPath);

            if ($isImage) {
                $createThumb = public_path('storage/' . $realPath . '/thumbs/');

                if (!File::isDirectory($createThumb)) {
                    File::makeDirectory($createThumb, 0755, true, true);
                }

                $thumbPath = $realPath . '/thumbs/' . pathinfo($path, PATHINFO_BASENAME);

                $this->setTinify();
                $source   = \Tinify\fromFile('storage/' . $path);
                $source->toFile('storage/' . $path);

                // Create thumb
                $resized = $source->resize($this->getTinifyThumbArray($width, $height));
                $resized->toFile('storage/' . $thumbPath);
            }

            $original = $image->getClientOriginalName();
            $path = $realPath . '/' . pathinfo($path, PATHINFO_BASENAME);
        }

        $image_array['image'] = $path;
        $image_array['original'] = $original;
        $image_array['thumbnail'] = $thumbPath;
        $image_array['size'] = Storage::size($path);

        return $image_array;
    }

    /**
     * Get Tinify thumb array
     */
    public function getTinifyThumbArray($width, $height)
    {
        return [
            "method" => "fit",
            "width" => $width,
            "height" => $height
        ];
    }

    /**
     * Store in s3 bucket
     */
    public function storeInBucket($path)
    {
        return [
            "service" => "s3",
            "aws_access_key_id" => config('s3.aws_access_key_id'),
            "aws_secret_access_key" => config('s3.aws_secret_access_key'),
            "region" => config('s3.aws_default_region'),
            "headers" => array("Cache-Control" => "max-age=31536000, public"),
            "path" => config('s3.aws_bucket') . '/' . $path
        ];
    }

    /**
     * copy file from one location to another location.
     *
     * @param $from - copy from location
     * @param $to - put file on this location
     */
    public function copyUploadFile($from, $to, $isImage)
    {
        if (Storage::exists($to)) {
            Storage::delete($to);
        }
        Storage::copy($from, $to);

        if ($isImage) // only image file set public visibility
            Storage::setVisibility($to, 'public');  // set visibility on AWS S3 otherwise return "Access Denied" Error on file access.
    }
}
