<?php
namespace App\Enums;
class MaterialType
{
    public const Cotton   = 'Cotton';
    public const Polyster = 'Polyster';
    public const Pika     = 'Pika';
    public const Jeans    = 'Jeans';
    public const Linen    = 'Linen';
    public const Wool     = 'Wool';
    public const Silk     = 'Silk';

    public static function values(): array
    {
        return [
            self::Cotton,
            self::Polyster,
            self::Pika,
            self::Jeans,
            self::Linen,
            self::Wool,
            self::Silk,
        ];
    }
}
