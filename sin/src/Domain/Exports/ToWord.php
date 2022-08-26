<?php
namespace Domain\Exports;

use PhpOffice\PhpWord\PhpWord;

interface ToWord
{
    function getFileName(): string;

//    function buildFileWord(PhpWord $file): PhpWord;
}