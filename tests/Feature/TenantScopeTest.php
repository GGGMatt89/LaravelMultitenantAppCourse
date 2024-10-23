<?php

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

test('A model has a tenant_id on the migration', function () {
    $now = now(); // grab the timestamp -> it is used to name the migration
    $this->artisan('make:model Test -m');

    //find the migration file and check it has a tenant_id on it
    $filename = Str::finish(now()->format('Y_m_d_His'), '_create_tests_table.php');
    $this->assertFileExists(database_path('migrations\\' . $filename));
    $this->assertFileExists(app_path('Models\Test.php'));
    $this->assertStringContainsString('$table->foreignId(\'tenant_id\')->index();', File::get(database_path('migrations\\' . $filename)));



    //clean up and remove the created files
    File::delete(database_path('migrations\\' . $filename));
    File::delete(app_path('Models\Test.php'));

});


test('A user can only see users in the same tenant', function () {
    $tenant1 = Tenant::factory()->create();
    $tenant2 = Tenant::factory()->create();

    $user1 = User::factory()->create(['tenant_id' => $tenant1->id]);
    User::factory(9)->create(['tenant_id' => $tenant1->id]);
    User::factory(10)->create(['tenant_id' => $tenant2->id]);

    auth()->login($user1);
    $this->assertEquals(10, User::count());

});

test('A user can only create a user in its tenant', function () {
    $tenant1 = Tenant::factory()->create();

    $user1 = User::factory()->create(['tenant_id' => $tenant1->id]);
    auth()->login($user1);

    $createdUser = User::factory()->create();

    $this->assertEquals($createdUser->tenant_id, $user1->tenant_id);
});

test('A user can only create a user in its tenant even if other tenant is provided', function () {
    $tenant1 = Tenant::factory()->create();
    $tenant2 = Tenant::factory()->create();

    $user1 = User::factory()->create(['tenant_id' => $tenant1]);
    auth()->login($user1);

    $createdUser = User::factory()->make();
    $createdUser->tenant_id = $tenant2->id;
    $createdUser->save();

    $this->assertTrue($createdUser->tenant_id == $user1->tenant_id);
});
