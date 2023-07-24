<?php

namespace App\DTO;

use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;

/**
 * Object paginator for table
 */
class Paginator
{

    /**
     * @param DoctrinePaginator $paginator
     * @param int $amount
     * @param int $page
     */
    public function __construct(private readonly  DoctrinePaginator $paginator, private readonly int $amount, private readonly int $page)
    {
    }

    /**
     * Get data to paginate
     * @return DoctrinePaginator|array
     */
    public function getData(): DoctrinePaginator|array
    {
        return $this->paginator;
    }

    /**
     * Max page amount inpagination
     * @return int
     */
    public function getMaxPage(): int
    {
        return ceil($this->paginator->count() / $this->amount);
    }

    /**
     * Start item of the page
     * @return int
     */
    public function from(): int
    {
        return ($this->page * $this->amount) - $this->amount + 1;
    }

    /**
     * End item of the page
     * @return int
     */
    public function to(): int
    {
        return (($this->page * $this->amount) < $this->paginator->count()) ? $this->page * $this->amount : $this->paginator->count();
    }

    /**
     * Total items
     * @return int
     */
    public function getTotal(): int
    {
        return $this->paginator->count();
    }

    /**
     * Amount for page
     * @return int
     */
    public function getAmount(): int
    {
        return $this->amount;
    }

    /**
     * Numer of the current page
     * @return int
     */
    public function currentPage(): int
    {
        return $this->page;
    }

}