<?php

use Livewire\Livewire;
use Barryvdh\DomPDF\PDF;
use Maatwebsite\Excel\Excel;
use App\Exports\GenericExport;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use Filament\Notifications\Notification;
use Symfony\Component\HttpFoundation\Response;


if (!function_exists('highlightQuery')) {
    function highlightQuery($text, $query)
    {
        $pattern = '/(' . preg_quote($query, '/') . ')/i';
        return preg_replace($pattern, '<span class="space-x-2 font-semibold text-gray-950">$1</span>', $text);
    }
}

if (!function_exists('formatEntityName')) {
    /**
     * Formate une chaîne représentant un nom d'entité.
     *
     * Cette fonction effectue les étapes suivantes :
     * 1. Remplace les underscores (_) et tirets (-) par des espaces.
     * 2. Capitalise chaque mot si demandé.
     * 3. Insère un mot spécifique au milieu du nom.
     *
     * @param string $name Le nom à formater.
     * @param string $middleWord Le mot à insérer au milieu (par défaut : 'de').
     * @param bool $capitalize Indique si chaque mot doit être capitalisé (par défaut : true).
     *
     * @return string Le nom formaté.
     *
     * @throws \InvalidArgumentException Si le paramètre `$name` est vide.
     *
     * @example
     * formatEntityName('entity_name_with_underscore');
     * // Retourne : "Entity Name de With Underscore"
     *
     * @example
     * formatEntityName('product-category', 'dans');
     * // Retourne : "Product dans Category"
     *
     * @example
     * formatEntityName('user_profile', 'et', false);
     * // Retourne : "user et profile"
     *
     * @example
     * try {
     *     formatEntityName('');
     * } catch (\InvalidArgumentException $e) {
     *     echo $e->getMessage();
     *     // Retourne : "Le nom ne peut pas être vide."
     * }
     */
    function formatEntityName($name, $middleWord = 'de', $capitalize = true)
    {
        if (empty($name)) {
            // throw new \InvalidArgumentException('Le nom ne peut pas être vide.');
            return '';
        }

        // Remplacer underscores ou tirets par des espaces
        $formattedName = str_replace(['_', '-'], ' ', $name);

        // Capitalisation conditionnelle
        if ($capitalize) {
            $formattedName = ucwords($formattedName);
        }

        // Insertion du mot au milieu
        $words = explode(' ', $formattedName);
        $middleIndex = floor(count($words) / 2);
        array_splice($words, $middleIndex, 0, $middleWord);
        $formattedName = implode(' ', $words);

        return $formattedName ?? '';
    }
}

if (!function_exists('formatEntityNameWithArray')) {
    /**
     * Formate une chaîne ou un tableau représentant un nom d'entité.
     *
     * Cette fonction effectue les étapes suivantes :
     * 1. Remplace les underscores (_) et tirets (-) par des espaces.
     * 2. Capitalise chaque mot si demandé.
     * 3. Insère un mot spécifique au milieu du nom ou des noms dans le cas d'un tableau.
     *
     * @param mixed $name Le nom ou le tableau de noms à formater.
     * @param string $middleWord Le mot à insérer au milieu (par défaut : 'de').
     * @param bool $capitalize Indique si chaque mot doit être capitalisé (par défaut : true).
     *
     * @return mixed Le nom(s) formaté(s), sous forme de chaîne ou tableau.
     *
     * @throws \InvalidArgumentException Si le paramètre `$name` est vide ou invalide.
     *
     * @example
     * formatEntityName('entity_name_with_underscore');
     * Retourne : "Entity Name de With Underscore"
     *
     * @example
     * formatEntityName('product-category', 'dans');
     * Retourne : "Product dans Category"
     *
     * @example
     * formatEntityName(['user_profile', 'product-item'], 'et', false);
     * Retourne : ["user et profile", "product et item"]
     *
     * @example
     * try {
     *     formatEntityName('');
     * } catch (\InvalidArgumentException $e) {
     *     echo $e->getMessage();
     *     Retourne : "Le nom ne peut pas être vide."
     * }
     */
    function formatEntityNameWithArray($name, $middleWord = 'de', $capitalize = true)
    {
        if (empty($name)) {
            throw new \InvalidArgumentException('Le nom ne peut pas être vide.');
        }

        // Traiter les cas où $name est un tableau
        if (is_array($name)) {
            $formattedNames = [];
            foreach ($name as $n) {
                $formattedNames[] = formatEntityNameWithArray($n, $middleWord, $capitalize);
            }
            return $formattedNames;
        }

        // Si le nom ne contient ni _ ni -, appliquer uniquement ucwords
        if (!str_contains($name, '_') && !str_contains($name, '-') && !str_contains($name, $middleWord)) {
            if ($capitalize) {
                return ucwords($name);
            }
            return $name;
        }

        // Remplacer underscores ou tirets par des espaces
        $formattedName = str_replace(['_', '-'], ' ', $name);

        // Capitalisation conditionnelle
        if ($capitalize) {
            $formattedName = ucwords($formattedName);
        }

        // Insertion du mot au milieu
        $words = explode(' ', $formattedName);
        $middleIndex = floor(count($words) / 2);
        array_splice($words, $middleIndex, 0, $middleWord);
        $formattedName = implode(' ', $words);

        return $formattedName;
    }
}

