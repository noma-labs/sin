<?php

namespace Tests\Feature;

use App\Nomadelfia\Controllers\PopolazioneNomadelfiaController;
use Tests\TestCase;
use Carbon\Carbon;


class PopolazioneExportTest extends TestCase
{
    /** @test */
    public function it_can_export_popolazione_as_docx()
    {
        $response = $this
            ->post(action([PopolazioneNomadelfiaController::class, 'print']))
            ->assertSuccessful();

        $data = Carbon::now()->toDatestring();
        $file_name = "popolazione-$data.docx";
        $this->assertTrue($response->headers->get('content-disposition') == 'attachment; filename=' . $file_name);
    }

}