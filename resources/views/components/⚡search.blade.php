<?php

use App\Services\SearchService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

new #[Layout('components.layouts.app')] #[Title('Search')] class extends Component
{
    #[Validate('required|string|min:2')]
    public string $query = '';

    public ?string $answer = null;
    public $matchedItems = null;
    public bool $hasSearched = false;

    public function search(SearchService $searchService): void
    {
        $this->validate();

        $this->hasSearched = true;

        $result = $searchService->query($this->query);

        $this->answer = $result['answer'];
        $this->matchedItems = $result['items'];
    }

    public function clear(): void
    {
        $this->query = '';
        $this->answer = null;
        $this->matchedItems = null;
        $this->hasSearched = false;
    }
};
?>

<div class="flex flex-col p-4">
    {{-- Hero --}}
    <div class="mb-6 pt-4 text-center">
        <h1 class="text-2xl font-bold text-gray-900">Where is it?</h1>
        <p class="mt-1 text-sm text-gray-500">Search for anything in your home</p>
    </div>

    {{-- Search form --}}
    <form wire:submit="search" class="mb-6">
        <div class="flex gap-2">
            <div class="relative flex-1">
                <input
                    wire:model="query"
                    id="search-input"
                    type="text"
                    placeholder="e.g. Where is the wrapping paper?"
                    autocomplete="off"
                    class="w-full rounded-2xl border border-gray-300 bg-white py-4 pl-4 pr-12 text-base shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200"
                >
                {{-- Mic button --}}
                <button
                    type="button"
                    id="mic-btn"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 transition-colors hover:text-indigo-600"
                    title="Search by voice"
                >
                    <svg id="mic-icon" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3zM19 10v2a7 7 0 0 1-14 0v-2M12 19v4M8 23h8"/>
                    </svg>
                </button>
            </div>
            <button type="submit"
                    class="rounded-2xl bg-indigo-700 px-5 py-4 font-semibold text-white shadow-sm hover:bg-indigo-600 active:bg-indigo-800">
                Search
            </button>
        </div>
        @error('query')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </form>

    {{-- Answer --}}
    @if($answer)
        <div class="mb-6 rounded-2xl bg-indigo-50 px-5 py-4">
            <p class="text-base leading-relaxed text-indigo-900">{{ $answer }}</p>
            <button wire:click="clear" class="mt-2 text-xs text-indigo-400 hover:text-indigo-600">
                Clear search
            </button>
        </div>

        {{-- Matched items --}}
        @if($matchedItems && $matchedItems->isNotEmpty())
            <div class="space-y-3">
                @foreach($matchedItems as $item)
                    <a href="{{ route('items.show', $item) }}"
                       class="flex items-center gap-3 rounded-2xl bg-white px-4 py-3 shadow-sm">
                        @if($item->photos->isNotEmpty())
                            <img src="{{ $item->photos->first()->url }}" alt=""
                                 class="h-16 w-16 shrink-0 rounded-xl object-cover">
                        @else
                            <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-xl bg-gray-100 text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/>
                                </svg>
                            </div>
                        @endif
                        <div class="min-w-0 flex-1">
                            <p class="font-semibold text-gray-900 truncate">{{ $item->name }}</p>
                            <p class="text-sm text-indigo-600 truncate">
                                {{ $item->location?->fullPath() ?? 'No location set' }}
                            </p>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                @endforeach
            </div>
        @endif
    @elseif(!$hasSearched)
        {{-- Empty state tips --}}
        <div class="mt-4 space-y-3">
            <p class="text-center text-xs font-semibold uppercase tracking-wide text-gray-400">Try asking</p>
            @foreach(['Where are my winter clothes?', 'Where is the wrapping paper?', 'Where did I put the camping gear?'] as $example)
                <button
                    type="button"
                    wire:click="$set('query', '{{ $example }}')"
                    class="w-full rounded-2xl bg-white px-4 py-3 text-left text-sm text-gray-700 shadow-sm hover:bg-gray-50">
                    "{{ $example }}"
                </button>
            @endforeach
        </div>
    @endif

    <script>
        document.addEventListener('livewire:initialized', () => {
            const micBtn = document.getElementById('mic-btn');
            const input = document.getElementById('search-input');

            if (!('webkitSpeechRecognition' in window) && !('SpeechRecognition' in window)) {
                micBtn.style.display = 'none';
                return;
            }

            const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
            const recognition = new SpeechRecognition();
            recognition.lang = 'en-US';
            recognition.continuous = false;
            recognition.interimResults = false;

            let listening = false;

            micBtn.addEventListener('click', () => {
                if (listening) { recognition.stop(); return; }
                recognition.start();
            });

            recognition.onstart = () => {
                listening = true;
                micBtn.classList.add('text-red-500');
                micBtn.classList.remove('text-gray-400');
            };

            recognition.onend = () => {
                listening = false;
                micBtn.classList.remove('text-red-500');
                micBtn.classList.add('text-gray-400');
            };

            recognition.onresult = (event) => {
                const transcript = event.results[0][0].transcript;
                @this.set('query', transcript);
                setTimeout(() => @this.call('search'), 100);
            };

            recognition.onerror = () => {
                listening = false;
                micBtn.classList.remove('text-red-500');
                micBtn.classList.add('text-gray-400');
            };
        });
    </script>
</div>
