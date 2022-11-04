<?php
/**
 * @package Cypherios
 * @author Daniel Botchway
 * @version 1.0.0
 * @abstract This is the App class
 * @link https://github.com/BhoiDanny
 * @license MIT
 */

namespace SannyTech;

 use PDO;

 Abstract class App extends Helper
{
    private   mixed $file;
    protected mixed $filename;
    protected mixed $type;
    protected mixed $size;
    protected mixed $tmpPath;
    protected array $errors = array();
    protected mixed $error;

    protected array $uploadErrors = array(
        UPLOAD_ERR_OK => "No errors.",
        UPLOAD_ERR_INI_SIZE => "Larger than upload_max_filesize.",
        UPLOAD_ERR_FORM_SIZE => "Larger than form MAX_FILE_SIZE.",
        UPLOAD_ERR_PARTIAL => "Partial upload.",
        UPLOAD_ERR_NO_FILE => "No file uploaded.",
        UPLOAD_ERR_NO_TMP_DIR => "No temporary directory.",
        UPLOAD_ERR_CANT_WRITE => "Can't write to disk.",
        UPLOAD_ERR_EXTENSION => "File upload stopped by extension."
    );

    /**
     * Assigns the file for upload
     * @return boolean
     */
    private function assignFile(): bool
    {
        if(empty($this->file) || !is_array($this->file)){
            $this->errors[]  = "There was no file uploaded here";
            return false;
        } else if($this->file['error'] != 0) {
            $this->errors[] = $this->uploadErrors[$this->file['error']];
            return false;
        } else {
            $this->filename = basename($this->file['name']);
            $this->type     = $this->file['type'];
            $this->size     = $this->file['size'];
            $this->tmpPath  = $this->file['tmp_name'];
            $this->error   = $this->file['error'];
            return true;
        }
    }

      /**
      * Sets the file for upload
      * @param string $file
      * @return boolean
      */
   public function setFile(mixed $file): bool
   {
      if(empty($file) || !is_array($file)){
         $this->errors[] = "There was no file uploaded here";
         return false;
      } else {
         $this->file = $file;
         return $this->assignFile();
      }
   }

   protected function findByQuery($ql)
   {
      global $db;
      $result = $db->query($ql);
      $object = array();
      /*Instantiate on fetch with PDO*/
      return $result->fetchAll(PDO::FETCH_CLASS, get_called_class());

   }

      



}