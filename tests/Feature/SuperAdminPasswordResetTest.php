<?php

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;

beforeEach(function () {
    Schema::create('users', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('username')->nullable()->unique();
        $table->string('email')->unique();
        $table->string('password');
        $table->string('role')->default('user');
        $table->string('avatar')->nullable();
        $table->string('bio', 500)->nullable();
        $table->rememberToken();
        $table->timestamps();
    });

    Schema::create('password_resets', function (Blueprint $table) {
        $table->string('email')->index();
        $table->string('token');
        $table->timestamp('created_at')->nullable();
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
    Schema::dropIfExists('password_resets');
    Schema::dropIfExists('users');
});

test('a superadmin can send a user a password reset link', function () {
    Notification::fake();

    $superadmin = User::factory()->create(['role' => 'superadmin']);
    $user = User::factory()->create();

    $response = $this->actingAs($superadmin)
        ->postJson(route('superadmin.users.password-reset', $user));

    $response->assertOk()
        ->assertJson([
            'success' => true,
            'message' => 'Password reset link sent successfully.',
        ]);

    Notification::assertSentTo($user, ResetPassword::class);
});

test('a regular user cannot send another user a password reset link', function () {
    Notification::fake();

    $regularUser = User::factory()->create(['role' => 'user']);
    $target = User::factory()->create();

    $response = $this->actingAs($regularUser)
        ->from(route('user.overview'))
        ->postJson(route('superadmin.users.password-reset', $target));

    $response->assertRedirect(route('user.overview'));
    Notification::assertNothingSent();
});

test('the users page includes the password reset action', function () {
    $superadmin = User::factory()->create(['role' => 'superadmin']);
    $user = User::factory()->create();

    $this->actingAs($superadmin)
        ->get(route('superadmin.users'))
        ->assertOk()
        ->assertSee('Send password reset link to '.$user->name, false)
        ->assertSee(route('superadmin.users.password-reset', $user), false);
});
