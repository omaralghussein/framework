<?php

namespace Shopper\Framework\Http\Livewire\Modals;

use Illuminate\Database\Eloquent\Model;
use Livewire\WithPagination;
use LivewireUI\Modal\ModalComponent;
use Shopper\Framework\Repositories\Ecommerce\ProductRepository;
use Shopper\Framework\Repositories\InventoryHistoryRepository;
use Shopper\Framework\Repositories\InventoryRepository;
use Shopper\Framework\Traits\WithStock;

class UpdateVariantStock extends ModalComponent
{
    use WithPagination;
    use WithStock;

    public Model $product;

    public function mount(int $id)
    {
        $this->product = $variant = (new ProductRepository())->getById($id);
        $this->stock = $variant->stock;
        $this->realStock = $variant->stock;
    }

    public static function modalMaxWidth(): string
    {
        return '3xl';
    }

    public function paginationView(): string
    {
        return 'shopper::livewire.wire-pagination-links';
    }

    public function render()
    {
        return view('shopper::livewire.modals.update-variant-stock', [
            'currentStock' => (new InventoryHistoryRepository())
                ->where('inventory_id', $this->inventory)
                ->where('stockable_id', $this->product->id)
                ->get()
                ->sum('quantity'),
            'histories' => (new InventoryHistoryRepository())
                ->where('inventory_id', $this->inventory)
                ->where('stockable_id', $this->product->id)
                ->orderBy('created_at', 'desc')
                ->paginate(3),
            'inventories' => (new InventoryRepository())->all(),
        ]);
    }
}
