<?php

namespace App\Livewire;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Component;
use Carbon\Carbon;

class OrderForm extends Component
{
    public ?Product $product = null;
    public ?Order $order = null;
    public string $name = '';
    public string $description = '';
    public ?float $price;
    public bool $editing = false;
    public ?int $country_id;
    public Collection $allProducts;
    public string $order_date;
    public array $orderProducts = [];
    public array $listsForFields = [];
    public int $taxesPercent = 0,
        $user_id,
        $total = 0,
        $subtotal = 0,
        $taxes = 0;

    public function mount(Order $order): void
    {
        $this->initListsForFields();
        if (!is_null($this->order)) {
            $this->editing = true;
            $this->order = $order;
            $this->user_id = $this->order->user_id;
            $this->order_date = $this->order->order_date;
            $this->subtotal = $this->order->subtotal;
            $this->taxes = $this->order->taxes;
            $this->total = $this->order->total;
        } else {
            $this->order_date = today();
        }
        $this->taxesPercent = config('app.orders.taxes');
    }

    public function render(): View
    {
        $this->subtotal = 0;

        foreach ($this->orderProducts as $orderProduct) {
            if ($orderProduct['is_saved'] && $orderProduct['product_price'] && $orderProduct['quantity']) {
                $this->subtotal += $orderProduct['product_price'] * $orderProduct['quantity'];
            }
        }

        $this->total = $this->subtotal * (1 + $this->taxesPercent / 100);
        $this->taxes = $this->total - $this->subtotal;
        return view('livewire.order-form');
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'order_date' => ['required', 'date'],
            'subtotal' => ['required', 'numeric'],
            'taxes' => ['required', 'numeric'],
            'total' => ['required', 'numeric'],
            'orderProducts' => ['array']
        ];
    }

    public function save(): void
    {
        $this->validate();

        $this->order_date = Carbon::parse($this->order_date)->format('Y-m-d');

        if (is_null($this->order)) {
            $this->order = Order::create($this->only('user_id', 'order_date', 'subtotal', 'taxes', 'total'));
        } else {
            $this->order->update($this->only('user_id', 'order_date', 'subtotal', 'taxes', 'total'));
        }

        $products = [];

        foreach ($this->orderProducts as $product) {
            $products[$product['product_id']] = ['price' => $product['product_price'], 'quantity' => $product['quantity']];
        }

        $this->order->products()->sync($products);

        $this->redirect(route('orders.index'));
    }
    protected function initListsForFields(): void
    {
        $this->listsForFields['users'] = User::pluck('name', 'id')->toArray();

        $this->allProducts = Product::all();
    }
    public function addProduct(): void
    {
        foreach ($this->orderProducts as $key => $product) {
            if (!$product['is_saved']) {
                $this->addError('orderProducts.' . $key, 'This line must be saved before creating a new one.');
                return;
            }
        }

        $this->orderProducts[] = [
            'product_id' => '',
            'quantity' => 1,
            'is_saved' => false,
            'product_name' => '',
            'product_price' => 0
        ];
    }
    public function saveProduct($index): void
    {
        $this->resetErrorBag();
        $product = $this->allProducts->find($this->orderProducts[$index]['product_id']);
        $this->orderProducts[$index]['product_name'] = $product->name;
        $this->orderProducts[$index]['product_price'] = $product->price;
        $this->orderProducts[$index]['is_saved'] = true;
    }
    public function editProduct($index): void
    {
        foreach ($this->orderProducts as $key => $invoiceProduct) {
            if (!$invoiceProduct['is_saved']) {
                $this->addError('$this->orderProducts.' . $key, 'This line must be saved before editing another.');
                return;
            }
        }

        $this->orderProducts[$index]['is_saved'] = false;
    }

    public function removeProduct($index): void
    {
        unset($this->orderProducts[$index]);
        $this->orderProducts = array_values($this->orderProducts);
    }
}
