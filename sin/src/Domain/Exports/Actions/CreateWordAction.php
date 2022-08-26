<?php

use Carbon\Carbon;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

class CreateWordAction
{

    public function execute(ToWord $file)
    {

        $wordfile = new PhpWord();

//        $w = $file->buildFileWord($wordfile);

        $objWriter = IOFactory::createWriter($wordfile, 'Word2007');

        $objWriter->save(storage_path($file->getFileName()));

    }



}