if (!function_exists('sumRecursive')) {
    /**
     * Additionne les données de différentes sources (modèles, collections, tableaux) de manière récursive.
     *
     * @param mixed $data Les données à additionner (modèle, collection, tableau, etc.).
     * @return float La somme des valeurs numériques.
     */
    function sumRecursive($data)
    {
        if (is_numeric($data)) {
            return $data;
        }

        if (is_array($data) || $data instanceof \Traversable) {
            $sum = 0;
            foreach ($data as $item) {
                $sum += sumRecursive($item);
            }
            return $sum;
        }

        if (is_object($data) && method_exists($data, 'toArray')) {
            return sumRecursive($data->toArray());
        }

        return 0;
    }
}

if (!function_exists('calculateSumWithCondition')) {
    /**
     * Calcule la somme des valeurs de différentes sources (modèles, collections, tableaux) en fonction d'une condition,
     * avec prise en charge des clés spécifiques et un formatage en pourcentage.
     *
     * @param mixed $data Les données à analyser (modèle, collection, tableau, etc.).
     * @param string|null $key Clé à utiliser pour extraire les valeurs numériques (optionnelle).
     * @param callable|null $condition Fonction de condition à appliquer pour filtrer les données (optionnelle).
     * @param int $precision Nombre de décimales pour le formatage (par défaut : 2).
     * @param bool $formatted Indique si le résultat doit être formaté en pourcentage (par défaut : false).
     * @return string|float La somme calculée, formatée en pourcentage si demandé.
     */
    function calculateSumWithCondition(
        $data,
        ?string $key = null,
        ?callable $condition = null,
        int $precision = 2,
        bool $formatted = false
    ) {
        if (is_numeric($data)) {
            return $data;
        }

        if (is_array($data) || $data instanceof \Traversable) {
            $sum = 0;

            foreach ($data as $item) {
                $value = $key && is_array($item) ? ($item[$key] ?? null) : $item;
                if (is_object($item) && $key) {
                    $value = $item->{$key} ?? null;
                }

                if (is_numeric($value) && (!$condition || $condition($item))) {
                    $sum += $value;
                }
            }

            return $formatted
                ? number_format($sum, $precision) . '%'
                : round($sum, $precision);
        }

        if (is_object($data) && method_exists($data, 'toArray')) {
            return calculateSumWithCondition($data->toArray(), $key, $condition, $precision, $formatted);
        }

        return 0;
    }
}

if (!function_exists('averageRecursive')) {
    function averageRecursive($data, ?string $key = null, int $precision = 2, bool $formatted = false)
    {
        $sum = 0;
        $count = 0;

        $recursiveSum = function ($data) use (&$sum, &$count, $key, &$recursiveSum) {
            if (is_numeric($data)) {
                $sum += $data;
                $count++;
                return;
            }

            if (is_array($data) || $data instanceof \Traversable) {
                foreach ($data as $item) {
                    $value = $item;

                    // Si une clé est spécifiée, on cherche la valeur correspondante.
                    if ($key) {
                        if (is_array($item)) {
                            $value = $item[$key] ?? null;
                        } elseif (is_object($item)) {
                            $value = $item->{$key} ?? null;
                        }
                    }

                    $recursiveSum($value);
                }
            } elseif (is_object($data) && method_exists($data, 'toArray')) {
                $recursiveSum($data->toArray());
            }
        };

        $recursiveSum($data);

        $average = $count > 0 ? $sum / $count : 0;

        return $formatted
            ? number_format($average, $precision) . '%'
            : round($average, $precision);
    }
}

