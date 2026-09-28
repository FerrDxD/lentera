<?php

namespace Tests\Feature;

use App\Models\Lomba;
use App\Models\User;
use App\Services\ScoringService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LenteraScoreTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_public_landing_loads_successfully(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    public function test_admin_dashboard_requires_admin_role(): void
    {
        $juri = User::where('role', 'juri')->first();
        $this->actingAs($juri);

        $response = $this->get('/admin/dashboard');
        // Juri is redirected to juri dashboard
        $response->assertRedirect(route('juri.dashboard'));
    }

    public function test_admin_can_access_dashboard_and_rekap(): void
    {
        $admin = User::where('role', 'admin')->first();
        $this->actingAs($admin);

        $response = $this->get('/admin/dashboard');
        $response->assertStatus(200);

        $puisi = Lomba::where('slug', 'lomba-baca-puisi')->first();
        $rekapResponse = $this->get("/admin/lomba/{$puisi->id}/rekap");
        $rekapResponse->assertStatus(200);
    }

    public function test_juri_can_access_scoring_form(): void
    {
        $juri1 = User::where('email', 'juri1@lentera.test')->first();
        $this->actingAs($juri1);

        $puisi = Lomba::where('slug', 'lomba-baca-puisi')->first();
        $response = $this->get("/juri/nilai/{$puisi->id}");
        $response->assertStatus(200);
    }

    public function test_scoring_service_computes_leaderboard(): void
    {
        $puisi = Lomba::where('slug', 'lomba-baca-puisi')->first();
        $service = new ScoringService();
        $leaderboard = $service->getLeaderboard($puisi);

        $this->assertNotEmpty($leaderboard);
        $this->assertEquals(1, $leaderboard[0]['rank']);
        $this->assertGreaterThanOrEqual($leaderboard[1]['total_skor'], $leaderboard[0]['total_skor']);
    }
}
