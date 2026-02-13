<?php

namespace App\Support;

class DimensionTheme
{
    protected array $themes = [
        1 => ['#3b82f6', '🧭'],
        2 => ['#8b5cf6', '📚'],
        3 => ['#14b8a6', '👩‍🏫'],
        4 => ['#f97316', '🤝'],
        5 => ['#0ea5e9', '💡'],
        6 => ['#6366f1', '🏛️'],
        7 => ['#10b981', '🏗️'],
        8 => ['#f59e0b', '📈'],
        9 => ['#ec4899', '🎓'],
        10 => ['#6b7280', '💰'],
    ];

    public function resolve(?int $order): array
    {
        [$color, $emoji] = $this->themes[$order] ?? ['#2563eb', '📝'];

        return $this->buildTheme($color, $emoji);
    }

    private function buildTheme(string $primaryColor, string $emoji): array
    {
        $svg = "
            <svg xmlns='http://www.w3.org/2000/svg' width='120' height='120'>
                <text 
                    x='50%' 
                    y='50%' 
                    dominant-baseline='middle' 
                    text-anchor='middle' 
                    font-size='32'
                    opacity='0.3'
                >
                    {$emoji}
                </text>
            </svg>
        ";

        $encodedSvg = rawurlencode($svg);

        return [
            'primary' => $primaryColor,
            'soft' => $primaryColor . '1A', // 10% alpha
            'pattern' => "url(\"data:image/svg+xml,{$encodedSvg}\")",
        ];
    }

}
