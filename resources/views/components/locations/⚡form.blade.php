<?php

use App\Models\Location;
use Livewire\Component;

new class extends Component
{
    public ?int $locationId = null;

    public string $name = '';
    public string $notes = '';
    public ?int $parent_id = null;

    public function mount(?Location $location = null, ?int $parent_id = null): void
    {
        if ($location && $location->exists) {
            $this->locationId = $location->id;
            $this->name = $location->name;
            $this->notes = $location->notes ?? '';
            $this->parent_id = $location->parent_id;
        } else {
            $this->parent_id = $parent_id;
        }
    }

    public function save(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'parent_id' => ['nullable', 'exists:locations,id'],
        ]);

        $data = [
            'name' => $this->name,
            'notes' => $this->notes ?: null,
            'parent_id' => $this->parent_id ?: null,
        ];

        if ($this->locationId) {
            Location::findOrFail($this->locationId)->update($data);
        } else {
            Location::create($data);
        }

        $this->redirect(route('locations.index'), navigate: true);
    }

    public function with(): array
    {
        return [
            'locations' => Location::when($this->locationId, fn ($q) => $q->where('id', '!=', $this->locationId))
                ->orderBy('name')
                ->get(),
            'isEditing' => (bool) $this->locationId,
        ];
    }
};
?>

<x-layouts.app :title="$isEditing ? 'Edit Location' : 'Add Location'">
    <div class="p-4">
        <div class="mb-4 flex items-center gap-3">
            <a href="{{ route('locations.index') }}" class="text-indigo-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <h1 class="text-xl font-bold text-gray-900">{{ $isEditing ? 'Edit Location' : 'Add Location' }}</h1>
        </div>

        <form wire:submit="save" class="space-y-4">
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Name</label>
                <input wire:model="name" type="text" placeholder="e.g. Under the guest bed"
                       class="w-full rounded-xl border border-gray-300 px-4 py-3 text-base focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200">
                @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Parent Location <span class="text-gray-400">(optional)</span></label>
                <select wire:model="parent_id"
                        class="w-full rounded-xl border border-gray-300 px-4 py-3 text-base focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200">
                    <option value="">— None (top level) —</option>
                    @foreach($locations as $loc)
                        <option value="{{ $loc->id }}">{{ $loc->fullPath() }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Notes <span class="text-gray-400">(optional)</span></label>
                <textarea wire:model="notes" rows="3" placeholder="Any extra details…"
                          class="w-full rounded-xl border border-gray-300 px-4 py-3 text-base focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200"></textarea>
            </div>

            <button type="submit"
                    class="w-full rounded-xl bg-indigo-700 py-3 text-base font-semibold text-white hover:bg-indigo-600 active:bg-indigo-800">
                <span wire:loading.remove>{{ $isEditing ? 'Save Changes' : 'Add Location' }}</span>
                <span wire:loading>Saving…</span>
            </button>
        </form>
    </div>
</x-layouts.app>
