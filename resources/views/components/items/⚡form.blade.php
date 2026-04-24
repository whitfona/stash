<?php

use App\Models\Item;
use App\Models\Location;
use App\Models\Photo;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Layout('components.layouts.app')] class extends Component
{
    use WithFileUploads;

    public ?int $itemId = null;

    public string $name = '';
    public string $aliasesText = '';
    public string $description = '';
    public string $tagsText = '';
    public ?int $location_id = null;
    public $photo = null;

    public function mount(?Item $item = null, ?int $location_id = null): void
    {
        if ($item && $item->exists) {
            $this->itemId = $item->id;
            $this->name = $item->name;
            $this->aliasesText = implode(', ', $item->aliases ?? []);
            $this->description = $item->description ?? '';
            $this->tagsText = implode(', ', $item->tags ?? []);
            $this->location_id = $item->location_id;
        } else {
            $this->location_id = $location_id;
        }
    }

    public function title(): string
    {
        return $this->itemId ? 'Edit Item' : 'Add Item';
    }

    public function save(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'aliasesText' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'tagsText' => ['nullable', 'string'],
            'location_id' => ['nullable', 'exists:locations,id'],
            'photo' => ['nullable', 'image', 'max:10240'],
        ]);

        $data = [
            'name' => $this->name,
            'aliases' => $this->aliasesText ? array_map('trim', explode(',', $this->aliasesText)) : null,
            'description' => $this->description ?: null,
            'tags' => $this->tagsText ? array_map('trim', explode(',', $this->tagsText)) : null,
            'location_id' => $this->location_id ?: null,
        ];

        if ($this->itemId) {
            $item = Item::findOrFail($this->itemId);
            $item->update($data);
        } else {
            $item = Item::create($data);
        }

        if ($this->photo) {
            $disk = config('filesystems.default') === 's3' ? 's3' : 'public';
            $path = $this->photo->store('photos', $disk);
            $item->photos()->create(['path' => $path]);
        }

        $this->redirect(route('items.show', $item), navigate: true);
    }

    public function deletePhoto(int $photoId): void
    {
        Photo::where('photoable_id', $this->itemId)
            ->where('photoable_type', Item::class)
            ->findOrFail($photoId)
            ->delete();
    }

    public function with(): array
    {
        return [
            'locations' => Location::orderBy('name')->get(),
            'isEditing' => (bool) $this->itemId,
            'existingPhotos' => $this->itemId ? Item::find($this->itemId)?->photos ?? collect() : collect(),
        ];
    }
};
?>

<div class="p-4">
    <div class="mb-4 flex items-center gap-3">
        <a href="{{ $isEditing ? route('items.show', $itemId) : route('items.index') }}"
           class="text-indigo-600">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h1 class="text-xl font-bold text-gray-900">{{ $isEditing ? 'Edit Item' : 'Add Item' }}</h1>
    </div>

    <form wire:submit="save" class="space-y-4">
        <div>
            <label class="mb-1 block text-sm font-medium text-gray-700">Name</label>
            <input wire:model="name" type="text" placeholder="e.g. Winter clothes"
                   class="w-full rounded-xl border border-gray-300 px-4 py-3 text-base focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200">
            @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="mb-1 block text-sm font-medium text-gray-700">
                Also known as <span class="text-gray-400">(comma-separated)</span>
            </label>
            <input wire:model="aliasesText" type="text" placeholder="e.g. sweaters, coats, warm clothes"
                   class="w-full rounded-xl border border-gray-300 px-4 py-3 text-base focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200">
        </div>

        <div>
            <label class="mb-1 block text-sm font-medium text-gray-700">Location</label>
            <select wire:model="location_id"
                    class="w-full rounded-xl border border-gray-300 px-4 py-3 text-base focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200">
                <option value="">— Unknown / Not set —</option>
                @foreach($locations as $loc)
                    <option value="{{ $loc->id }}">{{ $loc->fullPath() }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="mb-1 block text-sm font-medium text-gray-700">
                Description <span class="text-gray-400">(optional)</span>
            </label>
            <textarea wire:model="description" rows="2" placeholder="Any extra details…"
                      class="w-full rounded-xl border border-gray-300 px-4 py-3 text-base focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200"></textarea>
        </div>

        <div>
            <label class="mb-1 block text-sm font-medium text-gray-700">
                Tags <span class="text-gray-400">(comma-separated)</span>
            </label>
            <input wire:model="tagsText" type="text" placeholder="e.g. seasonal, holiday, tools"
                   class="w-full rounded-xl border border-gray-300 px-4 py-3 text-base focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200">
        </div>

        @if($existingPhotos->isNotEmpty())
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Current Photos</label>
                <div class="flex gap-2">
                    @foreach($existingPhotos as $photo)
                        <div class="relative">
                            <img src="{{ $photo->url }}" class="h-20 w-20 rounded-xl object-cover">
                            <button type="button" wire:click="deletePhoto({{ $photo->id }})"
                                    class="absolute -right-1 -top-1 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-xs text-white">
                                ×
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div>
            <label class="mb-1 block text-sm font-medium text-gray-700">
                Add Photo <span class="text-gray-400">(optional)</span>
            </label>
            <input wire:model="photo" type="file" accept="image/*" capture="environment"
                   class="w-full text-sm text-gray-500 file:mr-3 file:rounded-full file:border-0 file:bg-indigo-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-indigo-700">
            @error('photo') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror

            @if($photo)
                <div class="mt-2">
                    <img src="{{ $photo->temporaryUrl() }}" class="h-24 w-24 rounded-xl object-cover">
                </div>
            @endif
        </div>

        <button type="submit"
                class="w-full rounded-xl bg-indigo-700 py-3 text-base font-semibold text-white hover:bg-indigo-600 active:bg-indigo-800">
            <span wire:loading.remove>{{ $isEditing ? 'Save Changes' : 'Add Item' }}</span>
            <span wire:loading>Saving…</span>
        </button>
    </form>
</div>
