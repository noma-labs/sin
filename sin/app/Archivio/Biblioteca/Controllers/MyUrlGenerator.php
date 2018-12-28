<?php

namespace App\Biblioteca\Controllers;

use DateTimeInterface;
use Spatie\MediaLibrary\UrlGenerator\BaseUrlGenerator;

class MyUrlGenerator extends BaseUrlGenerator
{

    /*
     * Get the path for the profile of a media item.
     */
    public function getPath(): string
    {
         return "/FTP/sin/";
        //  return $this->getFTPPath();
    }

   /**
     * Get the url for a media item.
     *
     * @return string
     */
    public function getUrl(): string{
        return "";
    }

    /**
     * Get the temporary url for a media item.
     *
     * @param DateTimeInterface $expiration
     * @param array              $options
     *
     * @return string
     */
    public function getTemporaryUrl(DateTimeInterface $expiration, array $options = []): string{
        return "";
    }

    /**
     * Get the url to the directory containing responsive images.
     *
     * @return string
     */
    public function getResponsiveImagesDirectoryUrl(): string{
        return "";
    }

     /*
     * Get the path where the whole medialibrary is stored.
     */
    // protected function getFTPPath() : string
    // {
    //     $diskRootPath = $this->config->get("filesystems.disks.{$this->media->disk}.root");

    //     return realpath($diskRootPath);
    // }
}