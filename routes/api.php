<?php

use App\Models\Family;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Paddle\Cashier;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/families', function () {
    return Family::all();
});

Route::post('/pay-link', function () {
    $data = request()->json();

    $familyId = (int)$data->get('familyId');
    $styles = (array)$data->get('styles');
    $fullFamily = (bool)$data->get('fullFamily');

    $family = Family::findOrFail($familyId);
    $order = new Order([
        'styles' => $styles,
        'full_family' => $fullFamily,
    ]);
    $order->family()->associate($family);
    $order->save();

    $request = Cashier::post('/product/generate_pay_link', array_merge([
        'title' => $order->getTitle(),
        'prices' => [sprintf('EUR:%.2f', $order->getPrice())],
        'passthrough' => (string)$order->id,
        'webhook_url' => Cashier::webhookUrl(),
    ], Cashier::paddleOptions()));

    return $request['response']['url'];
});
