<?php

namespace App\Services;

use App\Models\Item;
use Illuminate\Support\Collection;

class SearchService
{
    public function query(string $question): array
    {
        $terms = $this->extractTerms($question);

        if (empty($terms)) {
            return [
                'answer' => 'Try searching for an item name, like "winter clothes" or "wrapping paper".',
                'items' => collect(),
            ];
        }

        $items = Item::with(['location', 'photos'])
            ->where(function ($query) use ($terms) {
                foreach ($terms as $term) {
                    $query->where(function ($q) use ($term) {
                        $q->where('name', 'like', "%{$term}%")
                            ->orWhere('description', 'like', "%{$term}%")
                            ->orWhere('aliases', 'like', "%{$term}%")
                            ->orWhere('tags', 'like', "%{$term}%");
                    });
                }
            })
            ->get();

        return [
            'answer' => $this->buildAnswer($question, $items),
            'items' => $items,
        ];
    }

    /** Strip filler words from natural-language questions to get searchable terms. */
    private function extractTerms(string $question): array
    {
        $stopWords = ['where', 'is', 'are', 'my', 'the', 'a', 'an', 'i', 'did', 'put', 'find', 'do', 'have', 'was', 'were', 'it', 'of', 'in', 'at', 'to', 'for'];

        $words = preg_split('/\s+/', strtolower(preg_replace('/[^\w\s]/', '', $question)));

        return array_values(array_filter($words, fn ($w) => strlen($w) > 1 && ! in_array($w, $stopWords)));
    }

    private function buildAnswer(string $question, Collection $items): string
    {
        if ($items->isEmpty()) {
            return "I couldn't find anything matching that. Try a different word, or add the item using the + button.";
        }

        if ($items->count() === 1) {
            $item = $items->first();
            $location = $item->location?->fullPath() ?? 'an unknown location';

            return "Your {$item->name} is in {$location}.";
        }

        $names = $items->take(3)->pluck('name')->implode(', ');
        $extra = $items->count() > 3 ? ' and '.($items->count() - 3).' more' : '';

        return "I found {$items->count()} matching items: {$names}{$extra}. Tap one to see exactly where it is.";
    }
}
