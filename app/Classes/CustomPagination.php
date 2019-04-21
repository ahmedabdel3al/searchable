<?php
/**
 * Created by PhpStorm.
 * User: boly
 * Date: 21/04/2019
 * Time: 10:56 م
 */

namespace App\Classes;


use Illuminate\Pagination\LengthAwarePaginator;

class CustomPagination
{
    private $paginatingItemsArray, $perPage, $currentPage, $paginatedItems;

    public function __construct(Array $paginatingItemsArray, $perPage)
    {
        $this->paginatingItemsArray = $paginatingItemsArray;
        $this->perPage = $perPage;
        // Get current page form url e.x. &page=1
        $this->currentPage = LengthAwarePaginator::resolveCurrentPage();
        // Slice the collection to get the items to display in current page
        $currentPageItems = $this->getCurrentPageItems();
        // Create our paginate and pass it to the view
        $lengthAwarePaginator = new LengthAwarePaginator($currentPageItems, count($this->paginatingItemsArray), $perPage);
        // set url path for generated links
        $this->paginatedItems = $this->SetUrlPath($lengthAwarePaginator);

    }

    public function paginate()
    {
        return $this->paginatedItems;
    }

    public function getCurrentPageItems()
    {
        return array_slice($this->paginatingItemsArray, ($this->currentPage * $this->perPage) - $this->perPage, $this->perPage);
    }

    public function SetUrlPath(LengthAwarePaginator $lengthAwarePaginator)
    {
        return $lengthAwarePaginator->setPath(request()->url());

    }
}