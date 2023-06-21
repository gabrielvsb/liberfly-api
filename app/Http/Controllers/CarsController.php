<?php

namespace App\Http\Controllers;

use App\Models\Car;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class CarsController extends Controller
{
    /**
     * @OA\Get(
     *     tags={"Cars"},
     *     summary="Obtenha dados de carros",
     *     description="Retorna uma lista de carros cadastrados",
     *     path="/api/cars",
     *     security={ {"bearerAuth":{}} },
     *     @OA\Response(
     *          response=200,
     *          description="Search done.",
     *          @OA\JsonContent(
     *              @OA\Property(property="id", type="string", example="5"),
     *              @OA\Property(property="name", type="string", example="Car One"),
     *              @OA\Property(property="description", type="string", example="Description for car one"),
     *          )
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated"),
     *          )
     *     )
     * ),
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
