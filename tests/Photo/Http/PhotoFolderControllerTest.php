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

    // Apply a filter that would exclude c if filters were used to build cards
    get(route('photos.folders.index', ['name' => 'nonexistent-filter']))
        ->assertSuccessful()
        ->assertSee('a')
        ->assertSee('b')
        ->assertSee('c');
});
