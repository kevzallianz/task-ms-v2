<?php

use App\Models\Campaign;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

beforeEach(function () {
    Schema::create('users', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('username')->nullable()->unique();
        $table->string('email')->unique();
        $table->string('password');
        $table->string('role')->default('user');
        $table->rememberToken();
        $table->timestamps();
    });

    Schema::create('campaigns', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->text('description')->nullable();
        $table->timestamps();
    });

    Schema::create('campaign_members', function (Blueprint $table) {
        $table->id();
        $table->foreignId('campaign_id');
        $table->foreignId('user_id');
        $table->string('access_level')->default('viewer');
        $table->timestamps();
    });
});

afterEach(function () {
    Schema::dropIfExists('campaign_members');
    Schema::dropIfExists('campaigns');
    Schema::dropIfExists('users');
});

test('a superadmin can assign their own account to a campaign', function () {
    $superadmin = User::factory()->create(['role' => 'superadmin']);
    $campaign = Campaign::create([
        'name' => 'Business Development',
        'description' => 'Superadmin campaign team',
    ]);

    $this->actingAs($superadmin)
        ->postJson(route('superadmin.users.assign-campaign', $superadmin), [
            'campaign_id' => $campaign->id,
            'access_level' => 'all',
        ])
        ->assertOk()
        ->assertJson([
            'success' => true,
            'campaign' => ['id' => $campaign->id],
            'access_level' => 'all',
        ]);

    expect(
        Campaign::whereHas('members', fn ($query) => $query->where('user_id', $superadmin->id))
            ->whereKey($campaign->id)
            ->exists()
    )->toBeTrue();

    $this->assertDatabaseHas('campaign_members', [
        'campaign_id' => $campaign->id,
        'user_id' => $superadmin->id,
        'access_level' => 'all',
    ]);

    $this->get(route('superadmin.users'))
        ->assertOk()
        ->assertSee('My Campaign')
        ->assertSee(route('user.campaign'), false)
        ->assertSee(route('superadmin.users.assign-campaign', $superadmin, false), false);
});
