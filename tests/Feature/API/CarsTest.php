<?php

namespace Tests\Feature\API;

use App\Models\Car;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class CarsTest extends TestCase
{
    use RefreshDatabase;
    /**
     * Test endpoint get cars.
     */
    public function test_get_all_cars(): void
    {
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
}