if (!function_exists('calculatePercentage')) {
    /**
     * Calcule un pourcentage basé sur les données fournies.
     *
     * @param mixed $data Données à utiliser pour le calcul.
     * @param ?string $totalKey Clé représentant la valeur totale (optionnelle).
     * @param ?string $valueKey Clé représentant la valeur à mesurer (optionnelle).
     * @param int $precision Définie le nombre de décimales à inclure (par défaut : 2).
     * @param bool $formatted Indique si le résultat doit être formaté avec le `%` (par défaut : true).
     * @param array $options Tableaux d'options supplémentaires.
     *
     * @return string Le pourcentage calculé formaté ou une valeur par défaut en cas d'erreur.
     */

    function calculatePercentage(
        $data,
        ?string $totalKey = null,
        ?string $valueKey = null,
        int $precision = 2,
        bool $formatted = true,
        array $options = []
    ): string {
        $options = array_merge([
            'cumulative' => false,
            'group_by' => null,
            'locale' => config('app.locale', 'en_US'),
            'inverse' => false,
            'include_units' => true,
            'debug' => false,
        ], $options);

        try {
            // Si $data est un pourcentage direct
            if (is_numeric($data) && $totalKey === null && $valueKey === null) {
                return $formatted ? number_format($data, $precision) . ' %' : (string)$data;
            }

            if (is_numeric($data) && $totalKey === null && $valueKey === null) {
                if ($options['debug']) {
                    return json_encode(['error' => 'Impossible de calculer sans clé totale ou valeur.']);
                }
                throw new InvalidArgumentException('Les données ne permettent pas un calcul de pourcentage.');
            }


            // Si $data est un tableau
            if (is_array($data)) {
                if (isset($data['total']) && isset($data['value'])) {
                    $total = $data['total'];
                    $value = $data['value'];
                } elseif ($options['cumulative'] && $totalKey && $valueKey) {
                    $total = array_sum(array_column($data, $totalKey));
                    $value = array_sum(array_column($data, $valueKey));
                } else {
                    throw new InvalidArgumentException('Format de données non pris en charge.');
                }
            } else {
                throw new InvalidArgumentException('Les données doivent être un nombre ou un tableau.');
            }

            // Validation des valeurs
            if (!isset($total) || !isset($value) || $total == 0) {
                throw new InvalidArgumentException('Les clés total ou value sont invalides ou le total est zéro.');
            }

            // Calcul du pourcentage
            $percentage = ($value / $total) * 100;

            // Inverser si nécessaire
            if ($options['inverse']) {
                $percentage = 100 - $percentage;
            }

            // Formattage
            if ($formatted) {
                return number_format($percentage, $precision) . ' %';
            }

            return (string)$percentage;
        } catch (\Exception $e) {
            if ($options['debug']) {
                return json_encode(['error' => $e->getMessage()]);
            }
            return 'Erreur';
        }
    }
}

