<?php

use App\Models\Location;
use Livewire\Component;

new class extends Component
{
    public function deleteLocation(int $id): void
    {
        $location = Location::findOrFail($id);

        if ($location->children()->exists()) {
            $this->addError('delete', 'Cannot delete a location that has sub-locations.');
            return;
        }

        if ($location->items()->exists()) {
            $this->addError('delete', 'Cannot delete a location that contains items.');
            return;
        }

        $location->delete();
    }

    public function with(): array
    {
        return [
            'roots' => Location::with('allChildren')->whereNull('parent_id')->get(),
        ];
    }
};
?>

<x-layouts.app title="Locations">
    <div class="p-4">
        <div class="mb-4 flex items-center justify-between">
            <h1 class="text-xl font-bold text-gray-900">Locations</h1>
            <a href="{{ route('locations.create') }}"
               class="rounded-full bg-indigo-700 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-600">
                + Add
            </a>
        </div>

        @error('delete')
            <div class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700">{{ $message }}</div>
        @enderror

        @if($roots->isEmpty())
            <div class="mt-10 flex flex-col items-center justify-center text-center text-gray-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="mb-2 h-12 w-12 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7h18M3 12h18M3 17h18"/>
                </svg>
                <p class="text-sm">No locations yet. Add your first one!</p>
            </div>
        @else
            <div class="space-y-2">
                @foreach($roots as $root)
                    <x-location-tree :location="$root" :depth="0"/>
                @endforeach
            </div>
        @endif
    </div>
</x-layouts.app>
