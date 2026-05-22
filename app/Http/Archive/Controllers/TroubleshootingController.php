<?php

declare(strict_types=1);

namespace App\Archive\Controllers;

use App\Archive\Models\Recording;
use App\Archive\Models\RecordingTranscript;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class TroubleshootingController
{
    public function index()
    {
        $page = request('page', 1);
        $orphanedTranscripts = RecordingTranscript::whereNull('recording_id')
            ->orderBy('code')
            ->paginate(1, ['*'], 'page', $page);

        // For the current transcript, find matching recordings
        $matchingRecordings = collect();
        if ($orphanedTranscripts->count() > 0) {
            $transcript = $orphanedTranscripts->first();
            $dateStr = $this->extractDateFromCode($transcript->code);
            if ($dateStr) {
                $matchingRecordings = Recording::whereRaw("DATE_FORMAT(DATA, '%Y-%m') = ?", [$dateStr])
                    ->orderBy('DATA')
                    ->get(['id', 'code', 'DATA', 'ORE', 'AUTORE', 'ARGOMENTO', 'GENERE', 'LOCALITA\'', 'DESTINATARI']);
            }
        }

        return view('archive.troubleshooting', [
            'orphanedTranscripts' => $orphanedTranscripts,
            'matchingRecordings' => $matchingRecordings,
        ]);
    }

    public function assign(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'transcript_id' => ['required', 'integer'],
            'recording_id' => ['required', 'integer'],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);

        $transcript = RecordingTranscript::query()->findOrFail((int) $validated['transcript_id']);

        $recordingExists = Recording::query()
            ->whereKey((int) $validated['recording_id'])
            ->exists();

        if (! $recordingExists) {
            return back()
                ->withErrors(['recording_id' => 'Recording non trovato'])
                ->withInput();
        }

        $transcript->recording_id = (int) $validated['recording_id'];
        $transcript->save();

        return redirect()
            ->route('archive.troubleshooting', ['page' => (int) ($validated['page'] ?? 1)])
            ->with('status', 'Recording assegnato con successo');
    }

    private function extractDateFromCode(string $code): ?string
    {
        // Extract first 6 characters: YYYYMM format
        if (strlen($code) < 6) {
            return null;
        }

        $year = substr($code, 0, 4);
        $month = substr($code, 4, 2);

        return $year . '-' . $month;
    }
}
