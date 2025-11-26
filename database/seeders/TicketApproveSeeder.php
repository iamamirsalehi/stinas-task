<?php

namespace Database\Seeders;

use App\Enums\Admin as EnumsAdmin;
use App\Enums\TicketStatus;
use App\Models\Admin;
use App\Models\TicketApprove;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TicketApproveSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin1 = Admin::query()->where('username', EnumsAdmin::Admin1->value)->first();

        TicketApprove::query()->firstOrCreate([
            'admin_id' => $admin1->id,
        ], [
            'order' => 1,
            'status' => TicketStatus::ApprovedByAdmin1->value,
        ]);

        $admin2 = Admin::query()->where('username', EnumsAdmin::Admin2->value)->first();

        TicketApprove::query()->firstOrCreate([
            'admin_id' => $admin2->id,
        ], [
            'order' => 2,
            'status' => TicketStatus::ApprovedByAdmin2->value,
        ]);
    }
}
