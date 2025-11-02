<?php

namespace App\Helpers;

use Spatie\PdfToText\Pdf;

class TextProcessor
{
    public static function extractTextFromPDF(string $filePath): string
    {
        return Pdf::getText($filePath);
    }

    public static function preprocessText(string $text): string
    {
        $text = strtolower($text); // Convertir en minuscule
        $text = preg_replace('/[^a-z0-9\s]/', '', $text); // Supprimer les caractères spéciaux
        $text = preg_replace('/\s+/', ' ', $text); // Supprimer les espaces multiples
        return trim($text);
    }
}
