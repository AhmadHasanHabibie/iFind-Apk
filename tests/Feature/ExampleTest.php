<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_landing_page_renders_stores_and_redirects_to_login_on_click(): void
    {
        $staff = User::create([
            'name' => 'Staff Landing',
            'email' => 'staff.landing@ifind.id',
            'password' => bcrypt('password'),
            'role' => 'staff',
            'is_active' => true,
            'verification_status' => 'approved',
            'verified_at' => now(),
        ]);

        $category = Category::create([
            'name' => 'Kafe Estetik',
            'slug' => 'kafe-estetik',
            'icon' => 'fa-solid fa-mug-saucer',
        ]);

        $store = Store::create([
            'user_id' => $staff->id,
            'category_id' => $category->id,
            'name' => 'Spot Nongkrong Unggulan',
            'slug' => 'spot-nongkrong-unggulan',
            'address' => 'Jl. Kenangan No. 10',
            'city' => 'Jakarta Selatan',
            'status' => 'approved',
            'is_active' => true,
            'description' => 'Tempat santai favorit pelajar untuk kumpul dan belajar bersama.',
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Spot Nongkrong Unggulan');
        $response->assertSee('Spot Nongkrong & Nugas');
        $response->assertSee(route('login'));
    }
}
