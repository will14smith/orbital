<?php

namespace AppBundle\Services\Importing;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageUploader
{
    /** @var string */
    private $upload_path;

    /**
     * @param string $upload_path
     */
    public function __construct($upload_path)
    {
        $this->upload_path = $upload_path;
    }

    /**
     * @param UploadedFile $file
     *
     * @return string Uploaded file name (rel to upload path)
     */
    public function persist(UploadedFile $file)
    {
        $raw_img = file_get_contents($file->getRealPath());
        $img = imagecreatefromstring($raw_img);

        $uniq_time = explode(' ', microtime());

        $file_name = $uniq_time[1] . substr($uniq_time[0], 2) . '.png';
        $path = $this->upload_path . DIRECTORY_SEPARATOR . $file_name;

        // preserve transparency
        imagealphablending($img, false);
        imagesavealpha($img, true);

        imagepng($img, $path);
        imagedestroy($img);

        return $file_name;
    }
}
