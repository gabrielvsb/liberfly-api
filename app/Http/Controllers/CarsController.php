<?php

namespace App\Http\Controllers;

use App\Models\Car;
use Illuminate\Http\Request;

class CarsController extends Controller
{
    /**
     * This method is an endpoint to return all records from cars table
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllCars(): \Illuminate\Http\JsonResponse
    {
        try {
            $cars_records = Car::all();

            return response()->json(['message' => 'Search done!', 'data' => $cars_records]);
        }catch (\Exception $exception){
            return response()->json(['message' => $exception->getMessage()], 500);
        }
    }

    public function getCar($idCar)
    {
        try {
            $car = Car::findOr($idCar, function (){
               return response()->json(['message' => 'Car not found.'], 404);
            });

            return response()->json(['message' => 'Search done!', 'data' => $car]);
        }catch (\Exception $exception){
            return response()->json(['message' => $exception->getMessage()], 500);
        }
    }
}
