<?php

namespace App\Services\Video;

class GenerateVideoUrlService
{
    private string $hashSecret;

    public function __construct()
    {
        // L'authentification utilisateur est geree par auth:sanctum en amont ; ce service
        // n'a plus besoin du token statique, seulement du secret HMAC pour signer l'URL.
        // Lecture via config() (et non env() direct) pour rester compatible config:cache.
        $this->hashSecret = (string) config('services.video.hash_secret');

        if (!$this->hashSecret) {
            throw new \RuntimeException('Secret HMAC (HASH_SECRET) non configuré');
        }
    }

    public function __invoke(string $video): array
    {
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
