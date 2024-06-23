<?php
/**
 * Photo Status.
 */

namespace App\Entity\Enum;

/**
 * Enum PhotoStatus.
 */
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
            PhotoStatus::PUBLIC => 'label.PUBLIC',
            PhotoStatus::PRIVATE => 'label.PRIVATE',
        };
    }
}
