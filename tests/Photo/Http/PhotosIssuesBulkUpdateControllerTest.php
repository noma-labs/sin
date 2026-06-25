<?php

declare(strict_types=1);

namespace Tests\Photo\Http;

use App\Photo\Controllers\PhotosIssuesBulkUpdateController;
use App\Photo\Models\DbfAll;
use App\Photo\Models\Photo;
use App\Photo\Models\PhotoIssue;

use function Pest\Laravel\get;
use function Pest\Laravel\put;

beforeEach(function (): void {
    PhotoIssue::query()->update(['resolved_at' => now()]);
});

it('renders the bulk update index page with grouped issues', function (): void {
    $strip = DbfAll::factory()->create(['datnum' => '01234', 'anum' => '01236']);
    $photo = Photo::factory()->create(['dbf_id' => $strip->id]);
    PhotoIssue::factory()->create(['photo_id' => $photo->id, 'issue_type' => 'not_yet_born']);

    login();

    get(action([PhotosIssuesBulkUpdateController::class, 'index']))
        ->assertSuccessful()
        ->assertSee('01234')
        ->assertSee($photo->file_name);
});

it('shows the strip date as a suggestion in the date input', function (): void {
    $strip = DbfAll::factory()->create([
        'datnum' => '00001',
        'anum' => '00001',
        'data' => '1990-06-15',
    ]);
    $photo = Photo::factory()->create(['dbf_id' => $strip->id]);
    PhotoIssue::factory()->create(['photo_id' => $photo->id]);

    login();

    get(action([PhotosIssuesBulkUpdateController::class, 'index']))
        ->assertSuccessful()
        ->assertSee('1990-06-15');
});

it('shows an empty state when there are no open issues', function (): void {
    login();

    get(action([PhotosIssuesBulkUpdateController::class, 'index']))
        ->assertSuccessful()
        ->assertSee('Nessun problema rilevato');
});

it('does not show resolved issues on the bulk index', function (): void {
    $strip = DbfAll::factory()->create();
    $photo = Photo::factory()->create(['dbf_id' => $strip->id]);
    PhotoIssue::factory()->resolved()->create(['photo_id' => $photo->id]);

    login();

    get(action([PhotosIssuesBulkUpdateController::class, 'index']))
        ->assertSuccessful()
        ->assertSee('Nessun problema rilevato');
});

it('paginates by datnum and issue_type group', function (): void {
    $stripA = DbfAll::factory()->create(['datnum' => '00100', 'anum' => '00102']);
    $stripB = DbfAll::factory()->create(['datnum' => '00200', 'anum' => '00202']);

    PhotoIssue::factory()->create([
        'photo_id' => Photo::factory()->create(['dbf_id' => $stripA->id])->id,
        'issue_type' => 'not_yet_born',
    ]);
    PhotoIssue::factory()->create([
        'photo_id' => Photo::factory()->create(['dbf_id' => $stripB->id])->id,
        'issue_type' => 'already_deceased',
    ]);

    login();

    // Page 1 shows first datnum group
    get(action([PhotosIssuesBulkUpdateController::class, 'index'], ['page' => 1]))
        ->assertSuccessful()
        ->assertSee('00100');

    // Page 2 shows second datnum group
    get(action([PhotosIssuesBulkUpdateController::class, 'index'], ['page' => 2]))
        ->assertSuccessful()
        ->assertSee('00200');
});

it('produces separate groups for same datnum with different issue types', function (): void {
    $strip = DbfAll::factory()->create(['datnum' => '00100', 'anum' => '00102']);

    PhotoIssue::factory()->create([
        'photo_id' => Photo::factory()->create(['dbf_id' => $strip->id])->id,
        'issue_type' => 'not_yet_born',
    ]);
    PhotoIssue::factory()->create([
        'photo_id' => Photo::factory()->create(['dbf_id' => $strip->id])->id,
        'issue_type' => 'already_deceased',
    ]);

    login();

    get(action([PhotosIssuesBulkUpdateController::class, 'index']))
        ->assertSuccessful()
        // Two groups total for same datnum
        ->assertSee('1') // currentPage
        ->assertSee('2'); // lastPage
});

