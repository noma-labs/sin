<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Domain\Photo\Models\ExifData;
use Domain\Photo\Models\Photo;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ConvertExifCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'exif:convert';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    // exiftool -r -n -a -struct -progress1000 -G1 -file:all  -exif:all -iptc:all -ImageDataHash -json>2000.json 2000
    public function handle()
    {
        //        $files = ['2000.json', '2001.json', '2002.json', '2003.json', '2004.json', '2005.json', '2006.json', '2007.json', '2008.json', '2009.json', '2010.json', '2011.json', '2013.json', '2014.json'];
        $files = ['2015.json', '2016.json', '2017.json', '2018.json', '2019.json', '2020.json', '2021.json', '2022.json', '2023.json'];
        foreach ($files as $f) {
            $photos = json_decode(file_get_contents(storage_path().'/'.$f), true);

            $raw = collect([]);
            foreach ($photos as $photo) {
                $exif = new ExifData();

                $exif->sourceFile = $photo['SourceFile'];
                $exif->fileName = $photo['System:FileName'];
                $exif->directory = $photo['System:Directory'];
                $exif->fileSize = $photo['System:FileSize'];

                if (! isset($photo['File:FileType'])) {
                    $this->warn('File type missing '.$exif->sourceFile);

                    continue;
                }
                $exif->fileType = $photo['File:FileType'];
                $exif->fileExtension = $photo['File:FileTypeExtension'];
                if ($exif->fileType == 'MPEG') {
                    $this->warn('MPEG video skipped: '.$exif->sourceFile);

                    continue;
                }
                if (isset($photo['File:ImageWidth'])) {
                    $exif->imageWidth = $photo['File:ImageWidth'];
                }
                if (isset($photo['File:ImageHeight'])) {
                    $exif->imageHeight = $photo['File:ImageHeight'];
                }
                $exif->folderTitle = Str::of($exif->directory)->basename();

                if (! isset($photo['File:ImageDataHash'])) {
                    $this->warn('Photo with missing File:ImageDataHash '.$exif->sourceFile);

                    continue;
                }
                $exif->sha = $photo['File:ImageDataHash'];

                // TODO: if taken at is missing ?
                // TODO: manage the timezone
                if (isset($photo['CreateDate'])) {
                    $exif->takenAt = Carbon::parse($photo['CreateDate']);
                }
                if (isset($photo['Subject'])) {
                    $exif->subjects = $photo['Subject'];
                }
                if (isset($photo['RegionInfo'])) {
                    $exif->regionInfo = json_encode($photo['RegionInfo']);
                }
                $raw->push($exif);
            }
            $chunks = $raw->chunk(100);
            foreach ($chunks as $chunk) {
                $attrs = [];
                foreach ($chunk as $r) {
                    array_push($attrs, [
                        'uid' => uniqid(),
                        'sha' => $r->sha,
                        'source_file' => $r->sourceFile,
                        'subject' => implode(',', $r->subjects),
                        'folder_title' => $r->folderTitle,
                        'file_size' => $r->fileSize,
                        'file_name' => $r->fileName,
                        'file_type' => $r->fileType,
                        'file_type_extension' => $r->fileExtension,
                        'image_height' => $r->imageHeight,
                        'image_width' => $r->imageWidth,
                        'taken_at' => $r->takenAt,
                        'directory' => $r->directory,
                        'region_info' => $exif->regionInfo,
                    ]);
                }
                Photo::insert($attrs);
            }
        }

        return Command::SUCCESS;

    }
}
