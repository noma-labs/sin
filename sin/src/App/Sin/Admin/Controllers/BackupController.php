<?php
namespace App\Admin\Controllers;

use App\Core\Controllers\BaseController as Controller;

use Alert;
use App\Http\Requests;
use Artisan;
use Log;
use Storage;

use Mail;

class BackupController extends Controller
{
    public function __construct()
    {
        // $this->middleware(['auth', 'isAdmin','isMaster']); //isAdmin middleware lets only users with a //specific permission permission to access these resources
    }


    public function index()
    {
        // Mail::raw('Sending emails with Mailgun and Laravel is easy!', function($message)
        // {
        // 	$message->to('davideneri18@gmail.com');
        // });

        // Log::info('Showing user profile for user: ');
        $disk = Storage::disk(config('backup.backup.destination.disks')[0]);
        $files = $disk->files(config('backup.backup.name'));
        $backups = [];
        // make an array of backup files, with their filesize and creation date
        foreach ($files as $k => $f) {
            // only take the zip files into account
            if (substr($f, -4) == '.zip' && $disk->exists($f)) {
                $backups[] = [
                    'file_path' => $f,
                    'file_name' => str_replace(config('backup.backup.name') . '/', '', $f),
                    'file_size' => $disk->size($f),
                    'last_modified' => $disk->lastModified($f),
                ];
            }
        }
        // reverse the backups, so the newest one would be on top
        $backups = array_reverse($backups);
        return view("admin.backup.backups")->with(compact('backups'));
    }
    public function create()
    {
        ini_set('max_execution_time', 300); // aumenta il numero di tempo per eseguire la query
        try {
            // start the backup process
            $exitCode = Artisan::call('backup:run');
            $output = Artisan::output();
            // log the results
            Log::info("Backpack\BackupManager -- new backup started from admin interface \r\n" . $output);
            // return the results as a response to the ajax call
            return redirect()->back()->withSuccess("Backup creato con successo.");
        } catch (Exception $e) {
            return redirect()->back()->withError($e->getMessage());
        }
    }
    /**
     * Downloads a backup zip file.
     *
     * TODO: make it work no matter the flysystem driver (S3 Bucket, etc).
     */
    public function download($file_name)
    {
        $file = config('backup.backup.name') . '/' . $file_name;
        $disk = Storage::disk(config('backup.backup.destination.disks')[0]);
        if ($disk->exists($file)) {
            $fs = Storage::disk(config('backup.backup.destination.disks')[0])->getDriver();
            $stream = $fs->readStream($file);
            return \Response::stream(function () use ($stream) {
                fpassthru($stream);
            }, 200, [
                "Content-Type" => $fs->getMimetype($file),
                "Content-Length" => $fs->getSize($file),
                "Content-disposition" => "attachment; filename=\"" . basename($file) . "\"",
            ]);
        } else {
            abort(404, "Il file di backup non esiste.");
        }
    }
    /**
     * Deletes a backup file.
     */
    public function delete($file_name)
    {
        $disk = Storage::disk(config('backup.backup.destination.disks')[0]);
        // dd(config('backup.backup.name') . '/' . $file_name);
        if ($disk->exists(config('backup.backup.name') . '\\' . $file_name)) {
            $disk->delete(config('backup.backup.name') . '\\' . $file_name);
            return redirect()->back()->withSuccess("Backup $file_name eliminato");
        } else {
            abort(404, "Il file di backup non esiste.");
        }
    }
}
