<?php

   namespace SannyTech\libs;

   use Exception;
   use SannyTech\Helper as help;
   use Tinify\Source as tinifySource;

   class PictureException extends \Exception {}

   class Picture extends \Tinify\Tinify
   {
      private mixed $source;
      private mixed $destination;
      private int $width;
      private int $height;
      private int $method;

      public function __construct() {
         parent::setKey(help::env('COMPRESSION_API_KEY'));
      }

      /**
       * Set Source for the Picture
       * @param mixed $file
       * @return void
       */
      public function setSource(mixed $file): void
      {
         $this->source = $file;
      }

      /**
       * Set Destination for the Picture
       * @param mixed $file
       * @return void
       */
      public function setDestination(mixed $file): void
      {
         $this->destination = $file;
      }

      /**
       * Compress from a file path.
       * @throws PictureException
       */
      public function compress()
      {
         try {
            $source = tinifySource::fromFile($this->source);
            $source->toFile($this->destination);
         } catch (\Tinify\AccountException $e) {
            throw new PictureException("Verify your API key or Contact Developer");
         } catch (\Tinify\ClientException $e) {
            throw new PictureException("Check your source image and request options.");
         } catch (\Tinify\ServerException $e) {
            throw new PictureException("Temporary issue with the Tinify API.");
         } catch (\Tinify\ConnectionException $e) {
            throw new PictureException("Check Internet Connectivity");
         } catch (Exception $e) {
            throw new PictureException("Something Went Wrong Contact Developer.");
         }
      }

      /**
       * Compress from a URL
       * @throws PictureException
       */
      public function compressUrl()
      {
         try {
            $source = tinifySource::fromUrl($this->source);
            $source->toFile($this->destination);
         } catch (\Tinify\AccountException $e) {
            throw new PictureException("Verify your API key or Contact Developer.");
         } catch (\Tinify\ClientException $e) {
            throw new PictureException("Check your source image and request options.");
         } catch (\Tinify\ServerException $e) {
            throw new PictureException("Temporary issue with the Tinify API.");
         } catch (\Tinify\ConnectionException $e) {
            throw new PictureException("Check Internet Connectivity.");
         } catch (Exception $e) {
            throw new PictureException("Something Went Wrong Contact Developer.");
         }
      }

      /**
       * Grab compression count from Tinify API
       * @return int
       * @throws PictureException
       */
      public function getCount(): int
      {
         if(is_null(parent::getCompressionCount())) {
            throw new PictureException("Verify your API key.");
         }
         return parent::getCompressionCount();
      }
      
      /**
       * Set Dimensions for the Picture
       * @param int $width
       * @param int $height
       * @param string $method
       * Options: fit, cover, scale, thumb
       * @return void
       */
      public function setDimensions(
         int $width = 150,
         int $height = 150,
         string $method = 'fit'
      ) : void
      {
         $this->width = $width;
         $this->height = $height;
         $this->method = $method;
      }

      /**
       * Resize the Picture
       * @return void
       * @throws PictureException
       */

      public function resizeImage(): void
      {
         try {
            $source = tinifySource::fromFile($this->source);
            $source->resize([
               'method' => $this->method,
               'width' => $this->width,
               'height' => $this->height
            ])->toFile($this->destination);
         } catch (\Tinify\AccountException $e) {
            throw new PictureException("Verify your API key or Contact Developer.");
         } catch (\Tinify\ClientException $e) {
            throw new PictureException("Check your source image and request options.");
         } catch (\Tinify\ServerException $e) {
            throw new PictureException("Temporary issue with the Tinify API.");
         } catch (\Tinify\ConnectionException $e) {
            throw new PictureException("Check Internet Connectivity.");
         } catch (Exception $e) {
            throw new PictureException("Something Went Wrong Contact Developer.");
         }
      }


   }