<?php

use App\Http\Controllers\User\Ticket\ListTicketController;
use App\Services\Ticket\TicketService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Illuminate\View\View;

it('returns view with paginated tickets using default pagination', function () {
    $ticketService = Mockery::mock(TicketService::class);
    $controller = new ListTicketController($ticketService);
    
    $request = Request::create('/dashboard', 'GET');
    
    $paginator = new Paginator(
        collect([]),
        0,
        10,
        1,
        ['path' => '/dashboard']
    );
    
    $ticketService
        ->shouldReceive('list')
        ->once()
        ->with(10, 1)
        ->andReturn($paginator);

    $response = $controller($request);

    expect($response)->toBeInstanceOf(View::class)
        ->and($response->getName())->toBe('user.dashboard')
        ->and($response->getData()['tickets'])->toBe($paginator);
});

it('returns view with paginated tickets using custom pagination parameters', function () {
    $ticketService = Mockery::mock(TicketService::class);
    $controller = new ListTicketController($ticketService);
    
    $request = Request::create('/dashboard?per_page=20&page=2', 'GET');
    
    $paginator = new Paginator(
        collect([]),
        0,
        20,
        2,
        ['path' => '/dashboard']
    );
    
    $ticketService
        ->shouldReceive('list')
        ->once()
        ->with(20, 2)
        ->andReturn($paginator);

    $response = $controller($request);

    expect($response)->toBeInstanceOf(View::class)
        ->and($response->getName())->toBe('user.dashboard')
        ->and($response->getData()['tickets'])->toBe($paginator);
});

it('handles string pagination parameters by casting to int', function () {
    $ticketService = Mockery::mock(TicketService::class);
    $controller = new ListTicketController($ticketService);
    
    $request = Request::create('/dashboard?per_page=15&page=3', 'GET');
    
    $paginator = new Paginator(
        collect([]),
        0,
        15,
        3,
        ['path' => '/dashboard']
    );
    
    $ticketService
        ->shouldReceive('list')
        ->once()
        ->with(15, 3)
        ->andReturn($paginator);

    $response = $controller($request);

    expect($response)->toBeInstanceOf(View::class);
});

it('passes tickets to view correctly', function () {
    $ticketService = Mockery::mock(TicketService::class);
    $controller = new ListTicketController($ticketService);
    
    $request = Request::create('/dashboard', 'GET');
    
    $tickets = collect([
        (object) ['id' => 1, 'title' => 'Ticket 1'],
        (object) ['id' => 2, 'title' => 'Ticket 2'],
    ]);
    
    $paginator = new Paginator(
        $tickets,
        2,
        10,
        1,
        ['path' => '/dashboard']
    );
    
    $ticketService
        ->shouldReceive('list')
        ->once()
        ->andReturn($paginator);

    $response = $controller($request);

    $viewData = $response->getData();
    expect($viewData)->toHaveKey('tickets')
        ->and($viewData['tickets'])->toBe($paginator);
});

