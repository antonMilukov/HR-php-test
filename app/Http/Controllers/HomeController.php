<?php

namespace App\Http\Controllers;

class HomeController extends Controller
{
    public function index()
    {
        return view('index')->with(['title' => 'Главная страница']);
    }

    public function temp()
    {
        return view('temp')->with(['title' => 'Температура в Брянске']);
    }

    public function tableOrders()
    {
        return view('table-orders')->with(['title' => 'Список заказов']);
    }
}
