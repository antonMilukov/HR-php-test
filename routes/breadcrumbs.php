<?php
/**
 * Breadcrumbs
 */
Breadcrumbs::register('order-form', function ($breadcrumbs) {
    $breadcrumbs->push('HR PHP', route('index'));
    $breadcrumbs->push('Список заказов', route('table-orders'));
    $breadcrumbs->push('Форма заказа', route('order-form', null));
});