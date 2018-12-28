<?php

namespace App\Biblioteca\Controllers;

use Spatie\MediaLibrary\PathGenerator\PathGenerator;
use Spatie\MediaLibrary\Models\Media;


class MyPathGenerator implements PathGenerator
{
    /**
     * Get the path for the given media, relative to the root storage path.
     *
     * @param \Spatie\MediaLibrary\Models\Media $media
     *
     * @return string
     */
    public function getPath(Spatie\MediaLibrary\Models\Media $media)
    {
        return "/FTP/sin";
        // return md5($media->id).'/';
        // switch ($media)
        
        // case isitance(Libro):
        //         return "FTP/sin/biblioteca/libro"
        // }
    }

    /**
     * Get the path for conversions of the given media, relative to the root storage path.
     *
     * @param \Spatie\MediaLibrary\Models\Media $media
     *
     * @return string
     */
    public function getPathForConversions(Media $media)
    {
        return "";
    }

    /*
     * Get the path for responsive images of the given media, relative to the root storage path.
     *
     * @param \Spatie\MediaLibrary\Models\Media $media
     *
     * @return string
     */
    public function getPathForResponsiveImages(Media $media)
    {
        return "";
    }
}