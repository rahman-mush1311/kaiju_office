<?php

// Home
use DaveJamesMiller\Breadcrumbs\Facades\Breadcrumbs;

Breadcrumbs::for('home', function ($trail) {
    $trail->push('Home', route('home'));
});

Breadcrumbs::for('product.index', function ($trail) {
    $trail->push('Products');
});

Breadcrumbs::for('product.create', function ($trail) {
    $trail->push('Products', route('product.index'));
    $trail->push('Create');
});

Breadcrumbs::for('product.edit', function ($trail) {
    $trail->push('Products', route('product.index'));
    $trail->push('Edit');
});

Breadcrumbs::for('brands.index', function ($trail) {
    $trail->push('Brands');
});

Breadcrumbs::for('brands.create', function ($trail) {
    $trail->push('Brands', route('brands.index'));
    $trail->push('Create');
});

Breadcrumbs::for('brands.edit', function ($trail) {
    $trail->push('Brands', route('brands.index'));
    $trail->push('Edit');
});

Breadcrumbs::for('location.index', function ($trail) {
    $trail->push('Location');
});

Breadcrumbs::for('location.create', function ($trail) {
    $trail->push('Location', route('location.index'));
    $trail->push('Create');
});

Breadcrumbs::for('location.edit', function ($trail) {
    $trail->push('Location', route('location.index'));
    $trail->push('Edit');
});

Breadcrumbs::for('area.index', function ($trail) {
    $trail->push('Area');
});

Breadcrumbs::for('area.create', function ($trail) {
    $trail->push('Area', route('area.index'));
    $trail->push('Create');
});

Breadcrumbs::for('area.edit', function ($trail) {
    $trail->push('Area', route('area.index'));
    $trail->push('Edit');
});

Breadcrumbs::for('distributors.index', function ($trail) {
    $trail->push('Distributors');
});

Breadcrumbs::for('sr.index', function ($trail) {
    $trail->push('Sales Representatives');
});

Breadcrumbs::for('distributors.assign-product', function ($trail) {
    $trail->push('Distributors', route('distributors.index'));
    $trail->push('Assign Product');
});

Breadcrumbs::for('distributors.create', function ($trail) {
    $trail->push('Distributors', route('distributors.index'));
    $trail->push('Create');
});

Breadcrumbs::for('distributors.edit', function ($trail) {
    $trail->push('Distributors', route('distributors.index'));
    $trail->push('Edit');
});

Breadcrumbs::for('order.index', function ($trail) {
    $trail->push('Order');
});

Breadcrumbs::for('order.edit', function ($trail) {
    $trail->push('Order', route('order.index'));
    $trail->push('Edit');
});

Breadcrumbs::for('customers.index', function ($trail) {
    $trail->push('Customer', route('customers.index'));
});

Breadcrumbs::for('customers.edit', function ($trail) {
    $trail->push('Customer', route('customers.index'));
    $trail->push('Edit');
});

Breadcrumbs::for('distributors.import-products', function ($trail) {
    $trail->push('Agents', route('distributors.import-products'));
    $trail->push('Import');
});

Breadcrumbs::for('delivery.charge.rule.index', function ($trail) {
    $trail->push('Delivery Charge Rules');
});

Breadcrumbs::for('delivery.charge.rule.create', function ($trail) {
    $trail->push('Delivery Charge Rules', route('delivery.charge.rules.index'));
    $trail->push('Create');
});

Breadcrumbs::for('delivery.charge.rule.edit', function ($trail) {
    $trail->push('Delivery Charge Rules', route('delivery.charge.rules.index'));
    $trail->push('Edit');
});
