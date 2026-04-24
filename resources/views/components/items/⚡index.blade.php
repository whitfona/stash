<?php

use App\Models\Item;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function with(): array
    {
        return [
            'items' => Item::with(['location', 'photos'])
                ->when($this->search, function ($q) {
                    $q->where(function ($q) {
                        $q->where('name', 'like', "%{$this->search}%")
                          ->orWhere('description', 'like', "%{$this->search}%")
                          ->orWhere('tags', 'like', "%{$this->search}%");
                    });
                })
                ->orderBy('name')
                ->paginate(30),
        ];
    }
};
?>

<x-layouts.app title="Items">
    <div class="p-4">
        <div class="mb-4 flex items-center justify-between">
            <h1 class="text-xl font-bold text-gray-900">Items</h1>
        </div>

        <div class="mb-4">
            <input wire:model.live.debounce.300ms="search" type="search" placeholder="Filter items…"
                   class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-base focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200">
        </div>

        @if($items->isEmpty())
            <div class="mt-10 flex flex-col items-center justify-center text-center text-gray-400">
                <p class="text-sm">{{ $search ? 'No items match your search.' : 'No items yet. Add your first one!' }}</p>
            </div>
        @else
            <div class="space-y-2">
                @foreach($items as $item)
                    <a href="{{ route('items.show', $item) }}"
                       class="flex items-center gap-3 rounded-xl bg-white px-4 py-3 shadow-sm">
                        @if($item->photos->isNotEmpty())
                            <img src="{{ $item->photos->first()->url }}" alt=""
                                 class="h-12 w-12 shrink-0 rounded-lg object-cover">
                        @else
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-gray-100 text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h16a2 2 0 0 0-2-2V9a2 2 0 0 0-2-2z"/>
                                </svg>
                            </div>
                        @endif
                        <div class="min-w-0 flex-1">
                            <p class="font-medium text-gray-900 truncate">{{ $item->name }}</p>
                            <p class="text-xs text-gray-500 truncate">
                                {{ $item->location?->fullPath() ?? 'No location set' }}
                            </p>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                @endforeach
            </div>

            <div class="mt-4">
                {{ $items->links() }}
            </div>
        @endif
    </div>
</x-layouts.app>
