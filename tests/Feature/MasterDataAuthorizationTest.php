<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MasterDataAuthorizationTest extends TestCase
{
    public function test_farm_manager_cannot_create_catching_team(): void
    {
        $response = $this
            ->actingAs($this->farmManager())
            ->post(route('catching-teams.store'), [
                'name' => 'ทีมที่ไม่ควรสร้างได้',
                'is_active' => true,
            ]);

        $response->assertForbidden();
    }

    public function test_farm_manager_cannot_create_chick_source(): void
    {
        $response = $this
            ->actingAs($this->farmManager())
            ->post(route('chick-sources.store'), [
                'name' => 'แหล่งที่ไม่ควรสร้างได้',
                'is_active' => true,
            ]);

        $response->assertForbidden();
    }

    public function test_catching_team_options_are_not_built_with_untrusted_html(): void
    {
        $template = file_get_contents(resource_path('views/catch-records/create.blade.php'));

        $this->assertIsString($template);
        $this->assertStringContainsString('teamSelect.add(new Option(name, name));', $template);
        $this->assertStringNotContainsString('${teamOptions}', $template);
    }

    private function farmManager(): User
    {
        $user = new User;
        $user->forceFill([
            'id' => 999,
            'name' => 'Farm Manager',
            'email' => 'manager-security-test@example.com',
            'password' => Hash::make('security-test-password'),
            'role' => User::ROLE_FARM_MANAGER,
            'farm_id' => 1,
        ]);
        $user->exists = true;

        return $user;
    }
}
