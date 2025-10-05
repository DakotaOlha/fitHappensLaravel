<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAIService
{
    protected $apiKey;
    protected $apiUrl;

    public function __construct()
    {
        $this->apiKey = config('services.openai.key');
        $this->apiUrl = 'https://api.openai.com/v1/chat/completions';
    }

    public function getColorType(string $prompt) : string
    {
        if (!$this->apiKey) {
            throw new \Exception('OpenAI API ключ не налаштований (OPENAI_API_KEY)');
        }

        try {
            $response = Http::withHeaders([
                'Content-Type'  => 'application/json',
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->timeout(30)->post($this->apiUrl, [
                'model'       => 'gpt-4o-mini',
                'messages'    => [
                    [
                        'role'    => 'system',
                        'content' => 'Ти експерт з визначення кольоротипів. Відповідай тільки одним словом: Весна, Літо, Осінь або Зима.'
                    ],
                    [
                        'role'    => 'user',
                        'content' => $prompt
                    ],
                ],
                'temperature' => 0.3,
                'max_tokens'  => 50,
            ]);

            if ($response->failed()) {
                Log::error('OpenAI API помилка', [
                    'status' => $response->status(),
                    'body'   => $response->body()
                ]);

                if ($response->status() == 401) {
                    throw new \Exception('Невірний OpenAI API ключ');
                }

                throw new \Exception('Помилка при зверненні до OpenAI API: ' . $response->body());
            }

            $data = $response->json();

            if (!isset($data['choices'][0]['message']['content'])) {
                throw new \Exception('API повернув некоректну відповідь');
            }

            $colorType = trim($data['choices'][0]['message']['content']);

            $validTypes = ['Весна', 'Літо', 'Осінь', 'Зима'];

            if (!in_array($colorType, $validTypes)) {
                Log::warning('OpenAI повернув невалідний кольоротип: ' . $colorType);

                foreach ($validTypes as $validType) {
                    if (stripos($colorType, $validType) !== false) {
                        $colorType = $validType;
                        break;
                    }
                }
            }

            return $colorType;
        }
        catch (\Exception $e) {
            Log::error('Помилка OpenAI Service: ' . $e->getMessage());
            throw $e;
        }
    }
}
