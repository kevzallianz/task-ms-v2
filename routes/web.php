<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::post('/login', [AuthController::class, 'authenticate'])->name('user.authenticate');
Route::post('/register', [AuthController::class, 'register'])->name('user.register');
Route::post('/logout', [AuthController::class, 'logout'])->name('user.logout');
// Password reset routes
Route::get('/password/reset', [AuthController::class, 'showForgotForm'])->name('password.request');
Route::post('/password/email', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/password/reset/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [AuthController::class, 'reset'])->name('password.update');
Route::get('/password/reset/success', [AuthController::class, 'showResetSuccess'])->name('password.reset.success');

Route::middleware('auth')->group(function () {
    Route::get('/overview', [UserController::class, 'overview'])->name('user.overview');
    Route::get('/tasks', [UserController::class, 'tasks'])->name('user.tasks');

    /* Project Routes */
    Route::get('/projects', [UserController::class, 'projects'])->name('user.projects');
    Route::get('/projects/{project}', [ProjectController::class, 'view'])->name('projects.view');
    Route::post('/projects/store', [ProjectController::class, 'store'])->name('projects.store');
    Route::post('/projects/{project}/status', [ProjectController::class, 'updateStatus'])->name('projects.update-status');
    Route::put('/projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
    Route::delete('/projects/{project}', [ProjectController::class, 'deleteProject'])->name('projects.delete');
    Route::post('/projects/{project}/tasks', [ProjectController::class, 'addTask'])->name('projects.tasks.store');
    Route::put('/projects/{project}/tasks/{task}/status', [ProjectController::class, 'updateTaskStatus'])->name('projects.tasks.update-status');
    Route::put('/projects/{project}/tasks/{task}', [ProjectController::class, 'updateTask'])->name('projects.tasks.update');
    Route::delete('/projects/{project}/tasks/{task}', [ProjectController::class, 'deleteTask'])->name('projects.tasks.delete');
    Route::get('/projects/{project}/tasks/{task}/remarks', [ProjectController::class, 'getTaskRemarks'])->name('projects.tasks.remarks');
    Route::post('/projects/{project}/tasks/{task}/remarks', [ProjectController::class, 'addTaskRemark'])->name('projects.tasks.remarks.store');
    Route::post('/projects/{project}/add-contributor', [ProjectController::class, 'addContributor'])->name('projects.add-contributor');
    Route::delete('/projects/{project}/remove-contributor/{contributor}', [ProjectController::class, 'removeContributor'])->name('projects.remove-contributor');

    /* Campaign Routes */
    Route::get('/campaign', [CampaignController::class, 'index'])->name('user.campaign');
    Route::post('/campaigns/{campaign}/members', [CampaignController::class, 'addMember'])->name('campaigns.add-member');
    Route::put('/campaigns/{campaign}/members/{campaignMember}/access-level', [CampaignController::class, 'updateMemberAccess'])->name('campaigns.members.update-access');
    Route::post('/campaigns/{campaign}/tasks', [CampaignController::class, 'storeTask'])->name('campaigns.tasks.store');
    Route::post('/campaigns/{campaign}/projects/{project}/tasks', [CampaignController::class, 'storeProjectTask'])->name('user.campaign.project.tasks.store');
    Route::post('/campaigns/{campaign}/projects/{project}/status', [CampaignController::class, 'updateProjectStatus'])->name('campaigns.projects.update-status');
    Route::post('/campaigns/{campaign}/projects/{project}', [CampaignController::class, 'updateProject'])->name('campaigns.projects.update');
    Route::delete('/campaigns/{campaign}/projects/{project}', [CampaignController::class, 'destroyProject'])->name('campaigns.projects.destroy');
    Route::post('/campaigns/{campaign}/projects', [CampaignController::class, 'storeProject'])->name('campaigns.projects.store');
    Route::get('/campaigns/{campaign}/projects/{project}', [CampaignController::class, 'viewProject'])->name('campaigns.projects.view');
    Route::post('/campaigns/{campaign}/import', [CampaignController::class, 'importTasks'])->name('campaigns.tasks.import');
    Route::get('/campaigns/{campaign}/tasks/{task}/remarks', [CampaignController::class, 'getTaskRemarks'])->name('campaigns.tasks.remarks');
    Route::post('/campaigns/{campaign}/tasks/{task}/remarks', [CampaignController::class, 'addTaskRemark'])->name('campaigns.tasks.remarks.store');
    Route::put('/campaigns/{campaign}/tasks/{task}/status', [CampaignController::class, 'updateTaskStatus'])->name('campaigns.tasks.update-status');
    Route::put('/campaigns/{campaign}/tasks/{task}', [CampaignController::class, 'updateTask'])->name('campaigns.tasks.update');
    Route::delete('/campaigns/{campaign}/tasks/{task}', [CampaignController::class, 'deleteTask'])->name('campaigns.tasks.delete');

    /* Super Admin Routes */
    Route::middleware('role:superadmin')->group(function () {
        Route::get('/~/campaigns', [SuperAdminController::class, 'campaigns'])->name('superadmin.campaigns');
        Route::post('/~/campaigns', [SuperAdminController::class, 'storeCampaign'])->name('superadmin.campaigns.store');
        Route::put('/~/campaigns/{campaign}', [SuperAdminController::class, 'updateCampaign'])->name('superadmin.campaigns.update');
        Route::delete('/~/campaigns/{campaign}', [SuperAdminController::class, 'deleteCampaign'])->name('superadmin.campaigns.delete');
        Route::get('/~/campaigns/{campaign}/members', [SuperAdminController::class, 'campaignMembers'])->name('superadmin.campaigns.members');
        Route::get('/~/users', [SuperAdminController::class, 'users'])->name('superadmin.users');
        Route::post('/~/users/bulk/campaign', [SuperAdminController::class, 'assignUsersToCampaignBulk'])->name('superadmin.users.assign-campaign-bulk');
        Route::put('/~/users/{user}/role', [SuperAdminController::class, 'updateUserRole'])->name('superadmin.users.update-role');
        Route::post('/~/users/{user}/campaign', [SuperAdminController::class, 'assignUserToCampaign'])->name('superadmin.users.assign-campaign');
        Route::delete('/~/users/{user}', [SuperAdminController::class, 'deleteUser'])->name('superadmin.users.delete');
    });

    Route::middleware('role:admin')->group(function () {
        Route::get('/~/~/campaigns', [AdminController::class, 'campaigns'])->name('admin.campaigns');
        Route::get('/~/~/campaigns/{campaign}', [AdminController::class, 'show'])->name('admin.campaigns.show');
        Route::get('/~/~/campaigns/{campaign}/projects/{project}', [AdminController::class, 'showProject'])->name('admin.campaigns.projects.show');

        Route::get('/~/~/projects', [AdminController::class, 'projects'])->name('admin.campaigns.projects');
    });
});
