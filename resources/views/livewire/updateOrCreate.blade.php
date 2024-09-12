<!-- Modal Structure -->
<div wire:ignore.self class="modal fade" id="productModal" aria-labelledby="productModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="productModalLabel">{{ $title }}</h5>
                <button type="button" class="btn-close" wire:click="cancel" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form wire:submit.prevent="save">
                    <!-- Single Product Form for Edit -->
                    @if($isEdit)
                        <div class="mb-3">
                            <!-- Product Name -->
                            <label for="name" class="form-label">Product Name</label>
                            <input type="text" wire:model.defer="product.name" class="form-control"
                                   :class="{'is-invalid': errors['product.name']}">
                            @error('product.name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror

                            <!-- Product Description -->
                            <label for="description" class="form-label">Product Description</label>
                            <div wire:ignore x-data="{ {$product['description']} }" x-init="
                                ClassicEditor.create($refs.editor)
                                    .then(newEditor => {
                                        editor = newEditor;
                                        editor.model.document.on('change:data', () => {
                                            @this.set('product.description', editor.getData());
                                        });
                                    })
                                    .catch(error => {
                                        console.error(error);
                                    })
                            ">
                                <textarea x-ref="editor" class="form-control">{{ $product['description'] ?? '' }}</textarea>
                            </div>
                            @error('product.description')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    @else
                        <!-- Loop through products for add mode -->
                        @foreach($products as $key => $prod)
                            <div class="mb-3">
                                <!-- Product Name -->
                                <label for="name_{{ $key }}" class="form-label">Product Name</label>
                                <input type="text" wire:model.defer="products.{{ $key }}.name" class="form-control"
                                       :class="{'is-invalid': errors['products.{{ $key }}.name']}">
                                @error('products.' . $key . '.name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror

                                <!-- Product Description -->
                                <label for="description_{{ $key }}" class="form-label">Product Description</label>
                                <div wire:ignore x-data="{ {products.*.$key.description} }" x-init="
                                    ClassicEditor.create($refs.editor_{{ $key }})
                                        .then(newEditor => {
                                            editor = newEditor;
                                            editor.model.document.on('change:data', () => {
                                                @this.set('products.{{ $key }}.description', editor.getData());
                                            });
                                        })
                                        .catch(error => {
                                            console.error(error);
                                        })
                                ">
                                    <textarea x-ref="editor_{{ $key }}" class="form-control">{{ $products['description'] ?? '' }}</textarea>
                                </div>
                                @error('products.' . $key . '.description')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror

                                <!-- Remove Button -->
                                <button type="button" class="btn btn-danger mt-2" wire:click="removeProduct({{ $key }})">
                                    Remove
                                </button>
                            </div>
                        @endforeach
                    @endif

                    <!-- Conditional Add New Product Button -->
                    @if(!$isEdit)
                        <button type="button" class="btn btn-info mb-3" wire:click="addForm">
                            Add New Product
                        </button>
                    @endif

                    <!-- Submit Button -->
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-success me-2">Save</button>
                        <button type="button" wire:click="cancel" class="btn btn-danger" data-bs-dismiss="modal">
                            Cancel
                        </button>
                    </div>
                    <div wire:loading class="text-primary mt-2">Processing...</div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- CKEditor Script -->
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.0/classic/ckeditor.js"></script>

<!-- JavaScript for handling dynamic product addition and removal -->
<script>
    document.addEventListener('livewire:load', function () {
        Livewire.on('removeProduct', key => {
            // Implement the removal logic for product with given key
            Livewire.dispatch('removeProduct', key);
        });
    });
</script>
