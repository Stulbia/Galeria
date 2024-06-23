//w app/src/entity/enum (jak nie ma folderu enum, to zrob)
<?php
/**
 * Task Status.
 */

namespace App\Entity\Enum;

/**
 * Enum TaskStatus.
 */
enum TaskStatus: string
{
    case RECENT = 'RECENT';
    case ARCHIVED= 'ARCHIVED';

    /**
     * Get the role label.
     *
     * @return string Role label
     */
    public function label(): string
    {
        return match ($this) {
            TaskStatus::RECENT => 'label.RECENT',
            TaskStatus::ARCHIVED=> 'label.ARCHIVED',
        };
    }
}

//do encji task (task.php) dokleić:
/**
 * Satus.
 *
 * @var array<int, string>
 */
#[ORM\Column(type: 'json')]
    private array $status = [];

#....

/**
 * Getter for status.
 *
 * @return array<int, string> Status
 *
 */
    public function getRoles(): array
{
    $status = $this->status;
    $status[] = TaskStatus::ACTIVE ->value;

    return array_unique($status);
}

    /**
     * Setter for status
     *
     * @param array<int, string> $status Status
     */
    public function setStatus(array $status): void
{
    $this->status = $status;
}


//do kontrolera TASK przy tworzeniu task

$task->setStatus([TaskStatus::RECENT->value]);


//przy edycji task tak samo, albo
$task->setStatus([TaskStatus::ARCHIVED->value]);
