<?php

   namespace SannyTech;

   class Pagination extends App
   {
      public int $currentPage;
      public int $itemsPerPage;
      public int $totalItems;

      public function __construct(
         $currentPage = 1,
         $itemsPerPage = 6,
         $totalItems = 0
      ) {
         $this->currentPage  = (int)$currentPage;
         $this->itemsPerPage = (int)$itemsPerPage;
         $this->totalItems   = (int)$totalItems;
      }

      /*Paginate Query Methods*/
      public function paginateQuery($sql): array
      {
         return $this->paginateFetch($sql);
      }

      private function paginateFetch($sql): array
      {
         global $db;
         $object = array();
         $result = $db->query($sql . " LIMIT " . $this->itemsPerPage . " OFFSET " . $this->offset());
         if (!empty($result)) {
            while($row = $result->fetch()) {
               $object[] = $row;
            }
         }
         return $object;
      }

      /*Total Pages Method*/
      private function totalPages(): float
      {
         return ceil($this->totalItems / $this->itemsPerPage);
      }

      /*Next Method*/
      private function next(): int
      {
         return $this->currentPage + 1;
      }

      /*Check if it has Next Method*/
      private function hasNext(): bool
      {
         return ($this->next() <= $this->totalPages());
      }

      /*Previous Method*/
      private function previous(): int
      {
         return $this->currentPage - 1;
      }

      /*Check if it has Previous Method*/
      private function hasPrevious(): bool
      {
         return ($this->previous() >= 1);
      }

      /*Offset Method*/
      public function offset(): float|int
      {
         return ($this->currentPage - 1) * $this->itemsPerPage;
      }

      /*Pagination Links Method*/

      /*previous link*/
      private function previousLink($url): string
      {
         if($this->hasPrevious()) {
            $link = "<li class='page-item'><a class='page-link' href='{$url}?page=" . $this->previous() . "'>«</a></li>";
         } else {
            $link = "<li class='page-item disabled'><a class='page-link' href='#'>«</a></li>";
         }
         return $link;
      }

      /*page links*/
      private function pageLinks($url): string
      {
         $link = "";
         for($i = 1; $i <= $this->totalPages(); $i++) {
            if($i == $this->currentPage){
               $link .= "<li class='page-item active'><a class='page-link' href='{$url}'>{$i}</a></li>";
            } else {
               if($i == $this->currentPage) {
                  $link .= "<li class='page-item active'><a class='page-link' href='{$url}?page={$i}'>{$i}</a></li>";
               } else {
                  $link .= "<li class='page-item'><a class='page-link' href='{$url}?page={$i}'>{$i}</a></li>";
               }
            }
         }
         return $link;
      }

      /*next link*/
      private function nextLink($url): string
      {
         if($this->hasNext()) {
            $link = "<li class='page-item'><a class='page-link' href='{$url}?page=" . $this->next() . "'>»</a></li>";
         } else {
            $link = "<li class='page-item disabled'><a class='page-link' href='#'>»</a></li>";
         }
         return $link;
      }

      /*pagination*/
      public function paginateLinks($url): string
      {
         $link  = $this->previousLink($url);
         $link .= $this->pageLinks($url);
         $link .= $this->nextLink($url);
         return $link;
      }
   }