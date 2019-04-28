<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderFormSave;
use App\Order;
use App\OrderProduct;
use App\Partner;
use App\Product;
use \Illuminate\Http\Request;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use \App\Services\Weather\WeatherManager;
use Illuminate\Support\Facades\Session;

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
        $order = Order::where('id', $request->orderId)->withRelations()->firstOrFail();
        $partners = Partner::all();

        $inputAsJson = $this->getInputAsJson($order);

        return view('order-form')->with([
            'title' => 'Форма заказа',
            'order' => $order,
            'partners' => $partners,
            'action' => route('order-form-save', ['orderId' => $order->id]),
            'inputAsJson' => $inputAsJson
        ]);
    }

    public function orderFormSave(OrderFormSave $request)
    {
        $order = Order::where('id', $request->orderId)->withRelations()->firstOrFail();
        $this->saveOrder($order, $request);
        $this->syncOrderProducts($order, $request->products);

        return redirect()->route('order-form', ['orderId' => $order->id]);
    }

    private function getInputAsJson(Order $order)
    {
        $isErrorExist = !empty(Session::get('errors'));
        $oldProducts = old('products');

        $orderProducts = [];
        foreach ($order->order_products as $orderProduct){
            $productId = $orderProduct->product_id;
            $orderProducts []= [
                'product_id' => $productId,
                'name' => $orderProduct->product->name,
                'quantity' => $isErrorExist ? $oldProducts[$productId]['quantity'] : $orderProduct->quantity,
                'price' => $orderProduct->price,
                'id' => $orderProduct->id
            ];
        }

        $r = json_encode([
            'partner_id' => ($isErrorExist) ? old('partner_id') : $order->partner_id,
            'client_email' => ($isErrorExist) ? old('client_email') : $order->client_email,
            'status' => ($isErrorExist) ? old('status') : $order->status,
            'products' => [
                'current' => $orderProducts,
                'for_add' => [
                    [
                        'product_id' => 3,
                        'name' => 'product#3',
                        'quantity' => 1,
                        'price' => 100,
                    ]
                ]
            ]
        ]);
        return $r;
    }

    private function saveOrder(Order $order, OrderFormSave $request)
    {
        $order->fill($request->all());
        $order->save();
    }

    /**
     * @param Order $order
     * @param $arrSrc
     */
    private function syncOrderProducts(Order $order, $arrSrc)
    {
        $requestProducts = isset($arrSrc['current']) ? $arrSrc['current'] : [];
        $requestProductIds = array_keys($requestProducts);
        foreach ($order->order_products as $model){
            if (in_array($model->id, $requestProductIds)){
                $model->fill($requestProducts[$model->id]);
                $model->save();
            } else {
                $model->delete();
            }
        }

        $requestProductsForAdd = isset($arrSrc['for_add']) ? $arrSrc['for_add'] : [];

        if (!empty($requestProductsForAdd)){
            $productIds = [];
            foreach ($requestProductsForAdd as $data){
                $productIds []= $data['product_id'];
            }
            $products = Product::whereIn('id', $productIds)->get();
            foreach ($products as $product){
                $attrs = array_merge($requestProductsForAdd[$product->id], [
                    'price' => $product->price
                ]);
                $order->order_products()->create($attrs);
            }
        }

    }
}
