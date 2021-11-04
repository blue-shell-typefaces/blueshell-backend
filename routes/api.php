<?php

use App\Models\Family;
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
    $id = (int)$data->get('id');
    $cart = (array)$data->get('cart');
    $full = (bool)$data->get('buyFullFamily');

    $family = Family::findOrFail($id);

    if ($full) {
        $price = $family->family_price;
    } else {
        $price = count($cart) * $family->style_price;
    }

    $request = Cashier::post('/product/generate_pay_link', array_merge([
        'title' => $family->name,
        'prices' => [sprintf('EUR:%.2f', $price)],
        'passthrough' => $data,
        'webhook_url' => Cashier::webhookUrl(),
    ], Cashier::paddleOptions()));

    return $request['response']['url'];
});
