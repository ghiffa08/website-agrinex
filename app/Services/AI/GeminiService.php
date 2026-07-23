<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class GeminiService
{
    protected string $apiKey;
    protected string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/';
    protected string $model = 'gemini-1.5-flash'; // Optimal untuk free plan
    
    // Rate limiting untuk Gemini Free Plan: 15 RPM
    protected int $maxRequestsPerMinute = 15;
    protected string $rateLimitKey = 'gemini_rate_limit';
    
    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key') ?? env('GEMINI_API_KEY');
        
        if (!$this->apiKey) {
            throw new \Exception('GEMINI_API_KEY not configured in .env');
        }
    }
    
    /**
     * Check rate limit before making request
     */
    protected function checkRateLimit(): bool
    {
        $requests = Cache::get($this->rateLimitKey, []);
        $now = now()->timestamp;
        
        // Remove requests older than 1 minute
        $requests = array_filter($requests, fn($timestamp) => ($now - $timestamp) < 60);
        
        if (count($requests) >= $this->maxRequestsPerMinute) {
            Log::warning('Gemini rate limit reached', [
                'current_requests' => count($requests),
                'limit' => $this->maxRequestsPerMinute
            ]);
            return false;
        }
        
        // Add current request
        $requests[] = $now;
        Cache::put($this->rateLimitKey, $requests, now()->addMinutes(2));
        
        return true;
    }
    
    /**
     * Send prompt to Gemini API
     */
    public function generateContent(string $prompt, array $options = []): array
    {
        if (!$this->checkRateLimit()) {
            return [
                'success' => false,
                'error' => 'Rate limit exceeded. Please wait a moment.',
                'retry_after' => 60
            ];
        }
        
        try {
            $response = Http::timeout(30)
                ->post("{$this->baseUrl}{$this->model}:generateContent?key={$this->apiKey}", [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt]
                            ]
                        ]
                    ],
                    'generationConfig' => array_merge([
                        'temperature' => 0.4, // Lower untuk consistency
                        'topK' => 32,
                        'topP' => 0.95,
                        'maxOutputTokens' => 2048,
                    ], $options)
                ]);
            
            if ($response->failed()) {
                Log::error('Gemini API request failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                
                return [
                    'success' => false,
                    'error' => 'API request failed: ' . $response->status()
                ];
            }
            
            $data = $response->json();
            
            // Extract text from response
            $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
            
            if (!$text) {
                return [
                    'success' => false,
                    'error' => 'No text content in response'
                ];
            }
            
            return [
                'success' => true,
                'text' => $text,
                'raw_response' => $data
            ];
            
        } catch (\Exception $e) {
            Log::error('Gemini API exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Parse JSON response from Gemini (handles markdown code blocks)
     */
    public function extractJson(string $text): ?array
    {
        // Remove markdown code blocks if present
        $text = preg_replace('/```json\s*(.*?)\s*```/s', '$1', $text);
        $text = preg_replace('/```\s*(.*?)\s*```/s', '$1', $text);
        $text = trim($text);
        
        try {
            return json_decode($text, true);
        } catch (\Exception $e) {
            Log::warning('Failed to parse JSON from Gemini response', [
                'text' => $text,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
}
