<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Product;

class Products extends Component
{
    use WithPagination;

    public $searchTerm = ''; // Search term from input
    public $products = []; // Array to store multiple products
    public $product = []; // Single product for editing
    public $isEdit = false;
    public $title = 'Add New Product';
    public $shouldCloseModal = false;

    protected $paginationTheme = 'bootstrap';

    protected $rules = [
        'products.*.name' => 'required|string|max:255',
        'products.*.description' => 'required|string|max:1000',
        'product.name' => 'required|string|max:255',
        'product.description' => 'required|string|max:1000',
    ];

    public function mount()
    {
        $this->resetFields();
    }

    public function updatingSearchTerm()
    {
        $this->resetPage();
    }

    public function resetFields()
    {
        $this->title = 'Add New Product';
        $this->products = [['name' => '', 'description' => '']];
        $this->product = ['name' => '', 'description' => ''];
        $this->isEdit = false;
    }

    public function addForm()
    {
        $this->products[] = ['name' => '', 'description' => ''];
    }

    public function removeProduct($index)
    {
        unset($this->products[$index]);
        $this->products = array_values($this->products); // Reindex array
    }

    public function save()
    {
        if ($this->isEdit) {
            // Validate product for edit
            $this->validate([
                'product.name' => 'required|string|max:255',
                'product.description' => 'required|string|max:1000',
            ]);

            $product = $this->product;
            Product::updateOrCreate(
                ['id' => $product['id']],
                ['name' => $product['name'], 'description' => $product['description']]
            );
            session()->flash('message', 'Product Successfully Updated.');
        } else {
            // Validate products for add
            $this->validate([
                'products.*.name' => 'required|string|max:255',
                'products.*.description' => 'required|string|max:1000',
            ]);

            foreach ($this->products as $product) {
                Product::create([
                    'name' => $product['name'],
                    'description' => $product['description']
                ]);
            }
            session()->flash('message', 'Product Successfully Added.');
        }

        $this->resetFields();
        $this->shouldCloseModal = true;
        $this->dispatch('close-modal');

        return redirect('/produk');
    }


    public function edit($id)
    {
        $this->title = 'Edit Product';
        $product = Product::findOrFail($id);
        $this->product = ['id' => $id, 'name' => $product->name, 'description' => $product->description];
        $this->isEdit = true;
    }

    public function delete($id)
    {
        Product::find($id)->delete();
    }

    public function cancel()
    {
        $this->resetFields();
        if ($this->isEdit) {
            $this->reset(); // Mereset state internal komponen
        }
        $this->dispatch('close-modal');
    }

    public function render()
    {
        $products_read = Product::where('name', 'like', '%' . $this->searchTerm . '%')
            ->orWhere('description', 'like', '%' . $this->searchTerm . '%')
            ->latest()
            ->paginate(5);

        return view('livewire.products', ['products_read' => $products_read]);
    }
}