if (!function_exists('calculatePercentageWithCondition')) {
    /**
     * Calcule un pourcentage basé sur les données fournies avec des options de filtrage spécifiques.
     *
     * @param mixed $data Données à utiliser pour le calcul.
     * @param ?string $totalKey Clé représentant la valeur totale (optionnelle).
     * @param ?string $valueKey Clé représentant la valeur à mesurer (optionnelle).
     * @param int $precision Définie le nombre de décimales à inclure (par défaut : 2).
     * @param bool $formatted Indique si le résultat doit être formaté avec le `%` (par défaut : true).
     * @param array $options Tableaux d'options supplémentaires.
     *
     * @return string Le pourcentage calculé formaté ou une valeur par défaut en cas d'erreur.
     *
     * Exemple d'utilisation :
     * $filteredData = [
     * ['total' => 100, 'value' => 75, 'statut' => 'plagiat'],
     * ['total' => 50, 'value' => 25, 'statut' => 'non_plagiat'],];
     * $percentage = calculatePercentage($filteredData, 'total', 'value', 2, true,
     * ['filter_key' => 'statut', 'filter_value' => 'plagiat']);
     * echo $percentage; // "75.00 %"
     */

    function calculatePercentageWithCondition(
        $data,
        ?string $totalKey = null,
        ?string $valueKey = null,
        int $precision = 2,
        bool $formatted = true,
        array $options = []
    ): string {
        $options = array_merge([
            'cumulative' => false,
            'group_by' => null,
            'locale' => config('app.locale', 'en_US'),
            'inverse' => false,
            'include_units' => true,
            'debug' => false,
            'filter_key' => null,
            'filter_value' => null,
        ], $options);

        try {
            // Si $data est un pourcentage direct
            if (is_numeric($data) && $totalKey === null && $valueKey === null) {
                return $formatted ? number_format($data, $precision) . ' %' : (string)$data;
            }

            // Filtrage basé sur une condition spécifique
            if (isset($options['filter_key']) && isset($options['filter_value']) && is_array($data)) {
                $data = array_filter($data, function ($item) use ($options) {
                    return $item[$options['filter_key']] === $options['filter_value'];
                });
            }

            // Si $data est un tableau
            if (is_array($data)) {
                if (isset($data['total']) && isset($data['value'])) {
                    $total = $data['total'];
                    $value = $data['value'];
                } elseif ($options['cumulative'] && $totalKey && $valueKey) {
                    $total = array_sum(array_column($data, $totalKey));
                    $value = array_sum(array_column($data, $valueKey));
                } else {
                    throw new InvalidArgumentException('Format de données non pris en charge.');
                }
            } else {
                throw new InvalidArgumentException('Les données doivent être un nombre ou un tableau.');
            }

            // Validation des valeurs
            if (!isset($total) || !isset($value) || $total == 0) {
                throw new InvalidArgumentException('Les clés total ou value sont invalides ou le total est zéro.');
            }

            // Calcul du pourcentage
            $percentage = ($value / $total) * 100;

            // Inverser si nécessaire
            if ($options['inverse']) {
                $percentage = 100 - $percentage;
            }

            // Formattage
            if ($formatted) {
                return number_format($percentage, $precision) . ' %';
            }

            return (string)$percentage;
        } catch (\Exception $e) {
            if ($options['debug']) {
                return json_encode(['error' => $e->getMessage()]);
            }
            return 'Erreur';
        }
    }
}

if (!function_exists('abbreviateNumberFormat')) {
    /**
     * Format a number to an abbreviated format or scientific notation.
     * Gestion des localisations : Ajout de la prise en charge des formats régionaux avec des séparateurs décimaux et de milliers basés sur les paramètres locaux.
     * Gestion des unités personnalisées : Permet d'ajouter ou de modifier les suffixes (par exemple : K pour kilo, M pour méga) via des paramètres.
     * Affichage en notation scientifique (optionnel) : Permet de choisir entre un format abrégé ou une notation scientifique.
     * Support des très grands ou très petits nombres : Gestion des nombres extrêmement grands (> trillions) et des fractions (valeurs < 1).
     * Suffixes adaptables dynamiquement : Ajout automatique de nouveaux suffixes si le nombre dépasse les limites actuelles.
     *
     * @param float|int $value The number to abbreviate.
     * @param int $precision The number of decimal places to include.
     * @param bool $useScientificNotation Use scientific notation for very large/small numbers.
     * @param array $customSuffixes Custom suffixes for formatting.
     * @param string|null $locale Optional locale for number formatting.
     * @param array $options Custom suffixes for formatting.
     * @return string The formatted number.
     */
    function abbreviateNumberFormat(
        $value,
        int $precision = 1,
        bool $useScientificNotation = false,
        array $customSuffixes = [],
        ?string $locale = null,
        array $options = []
    ): string {
        // Validation
        if (!is_numeric($value)) {
            return $options['invalid_value'] ?? 'N/A';
        }

        $sign = $value < 0 ? '-' : '';
        $absValue = abs($value);

        // Gestion des options
        $suffixes = $customSuffixes + [
            1_000_000_000_000 => 'T',
            1_000_000_000     => 'B',
            1_000_000         => 'M',
            1_000             => 'k',
        ];

        $roundingMode = $options['rounding_mode'] ?? PHP_ROUND_HALF_UP;

        if ($useScientificNotation && ($absValue >= 1e15 || ($absValue < 1e-3 && $absValue > 0))) {
            return sprintf('%.' . $precision . 'e', $value);
        }

        foreach ($suffixes as $threshold => $suffix) {
            if ($absValue >= $threshold) {
                $formatted = round($absValue / $threshold, $precision, $roundingMode);
                return $sign . number_format($formatted, $precision, '.', '') . $suffix;
            }
        }

        return $sign . number_format($value, $precision);
    }
}

