<?php

namespace App\Services\Video;

class GenerateVideoUrlService
{
    private string $apiToken;
    private string $hashSecret;

    public function __construct()
    {
        // Lecture via config() (et non env() direct) pour rester compatible config:cache.
        $this->apiToken = (string) config('services.video.token');
        $this->hashSecret = (string) config('services.video.hash_secret');

        if (!$this->apiToken || !$this->hashSecret) {
            throw new \RuntimeException('Token API ou hash non configuré');
        }
    }

    public function __invoke(string $video, ?string $authorizationHeader = null): array
    {
        // Vérification de l'autorisation à temps constant (évite les timing attacks).
        $expected = 'Bearer ' . $this->apiToken;
        if (!is_string($authorizationHeader) || !hash_equals($expected, $authorizationHeader)) {
            return [
                'success' => false,
                'error' => 'Accès non autorisé',
                'http_code' => 401,
            ];
        }

        if (!$video) {
            return [
                'success' => false,
                'error' => 'Paramètre video manquant',
                'http_code' => 400,
            ];
        }

        $slug = $this->slugify($video);
        $videoHash = hash_hmac('sha256', $slug.'/master.m3u8', $this->hashSecret);

        $url = url("/api/video/serve?video={$videoHash}&titre={$slug}&type=master");

        return [
            'success' => true,
            'url' => $url,
            'http_code' => 200,
        ];
    }

    private function slugify(string $text): string
    {
        $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text);
        $text = preg_replace('/[^A-Za-z0-9]+/', '-', $text);
        return strtolower(trim($text, '-'));
    }
}
