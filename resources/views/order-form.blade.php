@extends('layouts.app')
@section('content')
{{--    @todo vuejs validation--}}
    <order-form inline-template :input="{{ isset($inputAsJson) ? $inputAsJson : json_encode([]) }}">
        <div class="row">
            <form action="{{ $action }}" method="post" class="form-horizontal" @submit.prevent="submitForm" ref="form">
                {{ csrf_field() }}

                <div class="form-group">
                    <div class="col-md-4">
                        <label class="control-label" for="input1">email_клиента <span class="required-mark">*</span></label>
                        @php $error = $errors->has('client_email') ? 'error' : ''; @endphp
                        <input v-model="formData.client_email" type="email" class="form-control {{ $error }}" id="input1" name="client_email">
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-4">
                        <label class="control-label" for="input1">партнер <span class="required-mark">*</span></label>
                        @php $error = $errors->has('partner_id') ? 'error' : ''; @endphp
                        <select name="partner_id" v-model="formData.partner_id" class="form-control {{ $error }}">
                            <option disabled selected value>Выберите значение</option>
                            @foreach($partners as $partner)
                                <option value="{{ $partner->id }}">{{ $partner->name }}</option>
                            @endforEach
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-4">
                        <div class="row">
                            <div class="col-md-4">
                                <label class="control-label">продукты</label>
                            </div>

                            <div class="col-md-6">
                                <selectize v-model="internal.product_selected" :settings="settings">
                                    <option disabled selected value>Выберите значение</option>
                                    <template v-for="product in internal.product_src" >
                                        <option :value="product.product_id">@{{ product.name }}</option>
                                    </template>
                                </selectize>
                            </div>

                            <div class="col-md-2">
                                <button @click="addProduct()" type="button" class="btn btn-default">
                                    <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                                </button>
                            </div>
                        </div>
                        <div class="row col-md-offset-1">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>название</th>
                                        <th>кол-во</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
{{--                                @todo контролы: удаление и добавление--}}
                                    <template v-for="orderProduct in formData.products.current">
                                        <tr>
                                            <td>
                                                @{{ orderProduct.name }}
                                                <input type="hidden" v-model="orderProduct.id" :name="'products[current]['+orderProduct.id+'][id]'">
                                                <input type="hidden" v-model="orderProduct.product_id" :name="'products[current]['+orderProduct.id+'][product_id]'">
                                            </td>
                                            <td><input v-model="orderProduct.quantity" :name="'products[current]['+orderProduct.id+'][quantity]'" type="number" min="1" class="form-control"></td>
                                            <td>
                                                <button @click="removeProduct(formData.products.current, 'id', orderProduct.id)" type="button" class="btn btn-default">
                                                    <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>

{{--                                    products list for add--}}
                                    <template v-for="orderProduct in formData.products.for_add">
                                        <tr>
                                            <td>
                                                @{{ orderProduct.name }}
                                                <input type="hidden" v-model="orderProduct.product_id" :name="'products[for_add]['+orderProduct.product_id+'][product_id]'">
                                            </td>
                                            <td><input v-model="orderProduct.quantity" :name="'products[for_add]['+orderProduct.product_id+'][quantity]'" type="number" min="1" class="form-control"></td>
                                            <td>
                                                <button @click="removeProduct(formData.products.for_add, 'product_id', orderProduct.product_id)" type="button" class="btn btn-default">
                                                    <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-4">
                        <label class="control-label" for="input1">статус заказа <span class="required-mark">*</span></label>
                        @php $error = $errors->has('status') ? 'error' : ''; @endphp
                        <select name="status" v-model="formData.status" class="form-control {{ $error }}">
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