<?php

/**
 * Photo Status.
 */

namespace App\Entity\Enum;

enum PhotoStatus: string
{
    case PUBLIC = 'PUBLIC';
    case PRIVATE = 'PRIVATE';
    /**
     * Get the role label.
     *
     * @return string Role label
     */
    public function label(): string
    {
        return match ($this) {
            self::PUBLIC => 'PUBLIC',
            self::PRIVATE => 'PRIVATE',
        };
    }
}
