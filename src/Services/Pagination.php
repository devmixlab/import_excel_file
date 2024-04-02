<?php
namespace App\Services;

/**
 * Creates Pagination Links.
 */
class Pagination {

    protected int $total_pages;
    protected ?int $previous_page = null;
    protected ?int $next_page = null;

    protected ?array $previous_link = null;
    protected ?array $next_link = null;
    protected array $links = [];

    /**
     * Formats SQL
     *
     * @param int $page             Currrent page
     * @param int $total_items      Total items at all
     * @param int $per_page         How much items must be dispalyed on a page
     * @param int $before_after     How much sequent or preceding pagination links of current page
     *                              should be visible
     * @param int $first_last       How much in beginning and at the end must be visible
     *                              pagination links
     *
     * @returns  self
     */
    function __construct(
        protected int $page,
        protected int $total_items,
        protected int $per_page,
        protected int $before_after = 2,
        protected int $first_last = 2,
    ) {
        if($total_items == 0)
            return;

        $this->total_pages = ceil($total_items/$per_page);
        if($page > 1)
            $this->previous_page = $page - 1;
        if($page < $this->total_pages)
            $this->next_page = $page + 1;

        $this->make();
    }

    public function links() : array {
        if(empty($this->links))
            return [];

        return [$this->previous_link, ...$this->links, $this->next_link];
    }

    public function linksEmpty() : bool {
        return empty($this->links);
    }

    public function lastLink(bool $only_page = false) : array|string|int|null {
        if(empty($this->links))
            return null;

        $last_link = $this->links[count($this->links) - 1];
        return $only_page ? $last_link["page"] : $last_link;
    }

    public function isPreviousPage() : bool {
        return !empty($this->previous_page);
    }

    public function previousPage() : int {
        return $this->previous_page;
    }

    public function isNextPage() : bool {
        return !empty($this->next_page);
    }

    public function nextPage() : int {
        return $this->next_page;
    }

    public function totalPages() : int {
        return $this->total_pages;
    }

    public function page() : int {
        return $this->page;
    }

    /**
     * Creates list of pagination links
     *
     * @returns  void
     */
    protected function make () : void {
        $this->previous_link = $this->makePaginationItem($this->previous_page, "Previous");
//        $links = [
//            $this->makePaginationItem($this->previous_page, "Previous")
//        ];

        if($this->page <= ($this->before_after + $this->first_last + 1)){
            if($this->page > $this->first_last || ($this->before_after + $this->first_last + 1) < $this->total_pages){
                $stop_i = $this->page + $this->before_after;
            }else{
                $stop_i = $this->before_after + $this->first_last + 1;
            }

            if($stop_i > $this->total_pages)
                $stop_i = $this->total_pages;

            for($i = 1; $i <= $stop_i; $i++)
                $links[] = $this->makePaginationItem($i, $i, $this->page == $i);

            if($this->total_pages > $stop_i){
                if(($this->total_pages - $this->first_last) > $stop_i)
                    $links[] = $this->makePaginationItem(null, '...');

                for($i = ($this->total_pages - $this->first_last + 1); $i <= $this->total_pages; $i++)
                    $links[] = $this->makePaginationItem($i, $i);
            }

//            $this->next_link = $this->makePaginationItem($this->next_page, "Next");
//            $links[] = $this->makePaginationItem($this->next_page, "Next");
        }else if(
            $this->page > ($this->before_after + $this->first_last + 1) &&
            $this->page < $this->total_pages - ($this->before_after + $this->first_last + 1)
        ){
            for($i = 1; $i <= $this->first_last; $i++){
                $links[] = $this->makePaginationItem($i, $i);
            }

            $links[] = $this->makePaginationItem(null, '...');

            for($i = ($this->page - $this->before_after); $i <= ($this->page + $this->before_after); $i++){
                $links[] = $this->makePaginationItem($i, $i, $this->page == $i);
            }

            $links[] = $this->makePaginationItem(null, '...');

            for($i = ($this->total_pages - $this->first_last + 1); $i <= $this->total_pages; $i++){
                $links[] = $this->makePaginationItem($i, $i);
            }

//            $this->next_link = $this->makePaginationItem($this->next_page, "Next");
//            $links[] = $this->makePaginationItem($this->next_page, "Next");
//            $links[] = [
//                "url" => $next_page,
//                "label" => "Next",
//            ];
        }else{
            for($i = 1; $i <= $this->first_last; $i++){
                $links[] = $this->makePaginationItem($i, $i);
            }

            $links[] = $this->makePaginationItem(null, '...');

            $ii = $this->page < $this->first_last ?
                $this->total_pages - ($this->first_last + ($this->page - $this->before_after) + 1) :
                $this->page - $this->before_after;

            for($i = $ii; $i <= $this->total_pages; $i++){
                $links[] = $this->makePaginationItem($i, $i, $this->page == $i);
            }

//            $links[] = $this->makePaginationItem($this->next_page, "Next");
//            $links[] = [
//                "url" => $next_page,
//                "label" => "Next",
//            ];
        }

        $this->next_link = $this->makePaginationItem($this->next_page, "Next");

        $this->links = $links;
    }

    protected function makePaginationItem(
        int|null $page, int|string $label, bool $active = false, bool $disabled = false
    ) : array {
        return [
            "page" => $page,
            "label" => $label,
            "active" => $active,
            "disabled" => is_null($page) ? true : $disabled,
        ];
    }

}