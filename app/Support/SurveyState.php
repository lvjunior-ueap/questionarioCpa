<?php
namespace App\Support;

use Illuminate\Support\Collection;

class SurveyState
{
    public function __construct(
        public Collection $questions,
        public int $currentPage,
        public array $answers = [],
        public array $dimensionIntrosSeen = []
    ) {}

    public function currentQuestion()
    {
        return $this->questions->get($this->currentPage - 1);
    }

    public function totalPages(): int
    {
        return $this->questions->count();
    }

    public function isLastPage(): bool
    {
        return $this->currentPage >= $this->totalPages();
    }

    public function nextPage(): int
    {
        return min($this->currentPage + 1, $this->totalPages());
    }

    public function previousPage(): int
    {
        return max(1, $this->currentPage - 1);
    }

    public function progress(): int
    {
        if ($this->totalPages() === 0) return 0;

        return (int) round(
            count(array_filter($this->answers)) / $this->totalPages() * 100
        );
    }
}
