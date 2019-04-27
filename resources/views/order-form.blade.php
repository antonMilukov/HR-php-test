@extends('layouts.app')
@section('content')
    <order-form inline-template :input="{{ isset($inputAsJson) ? $inputAsJson : json_encode([]) }}">
        <div class="row">
            <form action="{{ $action }}" method="post" class="form-horizontal">
                {{ csrf_field() }}

                <div class="form-group">
                    <div class="col-md-4">
                        <label class="control-label" for="input1">email_клиента</label>
                        <input v-model="formData.client_email" type="email" class="form-control" id="input1" name="client_email">
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-4">
                        <label class="control-label" for="input1">партнер</label>
                        <select name="partner_id" v-model="formData.partner_id" class="form-control">
                            <option disabled selected value>Выберите значение</option>
                            @foreach($partners as $partner)
                                <option value="{{ $partner->id }}">{{ $partner->name }}</option>
                            @endforEach
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-4">
                        <label class="control-label">продукты</label><hr style="margin-top: 2px">

                        <div class="row col-md-offset-1">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>название</th>
                                        <th>кол-во</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template v-for="product in formData.products">
                                        <tr>
                                            <td>
                                                @{{ product.name }}
                                                <input type="hidden" v-model="product.product_id" :name="'products['+product.product_id+'][product_id]'">
                                            </td>
                                            <td><input v-model="product.quantity" :name="'products['+product.product_id+'][quantity]'" type="number" min="0" class="form-control"></td>
                                        </tr>
                                    </template>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-4">
                        <label class="control-label" for="input1">статус заказа</label>
                        <select name="status" v-model="formData.status" class="form-control">
                            <option disabled selected value>Выберите значение</option>
                            @foreach(\App\Order::$statusData as $statusId => $data)
                                <option value="{{ $statusId }}">{{ $data[\App\Order::ALIAS_TITLE] }}</option>
                            @endforEach
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-4">
                        <label class="control-label" for="input1">стоимость заказ</label>
                        <div class="alert alert-success" role="alert">@{{ sum }}</div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-4">
                        <input type="submit" value="сохранение изменений в заказе" class="btn btn-default">
                    </div>
                </div>
            </form>
        </div>
    </order-form>
@endsection