if (!function_exists('translate')) {
    function translate($key, $replace = [], $locale = null)
    {
        return __($key, $replace, $locale ?: app()->getLocale());
    }
}

if (!function_exists('paginate')) {
    function paginate($query, $perPage = 10)
    {
        return $query->paginate($perPage)->withQueryString();
    }
}

if (!function_exists('generateDocument')) {

    function generateDocument(
        string $viewOrData,
        array $data = [],
        string $filename = 'document',
        string $format = 'pdf', // pdf, excel, word, html
        string $outputMode = 'download', // download, inline, save
        string $savePath = null, // Chemin pour enregistrer
        array $options = [] // Options avancées
    ) {
        // try {
        //     // Configuration par défaut
        //     $defaultOptions = [
        //         'paper' => 'A4',
        //         'orientation' => 'portrait',
        //         'margins' => [10, 10, 10, 10], // top, right, bottom, left
        //         'library' => 'dompdf', // Peut être 'dompdf', 'snappy', etc.
        //         'locale' => app()->getLocale(), // Support multi-langues
        //         'headers' => false, // Ajouter un en-tête personnalisé
        //         'footer' => false, // Ajouter un pied de page personnalisé
        //         'watermark' => null, // Ajouter un filigrane
        //     ];

        //     $options = array_merge($defaultOptions, $options);

        //     // Valider le format
        //     $supportedFormats = ['pdf', 'excel', 'word', 'html'];
        //     if (!in_array($format, $supportedFormats)) {
        //         throw new InvalidArgumentException("Format non supporté : $format. Formats supportés : " . implode(', ', $supportedFormats));
        //     }

        //     // Génération selon le format
        //     // switch ($format) {
        //     //     case 'pdf':
        //     //         // $pdf = new Pdf();

        //     //         // $pdf->loadView($viewOrData, $data)
        //     //         //     ->setPaper($options['paper'], $options['orientation']);

        //     //         if ($options['margins']) {
        //     //             $pdf->setMargins(...$options['margins']);
        //     //         }

        //     //         if ($options['headers']) {
        //     //             $pdf->setOption('header-html', $options['headers']);
        //     //         }

        //     //         if ($options['footer']) {
        //     //             $pdf->setOption('footer-html', $options['footer']);
        //     //         }

        //     //         if ($options['watermark']) {
        //     //             // Ajouter le filigrane
        //     //         }

        //     //         $content = $pdf->output();
        //     //         break;

        //     //     case 'html':
        //     //         $content = view($viewOrData, $data)->render();
        //     //         break;

        //     //     case 'excel':
        //     //         // Génération avec Laravel Excel (package Maatwebsite)
        //     //         // $content = new Excel();
        //     //         // $content->download(new GenericExport($data), "$filename.xlsx");
        //     //         break;

        //     //     case 'word':
        //     //         // Implémentation similaire pour Word
        //     //         break;

        //     //     default:
        //     //         throw new InvalidArgumentException("Format non pris en charge : $format");
        //     // }

        //     // Modes de sortie
        //     switch ($outputMode) {
        //         case 'inline':
        //             return new Response($content, 200, [
        //                 'Content-Type' => 'application/pdf',
        //                 'Content-Disposition' => "inline; filename=\"$filename.$format\"",
        //             ]);

        //         case 'save':
        //             if (!$savePath) {
        //                 throw new InvalidArgumentException("Le chemin d'enregistrement n'est pas spécifié pour le mode 'save'.");
        //             }
        //             file_put_contents($savePath . DIRECTORY_SEPARATOR . "$filename.$format", $content);
        //             return "Fichier enregistré avec succès à $savePath";

        //         case 'download':
        //         default:
        //             return response($content, 200)
        //                 ->header('Content-Type', 'application/pdf')
        //                 ->header('Content-Disposition', "attachment; filename=\"$filename.$format\"");
        //     }
        // } catch (\Exception $e) {
        //     // Journalisation des erreurs
        //     Log::error("Erreur lors de la génération du document : " . $e->getMessage());
        //     return response()->json(['error' => 'Impossible de générer le document.'], 500);
        // }
    }
}

