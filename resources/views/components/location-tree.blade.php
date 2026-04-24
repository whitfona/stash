@props(['location', 'depth' => 0])

<div class="{{ $depth > 0 ? 'ml-4 border-l border-gray-200 pl-3' : '' }}">
    <div class="flex items-center gap-2 rounded-lg bg-white px-3 py-3 shadow-sm">
        <div class="flex-1 min-w-0">
            <a href="{{ route('locations.show', $location) }}" class="block font-medium text-gray-900 truncate">
                {{ $location->name }}
            </a>
            @if($location->notes)
                <p class="text-xs text-gray-500 truncate">{{ $location->notes }}</p>
            @endif
            <p class="text-xs text-indigo-500">
                {{ $location->items_count ?? $location->items->count() }} item(s)
                @if($location->children->isNotEmpty())
                    · {{ $location->children->count() }} sub-location(s)
                @endif
            </p>
        </div>
        <div class="flex shrink-0 gap-2">
            <a href="{{ route('locations.create', ['parent_id' => $location->id]) }}"
               class="text-xs text-indigo-600 hover:text-indigo-800">+ Sub</a>
            <a href="{{ route('locations.edit', $location) }}"
               class="text-xs text-gray-500 hover:text-gray-700">Edit</a>
        </div>
    </div>

    @if($location->children->isNotEmpty())
        <div class="mt-1 space-y-1">
            @foreach($location->children as $child)
                <x-location-tree :location="$child" :depth="$depth + 1"/>
            @endforeach
        </div>
    @endif
</div>