it('groups issues by datnum even when stripes have different ids', function (): void {
    $firstStripe = DbfAll::factory()->create(['datnum' => '00999', 'anum' => '01000']);
    $secondStripe = DbfAll::factory()->create(['datnum' => '00999', 'anum' => '01001']);

    PhotoIssue::factory()->create([
        'photo_id' => Photo::factory()->create(['dbf_id' => $firstStripe->id])->id,
        'issue_type' => 'already_deceased',
    ]);
    PhotoIssue::factory()->create([
        'photo_id' => Photo::factory()->create(['dbf_id' => $secondStripe->id])->id,
        'issue_type' => 'already_deceased',
    ]);

    login();

    get(action([PhotosIssuesBulkUpdateController::class, 'index']))
        ->assertSuccessful()
        ->assertSeeText('striscia 1 di')
        ->assertDontSeeText('striscia 1 di 2');
});

it('bulk updates taken_at for all selected photos', function (): void {
    $strip = DbfAll::factory()->create();
    $photoA = Photo::factory()->create(['dbf_id' => $strip->id, 'taken_at' => '1980-01-01']);
    $photoB = Photo::factory()->create(['dbf_id' => $strip->id, 'taken_at' => '1981-01-01']);
    $issueA = PhotoIssue::factory()->create(['photo_id' => $photoA->id]);
    $issueB = PhotoIssue::factory()->create(['photo_id' => $photoB->id]);

    login();

    put(action([PhotosIssuesBulkUpdateController::class, 'bulkUpdate']), [
        'taken_at' => '1985-06-20',
        'issue_ids' => [$issueA->id, $issueB->id],
    ])->assertRedirect();

    expect($photoA->fresh()->taken_at->format('Y-m-d'))->toBe('1985-06-20');
    expect($photoB->fresh()->taken_at->format('Y-m-d'))->toBe('1985-06-20');
});

it('sets the same resolved_at timestamp for all issues resolved in a bulk update', function (): void {
    $strip = DbfAll::factory()->create();
    $issueA = PhotoIssue::factory()->create([
        'photo_id' => Photo::factory()->create(['dbf_id' => $strip->id])->id,
    ]);
    $issueB = PhotoIssue::factory()->create([
        'photo_id' => Photo::factory()->create(['dbf_id' => $strip->id])->id,
    ]);

    login();

    put(action([PhotosIssuesBulkUpdateController::class, 'bulkUpdate']), [
        'taken_at' => '1985-06-20',
        'issue_ids' => [$issueA->id, $issueB->id],
    ])->assertRedirect();

    $resolvedIssueA = $issueA->fresh();
    $resolvedIssueB = $issueB->fresh();

    expect($resolvedIssueA->resolved_at)->not->toBeNull();
    expect($resolvedIssueB->resolved_at)->not->toBeNull();
    expect($resolvedIssueA->resolved_at?->format('Y-m-d H:i:s.u'))->toBe($resolvedIssueB->resolved_at?->format('Y-m-d H:i:s.u'));
});

it('appends old and new taken_at to the issue note after bulk update', function (): void {
    $photo = Photo::factory()->create(['taken_at' => '1980-03-10']);
    $issue = PhotoIssue::factory()->create(['photo_id' => $photo->id, 'note' => null]);

    login();

    put(action([PhotosIssuesBulkUpdateController::class, 'bulkUpdate']), [
        'taken_at' => '1985-06-20',
        'issue_ids' => [$issue->id],
    ])->assertRedirect();

    $updated = $issue->fresh();
    expect($updated->note)->toContain('old_taken_at=1980-03-10');
    expect($updated->note)->toContain('new_taken_at=1985-06-20');
});

it('requires taken_at for bulk update', function (): void {
    $issue = PhotoIssue::factory()->create();

    login();

    put(action([PhotosIssuesBulkUpdateController::class, 'bulkUpdate']), [
        'issue_ids' => [$issue->id],
    ])->assertSessionHasErrors('taken_at');
});

it('requires at least one issue_id for bulk update', function (): void {
    login();

    put(action([PhotosIssuesBulkUpdateController::class, 'bulkUpdate']), [
        'taken_at' => '1985-06-20',
        'issue_ids' => [],
    ])->assertSessionHasErrors('issue_ids');
});
