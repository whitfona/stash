<?php

use App\Models\Item;
use Livewire\Component;

new class extends Component
{
    public Item $item;

    public function mount(Item $item): void
    {
        $this->item = $item;
    }

    public function delete(): void
    {
        $this->item->photos()->each(fn ($p) => $p->delete());
        $this->item->delete();
        $this->redirect(route('items.index'), navigate: true);
    }

    public function with(): array
    {
        $this->item->load(['location.parent', 'photos']);

        return ['item' => $this->item];
    }
};
?>

<x-layouts.app :title="$item->name">
    <div class="p-4">
        <div class="mb-4 flex items-center gap-3">
            <a href="{{ route('items.index') }}" class="text-indigo-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <h1 class="flex-1 min-w-0 text-xl font-bold text-gray-900 truncate">{{ $item->name }}</h1>
            <a href="{{ route('items.edit', $item) }}"
               class="shrink-0 rounded-full bg-gray-100 px-3 py-1 text-sm font-medium text-gray-700 hover:bg-gray-200">
                Edit
            </a>
        </div>

        {{-- Photos --}}
        @if($item->photos->isNotEmpty())
            <div class="mb-4 flex gap-2 overflow-x-auto pb-1">
                @foreach($item->photos as $photo)
                    <img src="{{ $photo->url }}" alt="{{ $photo->caption }}"
                         class="h-40 w-40 shrink-0 rounded-xl object-cover">
                @endforeach
            </div>
        @endif

        {{-- Location --}}
        <div class="mb-4 rounded-xl bg-indigo-50 px-4 py-4">
            <p class="mb-1 text-xs font-semibold uppercase tracking-wide text-indigo-400">Location</p>
            @if($item->location)
                <a href="{{ route('locations.show', $item->location) }}"
                   class="text-base font-semibold text-indigo-900">
                    {{ $item->location->fullPath() }}
                </a>
            @else
                <p class="text-sm text-gray-500 italic">Location not set</p>
            @endif
        </div>

        @if($item->aliases)
            <div class="mb-3">
                <p class="mb-1 text-xs font-semibold uppercase tracking-wide text-gray-400">Also known as</p>
                <p class="text-sm text-gray-700">{{ implode(', ', $item->aliases) }}</p>
            </div>
        @endif

        @if($item->description)
            <div class="mb-3">
                <p class="mb-1 text-xs font-semibold uppercase tracking-wide text-gray-400">Description</p>
                <p class="text-sm text-gray-700">{{ $item->description }}</p>
            </div>
        @endif

        @if($item->tags)
            <div class="mb-6 flex flex-wrap gap-2">
                @foreach($item->tags as $tag)
                    <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-600">{{ $tag }}</span>
                @endforeach
            </div>
        @endif

        <button wire:click="delete" wire:confirm="Delete this item?"
                class="w-full rounded-xl border border-red-200 py-3 text-sm font-medium text-red-600 hover:bg-red-50">
            Delete Item
        </button>
    </div>
</x-layouts.app>
