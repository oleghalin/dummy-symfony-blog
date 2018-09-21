<?php

namespace App\Traits;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\Request;

trait Paginatable
{
    public $perPage = 5;

    private $page = 1;

    public function paginate(Request $request, $perPage = null)
    {
        if ($perPage !== null) {
            $this->perPage = $perPage;
        }

        if ($page = $request->get('page')) {
            $this->page = $page;
        }

        $queryBuilder = $this->createQueryBuilder('c');
        $query = $queryBuilder
            ->setFirstResult($this->getOffset())
            ->setMaxResults($this->perPage);

        return new Paginator($query);
    }

    private function getOffset()
    {
        return ($this->page - 1) * $this->perPage;
    }
}