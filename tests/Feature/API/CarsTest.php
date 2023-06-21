<?php

namespace Tests\Feature\API;

use App\Models\Car;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class CarsTest extends TestCase
{
    use RefreshDatabase;
    /**
     * Test endpoint get cars.
     */
    public function test_get_all_cars(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $num_records = 10;
        Car::factory($num_records)->create();

        $response = $this->getJson('/api/cars');

        $response->assertStatus(200);
        $response->assertJsonCount($num_records, 'data');
        $response->assertJsonStructure([
            'message',
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'description',
                    'created_at',
                    'updated_at'
                ]
            ]
        ]);
    }

    public function test_get_one_car(){
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $num_records = 10;
        $cars = Car::factory($num_records)->create();
        $car = $cars->first();
        $response = $this->getJson('/api/cars/' . $car->id);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'data' => [
                'id',
                'name',
                'description',
                'created_at',
                'updated_at'
            ]
        ]);
    }

}
