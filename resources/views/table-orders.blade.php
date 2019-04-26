@extends('layouts.app')
@section('content')
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>ид_заказа</th>
            <th>название_партнера</th>
            <th>стоимость_заказа</th>
            <th>наименование_состав_заказа</th>
            <th>статус_заказа</th>
        </tr>
        </thead>
        <tbody>
        @foreach($orders as $order)
            <tr>
                <td><a href="{{ route('order-form', ['orderId' => $order->id]) }}">{{ $order->id }}</a></td>
                <td>{{ $order->partner->name }}</td>
                <td>{{ $order->sum}}</td>
                <td>{{ $order->content}}</td>
                <td>{{ $order->readable_status}}</td>
            </tr>
        @endforeach

        </tbody>
    </table>

@endsection