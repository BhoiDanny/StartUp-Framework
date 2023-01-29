<?php /** @noinspection PhpIllegalPsrClassPathInspection */

   /**
    * @package Cypherios
    * @author Daniel Botchway
    * @version 1.0.0
    * @abstract This is the App class
    * @link https://github.com/BhoiDanny
    * @license MIT
    */

   namespace SannyTech;

   use Closure;
   use SannyTech\Exceptions\DatabaseException;
   use PDO;
   use PDOException;

   /**
    * @property $id
    */
   Abstract class App extends Helper
   {
      private mixed $file;
      protected mixed $filename;
      protected mixed $type;
      protected mixed $size;
      protected mixed $tmpPath;
      protected array $errors = array();
      protected mixed $error;
      public static string $placeholder = '/assets/images/hms2.png';
      public string $uploadLocation = '../storage/images/';
      public string $uploadLimit = '10000000';//10MB

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
         if (empty($this->file) || !is_array($this->file)) {
            $this->errors[] = "There was no file uploaded here";
            return false;
         } else if ($this->file['error'] != 0) {
            $this->errors[] = $this->uploadErrors[$this->file['error']];
            return false;
         } else {
            $this->filename = basename($this->file['name']);
            $this->type = $this->file['type'];
            $this->size = $this->file['size'];
            $this->tmpPath = $this->file['tmp_name'];
            $this->error = $this->file['error'];
            return true;
         }
      }

      /**
       * Get File Path
       * @return string
       */
      public function getFilePath(): string
      {
         return $this->tmpPath;
      }

      /**
       * Get File Name
       * @return string
       */
      public function getFileName(): string
      {
         return $this->filename;
      }

      /**
       * Get File Extension
       * @return string
       */
      public function getFileExt(): string
      {
         return pathinfo($this->filename, PATHINFO_EXTENSION);
      }

      /**
       * Get File Real Name
       * @return string
       */
      public function getFileRealName(): string
      {
         return pathinfo($this->filename, PATHINFO_FILENAME);
      }

      /**
       * Get File Size
       * @return int
       */
      public function getSize(): int
      {
         return $this->size;
      }

      /**
       * Get File Type
       */
      public function getType(): string
      {
         return $this->type;
      }

      /**
       * Get File Error
       * @return string
       */
      public function getFileError(): string
      {
         return $this->error;
      }

      /**
       * Get File Errors
       * @return array|string
       */
      public function getFileErrors(): array|string
      {
         return $this->errors;
      }

      /**
       * Sets the file for upload
       * @param string $file
       * @return boolean
       */
      public function setFile(mixed $file): bool
      {
         if (empty($file) || !is_array($file)) {
            $this->errors[] = "There was no file uploaded here";
            return false;
         } else {
            $this->file = $file;
            return $this->assignFile();
         }
      }

      /**
       * Set Id
       * @param $id
       * @return void
       */
      public function setId($id): void
      {
         $this->id = $id;
      }

      /**
       * Find all about an object
       * @param string $sql
       * @return array
       * @throws DatabaseException
       */
      public static function findAll(string $sql = ""): array
      {
         try {
            return static::find("SELECT * FROM " . static::$dbTable . $sql);
         } catch (DatabaseException $e) {
            throw new DatabaseException($e->getMessage());
         }
      }

      /**
       * Find all about an object in instance
       * @param string $sql
       * @return array
       * @throws DatabaseException
       */
      public static function all(string $sql= ""): mixed
      {
         $result = static::findByQuery("SELECT * FROM " . static::$dbTable . $sql);
         return !empty($result) ? array_shift($result) : false;
      }

      /**
       * Find by column
       * @param string $column
       * @param string $value
       * @param bool $array
       * @return array|object
       * @throws DatabaseException
       */
      public static function findByColumn(string $column, string $value, bool $array = true): array|object
      {
         try {
            return static::find("SELECT * FROM " . static::$dbTable . " WHERE " . $column . " = '" . $value . "'", $array);
         } catch (DatabaseException $e) {
            throw new DatabaseException($e->getMessage());
         }
      }

      /**
       * Find an object by id
       * @param int $id
       * @return mixed
       * @throws DatabaseException
       */
      public static function findById(int $id): mixed
      {
         global $db;
         try {
            $sql = "SELECT * FROM " . static::$dbTable . " WHERE id = :id LIMIT 1";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch();
            return !empty($result) ? $result : false;
         } catch (PDOException $e) {
            throw new DatabaseException($e->getMessage());
         }
      }

      /**
       * Find an object by id
       * @param int $id
       * @return mixed
       * @throws DatabaseException
       */
      public static function findOnId(int $id): mixed
      {
         $result = static::findByQuery("SELECT * FROM " . static::$dbTable . " WHERE `id` = '$id' LIMIT 1");
         return !empty($result) ? array_shift($result) : false;
      }

      /**
       * Execute a query and instantiate its objects
       * @param $ql
       * @return array
       * @throws DatabaseException
       */
      protected static function find($ql,$array=true): array
      {
         global $db;
         try {
            $result = $db->query($ql);
            /*Instantiate on fetch with PDO*/
            return $result->fetchAll(PDO::FETCH_CLASS, get_called_class(), array($array));
         } catch (PDOException $e) {
            throw new DatabaseException($e->getMessage());
         }
      }

      /**
       * Convert the objects into methods for easy use
       * @example $user->id() instead of $user->id
       */
      public function __call($name, $arguments)
      {
         $method = substr($name, 0, 3);
         $field = substr($name, 3);
         if ($method == 'get') {
            return $this->$field;
         } else {
            return false;
         }
      }
      public function objectToMethod(): void
      {
         $class = get_called_class();
         #only the properties of the child class not inherited from the parent
         $properties = array_diff_key(get_object_vars($this), get_class_vars($class));
         foreach ($properties as $key => $value) {
            $this->{$key} = $value;
            $this->{'get'.$key} = function () use (&$key) {
               return $this->{$key};
            };
            $this->{'set'.$key} = function ($value) use (&$key) {
               return $this->{$key} = $value;
            };
         }

      }

      /**
       * Return object to closures for easy use
       * @return void
       * @example $user->id() instead of $user->id
       */
      # TODO: Make this work
      public function objectToClosure(): void
      {
         $class = get_called_class();
         #only the properties of the child class not inherited from the parent
         $properties = array_diff_key(get_object_vars($this), get_class_vars($class));
         foreach ($properties as $key => $value) {
            $this->{$key} = $value;
            $this->{'get'.$key} = function () use (&$key) {
               return $this->{$key};
            };
            $this->{'set'.$key} = function ($value) use (&$key) {
               return $this->{$key} = $value;
            };
         }
      }

      /**
       * Execute a query and instantiate its objects
       * @param $sql
       * @return array
       * @throws DatabaseException
       */
      public static function findByQuery($sql): array
      {
         global $db;
         try {
            $result = $db->query($sql);
            $object = array();
            /*Instantiate on fetch with PDO*/
            while($row = $result->fetch(PDO::FETCH_ASSOC)){
               $object[] = static::instantiate($row);
            }
            return $object;
         } catch (PDOException $e) {
            throw new DatabaseException($e->getMessage());
         }
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
       * @throws DatabaseException
       */
      public function save(): bool
      {
         return isset($this->id) ? $this->update() : $this->create();
      }

      /**
       * Create Method
       * @throws DatabaseException
       */
      public function create(): bool
      {
         global $db;
         try {
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
         } catch (PDOException $e) {
            throw new DatabaseException($e->getMessage());
         }
      }

      /**
       * Update Method
       * @throws DatabaseException
       */
      public function update($item="id"): bool
      {
         global $db;
         try {
            $attributes = $this->cleanProperties();
            $attributePairs = array();
            foreach($attributes as $attribute => $value) {
               $attributePairs[] = "{$attribute} = {$value}";
            }
            $stmt = "UPDATE " . static::$dbTable . " SET ";
            $stmt .= join(", ", $attributePairs);
            $stmt .= " WHERE $item = " . $db->escape($this->id);
            $db->query($stmt);
            return $db->rowCount() === 1;
         } catch (PDOException $e) {
            throw new DatabaseException($e->getMessage());
         }
      }

      /**
       * Delete Method
       * @throws DatabaseException
       */
      public function delete($item="id"): bool
      {
         global $db;
         try {
            $stmt = "DELETE FROM " . static::$dbTable . " WHERE $item = " . $db->escape($this->id);
            $db->query($stmt);
            return $db->rowCount() === 1;
         } catch (PDOException $e) {
            throw new DatabaseException($e->getMessage());
         }
      }


      /**
       * Instantiate the object to closures
       * @param array|object $record
       * @return Closure|bool|null
       */
      public function convertToClosure(array|object $record): Closure|bool|null
      {
         $closure = function () use ($record) {
            foreach ($record as $key => $value) {
               $this->$key = $value;
            }
         };
         return $closure->bindTo($this, get_class());
      }

      /**
       * Image random name
       * @param string $prefix
       * @return string
       */
      public function imageRandomName(string $prefix = 'codester_'): string
      {
         /*$random = rand(1, 1000000);
         $date = date('Y-m-d');
         $time = date('H-i-s');
         $name = $prefix . $random . $date . $time;
         return $name;*/
         return uniqid($prefix);
      }

      /**
       * Generate Image Name
       * @param string $prefix
       * @return string
       */
      public function generateImageName(string $prefix = 'codester_'): string
      {
         $name = $this->imageRandomName($prefix);
         $ext = pathinfo($this->file['name'], PATHINFO_EXTENSION);
         return $name . '.' . $ext;
      }

      /**
       * Generate File Name
       * @param string $prefix
       * @return string
       */
      public function generateFileName(string $prefix = 'codester_'): string
      {
         return $this->imageRandomName($prefix) . '.' . $this->getFileExt();
      }

      /**
       * Get Picture Path
       * @return string
       */
      public function getPicturePath(): string
      {
         return $this->picturePath();
      }

      /**
       * Get Picture Path
       * @param string $picture
       * @return string
       */
      public static function picturePath(string $picture = ""): string
      {
         //return $this->uploadDirectory . DS . $this->picture;
         /*Picture Path Private method*/
         return empty($picture) ? static::$placeholder : static::$pictureBox . $picture;
      }


   }