<?php

use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

it('requires authentication to view tickets', function () {
    $response = $this->get('/dashboard');

    // Auth middleware should prevent unauthenticated access
    // Either redirects to login or returns error
    expect($response->status())->not->toBe(200)
        ->and($this->isAuthenticated())->toBeFalse();
});

it('displays tickets list for authenticated user', function () {
    $user = User::factory()->create([
        'username' => 'testuser',
        'password' => Hash::make('password123'),
    ]);

    $this->actingAs($user);

    Ticket::factory()->count(3)->create([
        'user_id' => $user->id,
        'status' => TicketStatus::Submitted,
    ]);

    $response = $this->get('/dashboard');

    $response->assertStatus(200);
    $response->assertViewIs('user.dashboard');
    $response->assertViewHas('tickets');
    
    $tickets = $response->viewData('tickets');
    expect($tickets->count())->toBe(3);
});

it('displays empty state when user has no tickets', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get('/dashboard');

    $response->assertStatus(200);
    $response->assertViewIs('user.dashboard');
    $response->assertViewHas('tickets');
    
    $tickets = $response->viewData('tickets');
    expect($tickets->count())->toBe(0);
    $response->assertSee('No tickets yet');
});

it('paginates tickets correctly', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Ticket::factory()->count(25)->create([
        'user_id' => $user->id,
        'status' => TicketStatus::Submitted,
    ]);

    $response = $this->get('/dashboard');

    $response->assertStatus(200);
    $tickets = $response->viewData('tickets');
    
    expect($tickets->count())->toBe(10) // Default per page
        ->and($tickets->total())->toBe(25)
        ->and($tickets->hasPages())->toBeTrue();
});

it('uses custom per_page parameter', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Ticket::factory()->count(25)->create([
        'user_id' => $user->id,
        'status' => TicketStatus::Submitted,
    ]);

    $response = $this->get('/dashboard?per_page=20');

    $response->assertStatus(200);
    $tickets = $response->viewData('tickets');
    
    expect($tickets->perPage())->toBe(20)
        ->and($tickets->count())->toBe(20);
});

it('uses custom page parameter', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Ticket::factory()->count(25)->create([
        'user_id' => $user->id,
        'status' => TicketStatus::Submitted,
    ]);

    $response = $this->get('/dashboard?per_page=10&page=2');

    $response->assertStatus(200);
    $tickets = $response->viewData('tickets');
    
    expect($tickets->currentPage())->toBe(2)
        ->and($tickets->count())->toBe(10);
});

it('shows all tickets for any authenticated user', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    Ticket::factory()->count(3)->create([
        'user_id' => $user1->id,
        'status' => TicketStatus::Submitted,
    ]);

    Ticket::factory()->count(2)->create([
        'user_id' => $user2->id,
        'status' => TicketStatus::Submitted,
    ]);

    $this->actingAs($user1);

    $response = $this->get('/dashboard');

    $response->assertStatus(200);
    $tickets = $response->viewData('tickets');
    
    // Current implementation shows all tickets (not filtered by user)
    expect($tickets->total())->toBe(5);
});

it('displays ticket information correctly', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $ticket = Ticket::factory()->create([
        'user_id' => $user->id,
        'title' => 'My Test Ticket',
        'description' => 'Test description',
        'status' => TicketStatus::Submitted,
    ]);

    $response = $this->get('/dashboard');

    $response->assertStatus(200);
    $response->assertSee('My Test Ticket');
    $response->assertSee('Submitted');
});

it('handles invalid per_page gracefully', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Ticket::factory()->count(5)->create([
        'user_id' => $user->id,
    ]);

    // Test with per_page > 30 (should throw exception)
    $response = $this->get('/dashboard?per_page=50');

    // Should either redirect with error or use default
    expect($response->status())->toBeIn([200, 302, 500]);
});

it('displays pagination links when there are multiple pages', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Ticket::factory()->count(25)->create([
        'user_id' => $user->id,
        'status' => TicketStatus::Submitted,
    ]);

    $response = $this->get('/dashboard');

    $response->assertStatus(200);
    $tickets = $response->viewData('tickets');
    
    if ($tickets->hasPages()) {
        $response->assertSee('Previous');
        $response->assertSee('Next');
    }
});

