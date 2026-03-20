<?php

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('returns products list', function () {
    Product::create([
        'name' => 'Elden Ring',
        'image' => '/images/products/eldenring.jpg',
        'price' => 3490.00,
        'stock' => 15,
    ]);

    $response = $this->getJson('/api/products');

    $response
        ->assertOk()
        ->assertJsonCount(1)
        ->assertJsonFragment([
            'name' => 'Elden Ring',
            'stock' => 15,
        ]);
});

it('rejects invalid product id', function () {
    $response = $this->postJson('/api/order', [
        'product_id' => 999,
        'quantity' => 1,
        'full_name' => 'Juan Dela Cruz',
        'location' => 'Manila City',
    ]);

    $response
        ->assertStatus(404)
        ->assertJson([
            'result' => 'Error response',
            'error' => 'Product not found',
        ]);
});

it('rejects zero quantity', function () {
    $product = Product::create([
        'name' => 'Ghost of Tsushima Director\'s Cut',
        'image' => '/images/products/gotd.png',
        'price' => 3490.00,
        'stock' => 14,
    ]);

    $response = $this->postJson('/api/order', [
        'product_id' => $product->id,
        'quantity' => 0,
        'full_name' => 'Juan Dela Cruz',
        'location' => 'Manila City',
    ]);

    $response
        ->assertStatus(400)
        ->assertJson([
            'result' => 'Invalid request',
            'error' => 'Invalid quantity',
        ]);
});

it('rejects negative quantity', function () {
    $product = Product::create([
        'name' => 'Spider-Man 2',
        'image' => '/images/products/spiderman2.png',
        'price' => 3490.00,
        'stock' => 25,
    ]);

    $response = $this->postJson('/api/order', [
        'product_id' => $product->id,
        'quantity' => -3,
        'full_name' => 'Juan Dela Cruz',
        'location' => 'Manila City',
    ]);

    $response
        ->assertStatus(400)
        ->assertJson([
            'result' => 'Error',
            'error' => 'Invalid quantity',
        ]);
});

it('rejects order when quantity exceeds stock', function () {
    $product = Product::create([
        'name' => 'Tekken 8',
        'image' => '/images/products/tekken.png',
        'price' => 3490.00,
        'stock' => 2,
    ]);

    $response = $this->postJson('/api/order', [
        'product_id' => $product->id,
        'quantity' => 10,
        'full_name' => 'Juan Dela Cruz',
        'location' => 'Manila City',
    ]);

    $response
        ->assertStatus(400)
        ->assertJson([
            'result' => 'Order rejected',
            'error' => 'Not enough stock',
        ]);
});

it('accepts valid order and updates stock', function () {
    $product = Product::create([
        'name' => 'Final Fantasy VII Rebirth',
        'image' => '/images/products/finalfantasy.png',
        'price' => 3490.00,
        'stock' => 10,
    ]);

    $response = $this->postJson('/api/order', [
        'product_id' => $product->id,
        'quantity' => 3,
        'full_name' => 'Juan Dela Cruz',
        'location' => 'Manila City',
    ]);

    $response
        ->assertOk()
        ->assertJson([
            'result' => 'Stock updated',
            'message' => 'Order successful',
            'product' => 'Final Fantasy VII Rebirth',
            'remainingStock' => 7,
        ]);

    expect($product->fresh()->stock)->toBe(7);
});

it('rejects order when stock is zero', function () {
    $product = Product::create([
        'name' => 'PlayStation Edition Gaming Chair',
        'image' => '/images/products/chair.png',
        'price' => 15990.00,
        'stock' => 0,
    ]);

    $response = $this->postJson('/api/order', [
        'product_id' => $product->id,
        'quantity' => 1,
        'full_name' => 'Juan Dela Cruz',
        'location' => 'Manila City',
    ]);

    $response
        ->assertStatus(400)
        ->assertJson([
            'result' => 'Order rejected',
            'error' => 'Not enough stock',
        ]);
});

it('rejects order when full name is missing', function () {
    $product = Product::create([
        'name' => 'Elden Ring',
        'image' => '/images/products/eldenring.jpg',
        'price' => 3490.00,
        'stock' => 15,
    ]);

    $response = $this->postJson('/api/order', [
        'product_id' => $product->id,
        'quantity' => 1,
        'location' => 'Manila City',
    ]);

    $response
        ->assertStatus(422)
        ->assertJsonValidationErrors(['full_name']);
});

it('rejects order when location is missing', function () {
    $product = Product::create([
        'name' => 'God of War Ragnarök',
        'image' => '/images/products/gowr.jpg',
        'price' => 3490.00,
        'stock' => 10,
    ]);

    $response = $this->postJson('/api/order', [
        'product_id' => $product->id,
        'quantity' => 1,
        'full_name' => 'Juan Dela Cruz',
    ]);

    $response
        ->assertStatus(422)
        ->assertJsonValidationErrors(['location']);
});
