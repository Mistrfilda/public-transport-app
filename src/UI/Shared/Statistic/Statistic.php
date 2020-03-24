<?php

declare(strict_types=1);

namespace App\UI\Shared\Statistic;

class Statistic
{
    public const CONTEXTUAL_SUCCESS = 'success';

    public const CONTEXTUAL_PRIMARY = 'primary';

    public const CONTEXTUAL_DANGER = 'danger';

    public const CONTEXTUAL_WARNING = 'warning';

    public const CONTEXTUAL_INFO = 'info';

    public const CONTEXTUAL_SECONDARY = 'secondary';

    /** @var string */
    private $contextualClass;

    /** @var string */
    private $heading;

    /** @var string */
    private $value;

    /** @var string */
    private $icon;

    /** @var string */
    private $border;

    /** @var string */
    private $size;

    /**
     * Statistic constructor.
     */
    public function __construct(
        string $contextualClass,
        string $heading,
        string $value,
        string $icon,
        string $border = 'border-left-',
        string $size = 'col-xl-6 col-md-6'
    ) {
        $this->contextualClass = $contextualClass;
        $this->heading = $heading;
        $this->value = $value;
        $this->icon = $icon;
        $this->border = $border;
        $this->size = $size;
    }

    public function getContextualClass(): string
    {
        return $this->contextualClass;
    }

    public function getHeading(): string
    {
        return $this->heading;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getIcon(): string
    {
        return $this->icon;
    }

    public function getBorder(): string
    {
        return $this->border . $this->getContextualClass();
    }

    public function getSize(): string
    {
        return $this->size;
    }
}
