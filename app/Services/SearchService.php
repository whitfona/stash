<?php

namespace App\Services;

use Anthropic\Client;
use App\Models\Item;

class SearchService
{
    public function __construct(private readonly Client $client) {}

    public function query(string $question): array
    {
        $items = Item::with('location')->get();

        if ($items->isEmpty()) {
            return [
                'answer' => "You haven't added any items yet. Tap the + button to add your first item!",
                'items' => collect(),
            ];
        }

        $index = $items->map(fn (Item $item) => $item->toSearchEntry())->values()->toJson();

        $prompt = <<<PROMPT
You are a helpful home inventory assistant. The user stores items in their house and wants to find where things are.

Below is a JSON index of all items in the inventory. Each item has a name, optional aliases, description, tags, and location (as a full path like "Guest Bedroom > Under the Bed > Blue Bin").

Inventory:
{$index}

The user's question: "{$question}"

Instructions:
- Answer in a single friendly, conversational sentence or two.
- If you find a match, name the item and describe exactly where it is using its full location path.
- If multiple items could match, mention all of them.
- If nothing matches, say so helpfully and suggest they add the item.
- Do not mention that you searched a JSON index.
- Also return a JSON array of matching item IDs (or empty array) on the last line in this exact format: ITEM_IDS:[1,2,3]
PROMPT;

        $response = $this->client->messages->create(
            messages: [['role' => 'user', 'content' => $prompt]],
            model: 'claude-haiku-4-5-20251001',
            maxTokens: 512,
        );

        $content = $response->content[0]->text;

        preg_match('/ITEM_IDS:\[([^\]]*)\]/', $content, $matches);
        $itemIds = isset($matches[1]) && $matches[1] !== ''
            ? array_map('intval', explode(',', $matches[1]))
            : [];

        $answer = trim(preg_replace('/ITEM_IDS:\[[^\]]*\]/', '', $content));

        $matchedItems = $itemIds
            ? Item::with(['location', 'photos'])->whereIn('id', $itemIds)->get()
            : collect();

        return [
            'answer' => $answer,
            'items' => $matchedItems,
        ];
    }
}