if (!function_exists('weightedAverage')) {
    function weightedAverage($data, $weightKey, $valueKey)
    {
        $totalWeight = array_sum(array_column($data, $weightKey));
        if ($totalWeight == 0) return 0;

        return array_sum(array_map(function ($item) use ($weightKey, $valueKey) {
            return $item[$valueKey] * ($item[$weightKey] / 100);
        }, $data));
    }
}

if (!function_exists('encryptData')) {
    function encryptData($data)
    {
        return encrypt($data);
    }
}

if (!function_exists('decryptData')) {
    function decryptData($encryptedData)
    {
        return decrypt($encryptedData);
    }
}

if (!function_exists('logActivity')) {
    function logActivity($message, $userId = null)
    {
        Log::info("User $userId: $message");
    }
}

if (!function_exists('hasPermission')) {
    function hasPermission($user, $permission)
    {
        return $user->hasPermissionTo($permission);
    }
}

if (!function_exists('cleanString')) {
    function cleanString($string)
    {
        return trim(preg_replace('/[^A-Za-z0-9]/', ' ', $string));
    }
}

if (!function_exists('formatForChart')) {
    function formatForChart($data, $labelKey, $valueKey)
    {
        return [
            'labels' => $data->pluck($labelKey)->toArray(),
            'values' => $data->pluck($valueKey)->toArray(),
        ];
    }
}

if (!function_exists('getSessionData')) {
    function getSessionData($key, $default = null)
    {
        return session($key, $default);
    }
}

if (!function_exists('setCookie')) {
    function setCookie($key, $value, $minutes = 60)
    {
        Cookie::queue($key, $value, $minutes);
    }
}

if (!function_exists('isAdmin')) {
    function isAdmin($user)
    {
        return $user->role === 'admin';
    }
}

if (!function_exists('isSuperAdmin')) {
    function isSuperAdmin($user)
    {
        return $user->role === 'Super Admin';
    }
}

if (!function_exists('isStudent')) {
    function isStudent($user)
    {
        return $user->role === 'etudiant';
    }
}

if (!function_exists('isTeacher')) {
    function isTeacher($user)
    {
        return $user->role === 'enseignant';
    }
}

if (!function_exists('addMissingTranslation')) {
    function addMissingTranslation($key, $defaultValue = '')
    {
        if (!Lang::has($key)) {
            // Stocker dans un fichier dédié pour suivi
            File::append(storage_path('logs/missing_translations.log'), "$key=$defaultValue\n");
        }
    }
}

if (!function_exists('emitLivewireEvent')) {
    function emitLivewireEvent($event, $payload = [])
    {
        return Livewire::emit($event, $payload);
    }
}

if (!function_exists('getStatusColor')) {
    function getStatusColor($status)
    {
        return match ($status) {
            'plagiat' => 'text-red-500',
            'non_plagiat' => 'text-green-500',
            default => 'text-gray-500',
        };
    }
}

if (!function_exists('cacheWithExpiry')) {
    function cacheWithExpiry($key, $callback, $expiry = 3600)
    {
        return Cache::remember($key, $expiry, $callback);
    }
}

if (!function_exists('processInBatches')) {
    function processInBatches($model, $callback, $batchSize = 100)
    {
        $model::chunk($batchSize, function ($items) use ($callback) {
            foreach ($items as $item) {
                $callback($item);
            }
        });
    }
}

if (!function_exists('sendNotification')) {
    function sendNotification($user, $message, $type = 'info')
    {
        Notification::make()
            ->title('Nouvelle notification')
            ->body($message)
            ->icon($type === 'success' ? 'heroicon-o-check-circle' : 'heroicon-o-exclamation-circle')
            ->send();
    }
}

if (!function_exists('calculatePlagiarismRate')) {
    function calculatePlagiarismRate($totalFiles, $plagiarizedFiles)
    {
        return $totalFiles > 0 ? round(($plagiarizedFiles / $totalFiles) * 100, 2) : 0;
    }
}

if (!function_exists('generateUniqueFilePath')) {

    /**
     * Générer un chemin d'accès unique pour chaque fichier.
     * Valider les types de fichiers et leurs tailles.
     * Compresser les fichiers images (ex. : pages de garde).
     * Obtenir l'URL complète du fichier depuis le disque.
     */
    function generateUniqueFilePath($filename, $directory = 'uploads')
    {
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $uniqueName = uniqid() . '.' . $extension;
        return $directory . '/' . $uniqueName;
    }
}
