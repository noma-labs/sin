<?php

declare(strict_types=1);

namespace Tests\Photo\Http;

use App\Photo\Models\Photo;

use function Pest\Laravel\get;

it('renders breadcrumb with links for nested folder path', function (): void {
    Photo::factory()->inFolder('A/B/C')->create();
    Photo::factory()->inFolder('A/B/C')->create();

    login();

    $response = get(route('photos.folders.show', ['path' => 'A/B/C']));
    $response->assertSuccessful();

    // Root crumb
    $response->assertSee('Cartelle');

    // Segment labels
    $response->assertSee('A');
    $response->assertSee('B');
    $response->assertSee('C');

    // Segment links should point to their respective folders (A and A/B clickable, C active)
    $response->assertSee(route('photos.folders.show', ['path' => 'A']));
    $response->assertSee(route('photos.folders.show', ['path' => 'A/B']));
});

it('opens corresponding folder when breadcrumb segment is clicked', function (): void {
    // Create nested photos under A/B/C so A/B has a child folder C
    Photo::factory()->inFolder('A/B/C')->create();

    login();

    // Simulate clicking the B segment (navigating to A/B)
    $response = get(route('photos.folders.show', ['path' => 'A/B']));
    $response->assertSuccessful();

    // The page should list the immediate subfolder C as a card
    $response->assertSee('C');
    // And the link to navigate deeper to A/B/C should exist
    $response->assertSee(route('photos.folders.show', ['path' => 'A/B/C']));
});

it('shows top-level folders across entire dataset (ignoring filters on index)', function (): void {
    // Create photos under different top-level segments
    Photo::factory()->inFolder('a/b')->create();
    Photo::factory()->inFolder('a/d/e')->create();
    Photo::factory()->inFolder('b/x')->create();
    Photo::factory()->inFolder('c')->create();

    login();

    // Index ignores filters for folder cards
    get(route('photos.folders.index', ['name' => 'nonexistent-filter']))
        ->assertSuccessful()
        ->assertSee('a')
        ->assertSee('b')
        ->assertSee('c');
});

it('handles folder names with spaces and parentheses', function (): void {
    // Seed a photo in a folder path that contains spaces and parentheses
    Photo::factory()->inFolder('ARCH GEN 41-50  (idem c. s)/Arch 44b')->create();

    login();

    // Index should show the top-level folder card
    get(route('photos.folders.index'))
        ->assertSuccessful()
        ->assertSee('ARCH GEN 41-50  (idem c. s)');

    // Show page for the nested folder should render and display breadcrumb segments
    $response = get(route('photos.folders.show', ['path' => 'ARCH GEN 41-50  (idem c. s)/Arch 44b']));
    $response->assertSuccessful();
    $response->assertSee('ARCH GEN 41-50  (idem c. s)');
    $response->assertSee('Arch 44b');

    // The breadcrumb link to the parent segment should exist
    $response->assertSee(route('photos.folders.show', ['path' => 'ARCH GEN 41-50  (idem c. s)']));
});

it('paginates photos within a folder (grid and list views)', function (): void {
    // Create more than one page of photos in the same folder
    Photo::factory()->count(55)->inFolder('Paginated')->create();

    login();

    // Grid view
    $grid = get(route('photos.folders.show', ['path' => 'Paginated', 'view' => 'grid']));
    $grid->assertSuccessful();
    // Pagination controls should render (Bootstrap 5 pagination markup)
    $grid->assertSee('pagination');

    // List view
    $list = get(route('photos.folders.show', ['path' => 'Paginated', 'view' => 'list']));
    $list->assertSuccessful();
    $list->assertSee('pagination');
});
