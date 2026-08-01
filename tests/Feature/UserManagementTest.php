<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_user_management_page(): void
    {
        $admin = $this->createUser('Administrator');
        $inspector = $this->createUser('Inspector', ['name' => 'Inspector One']);
        $resident = $this->createUser('Resident', ['name' => 'Resident One']);

        $response = $this->actingAs($admin)->get(route('users.index'));

        $response->assertOk();
        $response->assertSee($inspector->name);
        $response->assertSee($resident->name);
        $response->assertSee(route('users.update.status', $resident), false);
    }

    public function test_admin_can_update_resident_status(): void
    {
        $admin = $this->createUser('Administrator');
        $resident = $this->createUser('Resident', ['status' => 'Active']);

        $response = $this->actingAs($admin)
            ->patch(route('users.update.status', $resident), [
                'status' => 'Inactive',
            ]);

        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('status', 'Resident status updated.');
        $this->assertSame('Inactive', $resident->fresh()->status);
    }

    public function test_non_admin_cannot_manage_users(): void
    {
        $resident = $this->createUser('Resident');

        $this->actingAs($resident)
            ->get(route('users.index'))
            ->assertForbidden();
    }

    private function createUser(string $role, array $attributes = []): User
    {
        return User::create(array_merge([
            'name' => "{$role} User",
            'email' => fake()->unique()->safeEmail(),
            'password' => 'Password123',
            'role' => $role,
            'phone' => '0123456789',
            'status' => 'Active',
        ], $attributes));
    }
}
