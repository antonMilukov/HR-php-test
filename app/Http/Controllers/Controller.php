<?php

namespace App\Http\Controllers;

use App\Order;
use App\Partner;
use \Illuminate\Http\Request;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use \App\Services\Weather\WeatherManager;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function index()
    {
        return view('index')->with(['title' => 'Главная страница']);
    }

    public function temp()
    {
        $manager = new WeatherManager();
        $weatherEntity = $manager->getWeatherData();
        return view('temp')->with(['title' => 'Температура в Брянске', 'weatherEntity' => $weatherEntity]);
    }

    public function tableOrders()
    {
        $orders = Order::getForOrderList();
        return view('table-orders')->with(['title' => 'Список заказов', 'orders' => $orders]);
    }

    public function orderForm(Request $request)
    {
        $order = Order::where('id', $request->orderId)->with(['partner', 'order_products', 'order_products.product'])->firstOrFail();
        $partners = Partner::all();

        $orderProducts = [];
        foreach ($order->order_products as $orderProduct){
            $orderProducts []= [
                'product_id' => $orderProduct->product_id,
                'name' => $orderProduct->product->name,
                'quantity' => $orderProduct->quantity,
                'price' => $orderProduct->price
            ];
        }
        $inputAsJson = json_encode([
            'partner_id' => $order->partner_id,
            'client_email' => $order->client_email,
            'status' => $order->satatus,
            'products' => $orderProducts
        ]);
        return view('order-form')->with([
            'title' => 'Форма заказа',
            'order' => $order,
            'partners' => $partners,
            'action' => route('order-form-save', ['orderId' => $order->id]),
            'inputAsJson' => $inputAsJson
        ]);
    }

    public function orderFormSave(Request $request)
    {
        dd($request->all());
    }
}
