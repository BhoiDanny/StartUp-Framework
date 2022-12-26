<?php /** @noinspection PhpUndefinedConstantInspection */

   namespace SannyTech;

   use SannyTech\Helper as help;
   use Exception;
   use PDO;
   use PDOException;

   class DB
   {

      private   $host        = DB_HOST;
      private   $port        = DB_PORT;
      private   $dbReadUser  = DB_READ_USER;
      private   $dbReadPass  = DB_READ_PASS;
      private   $dbWriteUser = DB_WRITE_USER;
      private   $dbWritePass = DB_WRITE_PASS;
      private   $dbName      = DB_NAME;
      private   $charset     = DB_CHARSET;
      protected mixed $db;
      protected mixed $error;
      protected string $dsn;
      protected mixed $stmt;

      private array $options = array(
         PDO::ATTR_PERSISTENT => true,
         PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
         PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
         PDO::ATTR_EMULATE_PREPARES => false
      );

      /**
       * DB constructor.
       * @param bool $connection
       * @throws Exception
       */
      public function __construct(bool $connection = false)
      {
         if(help::env('DB_MODEL') == 'sqlite') {
            if(!file_exists(help::env('SQLITE_DB_DIR'))) {
               if(help::createDir(help::env('SQLITE_DB_DIR'))) {
                  help::createFile(help::env('SQLITE_DB_DIR') . 'DB.php/' . help::env('DB_NAME'));
               }
            } else {
               if(!file_exists(help::env('SQLITE_DB_DIR') . 'DB.php/' . help::env('DB_NAME'))) {
                  if(help::createFile(help::env('SQLITE_DB_DIR') . 'DB.php/' . help::env('DB_NAME'))){
                     $this->dsn = "sqlite:" . help::env('SQLITE_DB_DIR') . '/' . help::env('DB_NAME');
                     $this->connect();
                  }
               } else {
                  $this->dsn = "sqlite:" . help::env('SQLITE_DB_DIR') . '/' . help::env('DB_NAME');
                  $this->connect();
               }
            }

         } else {
            $this->dsn = "mysql:host=$this->host;port=$this->port;dbname=$this->dbName;charset=$this->charset";
            if(!$connection) {
               $this->readConnect();
            } else {
               $this->writeConnect();
            }
            echo "MySQL Database Connected";
         }

      }

      private function connect(): void
      {
         try {
            $this->db = new PDO($this->dsn , null, null, $this->options);
         } catch(PDOException $e) {
            $this->error = $e->getMessage();
            die($this->error);
         }
      }

      private function readConnect(): void
      {
         try {
            $this->db = new PDO($this->dsn, $this->dbReadUser, $this->dbReadPass, $this->options);
         } catch (PDOException $e) {
            $this->error = $e->getMessage();
            die($this->error);
         }
      }

      private function writeConnect(): void
      {
         try {
            $this->db = new PDO($this->dsn, $this->dbWriteUser, $this->dbWritePass, $this->options);
         } catch (PDOException $e) {
            $this->error = $e->getMessage();
            die($this->error);
         }
      }

      /**
       * Query the database
       * @param $sql
       * @return mixed
       */
      public function query($sql): mixed
      {
         $this->stmt = $this->db->prepare($sql);
         $this->confirmQuery($this->stmt);
         $this->stmt->execute();
         return $this->stmt;
      }

      /**
       * Confirm if the query was successful
       * @param $stmt
       */
      private function confirmQuery($stmt): void
      {
         if(!$stmt) {
            $this->error = $this->db->errorInfo();
            die($this->error[2]);
         }
      }

      /**
       * Bind values
       * @param $param
       * @param $value
       * @param null $type
       */
      public function bind($param, $value, $type = null): void
      {
         if(is_null($type)) {
            $type = match (true) {
               is_int($value) => PDO::PARAM_INT,
               is_bool($value) => PDO::PARAM_BOOL,
               is_null($value) => PDO::PARAM_NULL,
               default => PDO::PARAM_STR,
            };
         }
         $this->stmt->bindValue($param, $value, $type);
      }

      /**
       * Prepare the query
       * @param $sql
       * @return mixed
       */
      public function prepare($sql): mixed
      {
         $this->stmt = $this->db->prepare($sql);
         $this->confirmQuery($this->stmt);
         return $this->stmt;
      }

      /**
       * Execute the prepared statement
       * @return mixed
       */
      public function execute(): mixed
      {
         return $this->stmt->execute();
      }

      /**
       * Get the result set as objects
       * @return mixed
       */
      public function resultSet(): mixed
      {
         $this->execute();
         return $this->stmt->fetchAll();
      }

      /**
       * Get the result set as an array of objects
       * @return mixed
       */
      public function resultSetArray(): mixed
      {
         $this->execute();
         return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
      }

      /**
       * Get a single record as an object
       * @return mixed
       */
      public function result(): mixed
      {
         $this->execute();
         return $this->stmt->fetch();
      }

      /**
       * Get a single record as an array
       * @return mixed
       */
      public function resultArray(): mixed
      {
         $this->execute();
         return $this->stmt->fetch(PDO::FETCH_ASSOC);
      }

      /**
       * Get the row count
       * @return mixed
       */
      public function rowCount(): mixed
      {
         return $this->stmt->rowCount();
      }

      /**
       * Get the last inserted ID
       * @return string
       */
      public function lastInsertId(): string
      {
         return $this->db->lastInsertId();
      }

      /**
       * Begin a transaction
       * @return bool
       */
      public function beginTransaction(): bool
      {
         return $this->db->beginTransaction();
      }

      /**
       * End a transaction
       * @return bool
       */
      public function commit(): bool
      {
         return $this->db->commit();
      }

      /**
       * Cancel a transaction
       * @return bool
       */
      public function rollBack(): bool
      {
         return $this->db->rollBack();
      }

      /**
       * Debug the query
       * @return mixed
       */
      public function debugParams(): mixed
      {
         return $this->stmt->debugDumpParams();
      }

      /**
       * Get the error
       * @return mixed
       */
      public function getError(): mixed
      {
         return $this->error;
      }

      /**
       * Get the database connection
       * @return mixed
       */
      public function getDb(): mixed
      {
         return $this->db;
      }

      /**
       * Get the statement
       * @return mixed
       */
      public function getStmt(): mixed
      {
         return $this->stmt;
      }

      /**
       * Escape the string
       * @param $string
       * @return string
       */
      public function escape($string): string
      {
         return $this->db->quote($string);
      }

      /**
       * Close the connection
       */
      public function close(): void
      {
         $this->db = null;
      }

      /**
       * Destructor
       */
      public function __destruct()
      {
         $this->close();
      }

   }