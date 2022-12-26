<?php

   namespace SannyTech\libs;

   class Date extends \Carbon\Carbon
   {

      public function __construct($time = null, $tz = null)
      {
         parent::__construct($time, $tz);
      }

      public function __toString()
      {
         return $this->toFormattedDateString();
      }

      /**
       * Today's Date
       * @return string
       */
      public function dateToday(): string
      {
         return $this->now();
      }

      /**
       * Yesterday's Date
       * @return string
       */
      public function dateYesterday(): string
      {
         return $this->yesterday();
      }

      /**
       * Tomorrow's Date
       * @return string
       */
      public function dateTomorrow(): string
      {
         return $this->tomorrow();
      }

      /**
       * Get the difference in a human readable format in relation to now.
       *
       * @param  \Carbon\Carbon|\DateTimeInterface|null  $other
       * @param  bool  $absolute Removes time difference modifiers ago, after, etc
       * @param  bool  $short    Uses short format like 1y instead of 1 year
       * @return string
       */
      public function readHumans($other = null, $absolute = false, $short = false)
      {
         return parent::diffForHumans($other, $absolute, $short);
      }

      //$date->create(2020, 12, 12, 12, 12, 12)->diffForHumans()

      /**
       * Create Ago Time
       * @params $date
       * @param $date
       * @return string
       */
      public function agoTime(
         $date  = null
      ): string
      {
         return $this->parse($date)->diffForHumans();
      }



   }