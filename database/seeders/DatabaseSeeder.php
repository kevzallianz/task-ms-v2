<?php

namespace Database\Seeders;

use App\Models\Campaign;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $campaigns = [
            ['name' => 'BPI PA OUTBOUND', 'description' => 'Campaign for BPI PA OUTBOUND.'],
            ['name' => 'BPI PA INBOUND', 'description' => 'Campaign for BPI PA INBOUND.'],
            ['name' => 'BPI PL', 'description' => 'Campaign for BPI PL.'],
            ['name' => 'BPI FF', 'description' => 'Campaign for BPI FF.'],
            ['name' => 'MB ACQ', 'description' => 'Campaign for MB ACQ.'],
            ['name' => 'MB PL', 'description' => 'Campaign for MB PL.'],
            ['name' => 'MB PA', 'description' => 'Campaign for MB PA.'],
            ['name' => 'BDO SGM', 'description' => 'Campaign for BDO SGM.'],
            ['name' => 'BDO ONLINE', 'description' => 'Campaign for BDO ONLINE.'],
            ['name' => 'BDO AEM', 'description' => 'Campaign for BDO AEM.'],
            ['name' => 'BDO CRM', 'description' => 'Campaign for BDO CRM.'],
            ['name' => 'BDO CIE', 'description' => 'Campaign for BDO CIE.'],
            ['name' => 'BDO SUPPLE', 'description' => 'Campaign for BDO SUPPLE.'],
            ['name' => 'BDO VC', 'description' => 'Campaign for BDO VC.'],
            ['name' => 'BDO NTH CARD', 'description' => 'Campaign for BDO NTH CARD.'],
            ['name' => 'CBC ACQUI', 'description' => 'Campaign for CBC ACQUI.'],
            ['name' => 'CBC PA', 'description' => 'Campaign for CBC PA.'],
            ['name' => 'CBC HPL', 'description' => 'Campaign for CBC HPL.'],
            ['name' => 'MEDICARD', 'description' => 'Campaign for MEDICARD.'],
            ['name' => 'BDO CCC', 'description' => 'Campaign for BDO CCC.'],
            ['name' => 'CBC CCC', 'description' => 'Campaign for CBC CCC.'],
            ['name' => 'AC MOBILITY', 'description' => 'Campaign for AC MOBILITY.'],
            ['name' => 'RBSC', 'description' => 'Campaign for RBSC.'],
            ['name' => 'HR', 'description' => 'Campaign for HR.'],
            ['name' => 'EMPLOYEE ENGAGEMENT', 'description' => 'Campaign for EMPLOYEE ENGAGEMENT.'],
            ['name' => 'TRAINING', 'description' => 'Campaign for TRAINING.'],
            ['name' => 'TA', 'description' => 'Campaign for TA.'],
            ['name' => 'FINANCE', 'description' => 'Campaign for FINANCE.'],
            ['name' => 'PURCHASING', 'description' => 'Campaign for PURCHASING.'],
            ['name' => 'PAYROLL', 'description' => 'Campaign for PAYROLL.'],
            ['name' => 'STATUTORY', 'description' => 'Campaign for STATUTORY.'],
            ['name' => 'I.T.', 'description' => 'Campaign for I.T.'],
            ['name' => 'PREMISES', 'description' => 'Campaign for PREMISES.'],
            ['name' => 'COMPLIANCE', 'description' => 'Campaign for COMPLIANCE.'],
            ['name' => 'BUSINESS DEV', 'description' => 'Campaign for BUSINESS DEV.'],
            ['name' => 'APS', 'description' => 'Campaign for APS.'],
            ['name' => 'BDO SV/QA', 'description' => 'Campaign for BDO SV/QA.'],
            ['name' => 'BPI SV/QA', 'description' => 'Campaign for BPI SV/QA.'],
        ];

        User::factory()->create([
            'name' => 'Kevs Administrator',
            'username' => 'kevsadmin',
            'email' => 'allianz.synergia.kevs@gmail.com',
            'password' => Hash::make('bus.dev0!'),
            'role' => 'superadmin',
        ]);

        Campaign::factory()->createMany($campaigns);
    }
}
