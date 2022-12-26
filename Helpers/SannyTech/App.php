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

    /**
     * Find all about an object
     * @param string $sql
     * @return array
     */
    public static function findAll(string $sql=""):array
    {
       return static::find("SELECT * FROM " . static::$dbTable . " " . $sql);
    }

    /**
     * Find an object by id
     * @param int $id
     * @return mixed
     */
    public static function findById(int $id)
    {
       global $db;
       $sql = "SELECT * FROM " . static::$dbTable . " WHERE id = :id LIMIT 1";
       $stmt = $db->prepare($sql);
       $stmt->bindParam(':id', $id, PDO::PARAM_INT);
       $stmt->execute();
       $result = $stmt->fetch();
       return !empty($result) ? $result : false;
    }

    /**
     * Execute a query and instantiate its objects
     * @param $ql
     * @return array
     */
    protected static function find($ql): array
    {
       global $db;
       $result = $db->query($ql);
       /*Instantiate on fetch with PDO*/
       return $result->fetchAll(PDO::FETCH_CLASS, get_called_class(), array(true));
    }

    /**
     * Execute a query and instantiate its objects
     * @param $sql
     * @return array
     */
    public static function findByQuery($sql): array
    {
       global $db;
       $result = $db->query($sql);
       $object = array();
       /*Instantiate on fetch with PDO*/
       while($row = $result->fetch(PDO::FETCH_ASSOC)){
          $object[] = static::instantiate($row);
       }
       return $object;
    }

    /**
     * Instantiate object
     * @param $record
     * @return object
     */
    protected static function instantiate($record):object{
       $calledClass = get_called_class();
       $object = new $calledClass;
       foreach($record as $attribute => $value) {
          if($object->hasAttribute($attribute)) {
             $object->$attribute = $value;
          }
       }
       return $object;
    }

    /**
     * Check If incoming $object has attribute in class
     * @param $attribute
     * @return bool
     */
    protected function hasAttribute($attribute):bool
    {
       return property_exists($this, $attribute);
    }


    /**
     * Grab properties of called
     */
    protected function grabProperties(): array
    {
       $properties = array();
       foreach(static::$dbFields as $dbField) {
          if(property_exists($this, $dbField)) {
             $properties[$dbField] = $this->$dbField;
          }
       }
       return $properties;
    }

    /**
     * Clean Properties
     */
    protected function cleanProperties(): array
    {
       global $db;
       $cleanedProperties = array();
       foreach($this->grabProperties() as $attribute => $value){
          $cleanedProperties[$attribute] = $db->escape($value);
       }
       return $cleanedProperties;
    }

    /**
     * Save Method
     */
    public function save(): bool
    {
       return isset($this->id) ? $this->update() : $this->create();
    }

    /**
     * Create Method
     */
    public function create(): bool
    {
       global $db;
       $attributes = $this->cleanProperties();
       $stmt = "INSERT INTO " . static::$dbTable . " (";
       $stmt .= join(",", array_keys($attributes));
       $stmt .= ") VALUES (";
       $stmt .= join(", ", array_values($attributes));
       $stmt .= ")";
       if($db->query($stmt)) {
          $this->id = $db->lastInsertId();
          return true;
       } else {
          return false;
       }
    }

    /**
     * Update Method
     */
    public function update($item="id"): bool
    {
       global $db;
       $attributes = $this->cleanProperties();
       $attributePairs = array();
       foreach($attributes as $attribute => $value) {
          $attributePairs[] = "{$attribute} = '{$value}'";
       }
       $stmt = "UPDATE " . static::$dbTable . " SET ";
       $stmt .= join(", ", $attributePairs);
       $stmt .= " WHERE $item = " . $db->escape($this->id);
       $db->query($stmt);
       return $db->rowCount() === 1;
    }

    /**
     * Delete Method
     */
    public function delete($item="id"): bool
    {
       global $db;
       $stmt = "DELETE FROM " . static::$dbTable . " WHERE $item = " . $db->escape($this->id);
       $db->query($stmt);
       return $db->rowCount() === 1;
    }

      



}