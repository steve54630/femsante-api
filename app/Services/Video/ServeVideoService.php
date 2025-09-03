<?php

namespace App\Services\Video;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ServeVideoService
{
    /**
     * Génère le contenu d'une vidéo HLS en fonction du type (master / 480p / 720p)
     *
     * @param string $titre
     * @param string $videoHash
     * @param string $type
     * @return string Contenu du fichier m3u8 prêt à être renvoyé
     *
     * @throws \RuntimeException
     */
    public function __invoke(string $titre, string $videoHash, string $type): string
    {
        $basePath = storage_path("app/videos/{$titre}");

        if ($type === 'master') {
            $masterPath = "{$basePath}/master.m3u8";
            if (!File::exists($masterPath)) {
                throw new \RuntimeException('Fichier master.m3u8 introuvable');
            }

            $lines = file($masterPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as &$line) {
                if (preg_match('#^(\d{3,4}p)/index\.m3u8$#', $line, $matches)) {
                    $quality = $matches[1];
                    $line = url("/video/serve?video={$videoHash}&titre=" . urlencode($titre) . "&type={$quality}");
                }
            }

            return implode("\n", $lines);
        }

        $indexPath = "{$basePath}/{$type}/index.m3u8";
        if (!File::exists($indexPath)) {
            throw new \RuntimeException("Fichier index.m3u8 pour {$type} introuvable");
        }

        $content = File::get($indexPath);

        // Remplacement dynamique des liens ts
        $content = preg_replace_callback('/^([^\#][^\r\n]*)$/m', function ($matches) use ($titre, $type) {
            $segment = trim($matches[1]);
            if (Str::endsWith($segment, '.ts')) {
                return url("videos/{$titre}/{$type}/{$segment}");
            }
            return $segment;
        }, $content);

        return $content;
    }
}
