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

    /**
     * Main page
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('index')->with(['title' => 'Главная страница']);
    }

    /**
     * Temperature page
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function temp()
    {
        $manager = new WeatherManager();
        $weatherEntity = $manager->getWeatherData();
        return view('temp')->with(['title' => 'Температура в Брянске', 'weatherEntity' => $weatherEntity]);
    }

    /**
     *  Orders list page
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function tableOrders()
    {
        $orders = Order::getForOrderList();
        return view('table-orders')->with(['title' => 'Список заказов', 'orders' => $orders]);
    }

    /**
     * Order editor page
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
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
            'inputAsJson' => $inputAsJson,
            'breadcrumbs' => 'order-form'
        ]);
    }

    /**
     * Ajax save order page
     * @param OrderFormSave $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function orderFormSave(OrderFormSave $request)
    {
        $order = Order::where('id', $request->orderId)->withRelations()->firstOrFail();
        $this->saveOrder($order, $request);
        $this->syncOrderProducts($order, $request->products);

        return response()->json([
            'success' => true,
            'redirect' => route('table-orders')
        ]);
    }

    /**
     * Method with data for "Order editor page"
     * - collect all data for frontend processing
     * @param Order $order
     * @return false|string
     */
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

        $allProducts = [];
        foreach (Product::all() as $product){
            $allProducts []= [
                'product_id' => $product->id,
                'name' => $product->name,
                'quantity' => 1,
                'price' => $product->price,
            ];
        }

        $r = json_encode([
            'partner_id' => ($isErrorExist) ? old('partner_id') : $order->partner_id,
            'client_email' => ($isErrorExist) ? old('client_email') : $order->client_email,
            'status' => ($isErrorExist) ? old('status') : $order->status,
            'products' => [
                'current' => $orderProducts,
                'for_add' => []
            ],
            'product_src' => $allProducts
        ]);
        return $r;
    }

    /**
     *  Method for save order
     * @param Order $order
     * @param OrderFormSave $request
     */
    private function saveOrder(Order $order, OrderFormSave $request)
    {
        $order->fill($request->all());
        $order->save();
    }

    /**
     * Method for syncronizing order products in order_products table
     * @param Order $order
     * @param $arrSrc
     */
    private function syncOrderProducts(Order $order, $arrSrc)
    {
        $requestOrderProducts = isset($arrSrc['current']) ? $arrSrc['current'] : [];
        $requestOrderProductIds = array_map(function ($product){
            return $product['id'];
        }, $requestOrderProducts);

        foreach ($order->order_products as $model){
            if (in_array($model->id, $requestOrderProductIds)){
                $input = self::getFromArrayByValue($requestOrderProducts, 'id', $model->id);
                $model->fill($input);
                $model->save();
            } else {
                $model->delete();
            }
        }

        $requestOrderProductsForAdd = isset($arrSrc['for_add']) ? $arrSrc['for_add'] : [];
        if (!empty($requestOrderProductsForAdd)){
            $productIds = [];
            foreach ($requestOrderProductsForAdd as $data){
                $productIds []= $data['product_id'];
            }
            $products = Product::whereIn('id', $productIds)->get();
            foreach ($products as $product){
                $input = self::getFromArrayByValue($requestOrderProductsForAdd, 'product_id', $product->id);
                $attrs = array_merge($input, [
                    'price' => $product->price
                ]);
                $order->order_products()->create($attrs);
            }
        }

    }

    /**
     * Method for getting target array from container array by sender value
     * @param $src
     * @param $alias
     * @param $val
     * @return array|null
     */
    private static function getFromArrayByValue($src, $alias, $val)
    {
        $r = null;
        foreach ($src as $data){
            if (is_null($r) && $data[$alias] == $val){
                $r = $data;
            }
        }
        return $r;
    }
}
