<?php

use App\Enums\TicketStatus;
use App\Models\Admin;
use App\Models\Ticket;
use App\Models\TicketApproveStep;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('approves a single ticket as admin', function () {
    $admin = Admin::query()->create([
        'name' => 'Admin 1',
        'username' => 'admin1',
        'password' => bcrypt('password123'),
    ]);
    TicketApproveStep::query()->create([
        'admin_id' => $admin->id,
        'order' => 1,
        'status' => TicketStatus::ApprovedByAdmin1->value,
    ]);

    $this->actingAs($admin, 'admin');

    $ticket = Ticket::factory()->create([
        'status' => TicketStatus::Submitted,
    ]);

    $response = $this->post(route('admin.tickets.approve', ['id' => $ticket->id]), [
        'note' => 'Looks good',
    ]);

    $response->assertRedirect(route('admin.dashboard'));
    $response->assertSessionHas('success', 'Approved');

    $ticket->refresh();
    expect($ticket->status)->toBe(TicketStatus::ApprovedByAdmin1);
});

it('rejects a single ticket as admin', function () {
    $admin = Admin::query()->create([
        'name' => 'Admin 1',
        'username' => 'admin1',
        'password' => bcrypt('password123'),
    ]);
    TicketApproveStep::query()->create([
        'admin_id' => $admin->id,
        'order' => 1,
        'status' => TicketStatus::ApprovedByAdmin1->value,
    ]);

    $this->actingAs($admin, 'admin');

    $ticket = Ticket::factory()->create([
        'status' => TicketStatus::Submitted,
    ]);

    $response = $this->post(route('admin.tickets.reject', ['id' => $ticket->id]), [
        'note' => 'Not acceptable',
    ]);

    $response->assertRedirect(route('admin.dashboard'));
    $response->assertSessionHas('success', 'Ticket rejected');

    $ticket->refresh();
    expect($ticket->status)->toBe(TicketStatus::RejectedByAdmin1);
});

it('bulk approves multiple tickets as admin', function () {
    $admin = Admin::query()->create([
        'name' => 'Admin 1',
        'username' => 'admin1',
        'password' => bcrypt('password123'),
    ]);
    TicketApproveStep::query()->create([
        'admin_id' => $admin->id,
        'order' => 1,
        'status' => TicketStatus::ApprovedByAdmin1->value,
    ]);

    $this->actingAs($admin, 'admin');

    $tickets = Ticket::factory()->count(3)->create([
        'status' => TicketStatus::Submitted,
    ]);

    $ids = $tickets->pluck('id')->all();

    $response = $this->post(route('admin.tickets.bulk-approve'), [
        'ticket_ids' => json_encode($ids),
    ]);

    $response->assertRedirect(route('admin.dashboard'));
    $response->assertSessionHas('success', 'Tickets approved successfully.');

    foreach ($tickets as $ticket) {
        $ticket->refresh();
        expect($ticket->status)->toBe(TicketStatus::ApprovedByAdmin1);
    }
});

it('bulk rejects multiple tickets as admin', function () {
    $admin = Admin::query()->create([
        'name' => 'Admin 1',
        'username' => 'admin1',
        'password' => bcrypt('password123'),
    ]);
    TicketApproveStep::query()->create([
        'admin_id' => $admin->id,
        'order' => 1,
        'status' => TicketStatus::ApprovedByAdmin1->value,
    ]);

    $this->actingAs($admin, 'admin');

    $tickets = Ticket::factory()->count(3)->create([
        'status' => TicketStatus::Submitted,
    ]);

    $ids = $tickets->pluck('id')->all();

    $response = $this->post(route('admin.tickets.bulk-reject'), [
        'ticket_ids' => json_encode($ids),
    ]);

    $response->assertRedirect(route('admin.dashboard'));
    $response->assertSessionHas('success', 'Tickets rejected successfully.');

    foreach ($tickets as $ticket) {
        $ticket->refresh();
        expect($ticket->status)->toBe(TicketStatus::RejectedByAdmin1);
    }
});


