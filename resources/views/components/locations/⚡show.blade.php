<?php

use App\Models\Location;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('components.layouts.app')] class extends Component
{
    public Location $location;

    public function mount(Location $location): void
    {
        $this->location = $location;
    }

    public function title(): string
    {
        return $this->location->name;
    }

    public function with(): array
    {
        $this->location->load(['parent', 'children', 'items.photos', 'photos']);

        return ['location' => $this->location];
    }
};
?>

<div class="p-4">
    <div class="mb-4 flex items-center gap-3">
        <a href="{{ $location->parent_id ? route('locations.show', $location->parent) : route('locations.index') }}"
           class="text-indigo-600">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div class="min-w-0 flex-1">
            <h1 class="truncate text-xl font-bold text-gray-900">{{ $location->name }}</h1>
            @if($location->parent)
                <p class="truncate text-xs text-gray-500">in {{ $location->parent->fullPath() }}</p>
            @endif
        </div>
        <a href="{{ route('locations.edit', $location) }}"
           class="shrink-0 rounded-full bg-gray-100 px-3 py-1 text-sm font-medium text-gray-700 hover:bg-gray-200">
            Edit
        </a>
    </div>

    @if($location->notes)
        <p class="mb-4 text-sm text-gray-600">{{ $location->notes }}</p>
    @endif

    @if($location->photos->isNotEmpty())
        <div class="mb-4 flex gap-2 overflow-x-auto pb-1">
            @foreach($location->photos as $photo)
                <img src="{{ $photo->url }}" alt="{{ $photo->caption }}"
                     class="h-24 w-24 shrink-0 rounded-xl object-cover">
            @endforeach
        </div>
    @endif

    @if($location->children->isNotEmpty())
        <h2 class="mb-2 text-sm font-semibold uppercase tracking-wide text-gray-500">Sub-locations</h2>
        <div class="mb-4 space-y-2">
            @foreach($location->children as $child)
                <a href="{{ route('locations.show', $child) }}"
                   class="flex items-center justify-between rounded-xl bg-white px-4 py-3 shadow-sm">
                    <span class="font-medium text-gray-900">{{ $child->name }}</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            @endforeach
        </div>
    @endif

    <div class="mb-2 flex items-center justify-between">
        <h2 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Items here</h2>
        <a href="{{ route('items.create', ['location_id' => $location->id]) }}"
           class="text-sm font-medium text-indigo-600">+ Add item</a>
    </div>

    @if($location->items->isEmpty())
        <p class="py-4 text-center text-sm text-gray-400">No items stored here yet.</p>
    @else
        <div class="space-y-2">
            @foreach($location->items as $item)
                <a href="{{ route('items.show', $item) }}"
                   class="flex items-center gap-3 rounded-xl bg-white px-4 py-3 shadow-sm">
                    @if($item->photos->isNotEmpty())
                        <img src="{{ $item->photos->first()->url }}" alt=""
                             class="h-12 w-12 shrink-0 rounded-lg object-cover">
                    @else
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-gray-100 text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/>
                            </svg>
                        </div>
                    @endif
                    <div class="min-w-0 flex-1">
                        <p class="truncate font-medium text-gray-900">{{ $item->name }}</p>
                        @if($item->description)
                            <p class="truncate text-xs text-gray-500">{{ $item->description }}</p>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>
