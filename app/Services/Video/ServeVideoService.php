<?php

namespace App\Services\Video;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ServeVideoService
{
    /**
     * Génère le contenu d'une vidéo HLS en fonction du type (master / 480p / 720p)
     *
     * @param string $titre
     * @param string $videoHash
     * @param string $type
     * @param string|null $segment
     * @return string Contenu du fichier m3u8 prêt à être renvoyé
     *
     * @throws \RuntimeException
     */
    public function __invoke(string $titre, string $videoHash, string $type, ?string $segment = null): string
    {
        $basePath = storage_path("app/videos/{$titre}");

        // Si c'est une demande de segment TS
        if ($segment) {
            Log::info('Segment demandé: ' . $segment);
            $filepath = storage_path("app/videos/{$titre}/{$type}/{$segment}");
            if (!file_exists($filepath)) {
                throw new \RuntimeException("Segment introuvable: {$filepath}");
            }
            return $filepath;
        }

        // Si c'est la playlist master
        if ($type === 'master') {
            return $this->generateMasterPlaylist($basePath, $videoHash, $titre);
        }

        // Si c'est une playlist de qualité spécifique (480p, 720p, etc.)
        return $this->generateQualityPlaylist($basePath, $type, $videoHash, $titre);
    }

    private function generateMasterPlaylist(string $basePath, string $videoHash, string $titre): string
    {
        $masterPath = "{$basePath}/master.m3u8";
        if (!File::exists($masterPath)) {
            throw new \RuntimeException('Fichier master.m3u8 introuvable');
        }

        $lines = file($masterPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as &$line) {
            // Regex plus précise pour les références de playlist
            if (preg_match('#^(\d{3,4}p)/.*\.m3u8$#', $line, $matches)) {
                $quality = $matches[1];
                $line = url("/api/video/serve?video={$videoHash}&titre=" . urlencode($titre) . "&type={$quality}");
            }
        }

        return implode("\n", $lines);
    }

    private function generateQualityPlaylist(string $basePath, string $type, string $videoHash, string $titre): string
    {
        $indexPath = "{$basePath}/{$type}/{$type}.m3u8";
        if (!File::exists($indexPath)) {
            throw new \RuntimeException("Fichier playlist introuvable: {$indexPath}");
        }

        $content = File::get($indexPath);
        
        Log::info("Contenu original de la playlist {$type}:", ['content' => $content]);

        // Regex plus précise pour les segments TS
        $content = preg_replace_callback('/^(?!#)(.+\.ts)\s*$/m', function ($matches) use ($titre, $type, $videoHash) {
            $segment = trim($matches[1]);
            
            Log::info("Segment trouvé: {$segment}");
            
            // Construction de l'URL sans double encoding
            $params = http_build_query([
                'video' => $videoHash,
                'titre' => $titre,
                'type' => $type,
                'segment' => $segment
            ]);
            
            $url = url("api/video/serve?{$params}");
            
            Log::info("URL générée: {$url}");
            
            return $url;
        }, $content);

        Log::info("Contenu modifié de la playlist {$type}:", ['content' => $content]);

        return $content;
    }
}