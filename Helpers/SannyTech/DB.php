<?php /** @noinspection PhpMultipleClassesDeclarationsInOneFile */

   /** @noinspection .PhpUndefinedConstantInspection */

   namespace SannyTech;

   use SannyTech\Helper as help;
   use Exception;
   use PDO;
   use PDOException;

   class DatabaseException extends Exception {}
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
      protected mixed $error = null;
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
       * Import a SQL file
       * @param $file
       * The file to import
       * @return bool
       * @throws Exception
       */
      public function restore($file): bool
      {

         if(!file_exists($file)) {
            throw new Exception("File does not exist");
         } else {
            $sql = file_get_contents($file);
            $content = explode(';', $sql);
            foreach($content as $query) {
               $query = trim($query);
               if(!empty($query)) {
                  try {
                     # Disable foreign key checks
                     $this->db->prepare("SET FOREIGN_KEY_CHECKS = 0")->execute();
                     $this->db->prepare("SET UNIQUE_CHECKS = 0")->execute();
                     $this->db->prepare("SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO'")->execute();
                     $this->db->prepare($query)->execute();
                  } catch (PDOException $e) {
                     $this->error = $e->getMessage();
                     return false;
                  }
               }
            }
         }
         return true;
      }

      /**
       * Export the database to a SQL file
       * @param $file
       * The name of the file to export to
       * @param bool $dbTablePick
       * Pick specific tables to export
       * @param array $dbTable
       * The tables to export
       * @param bool $dbDrop
       * Whether to include the DROP DATABASE statement
       * @return bool
       */
      public function backup($file, bool $dbTablePick = false, array $dbTable = [], bool $dbDrop = false): bool
      {
         # Get all the tables
         $tables = $this->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
         if($dbTablePick) {
            $tables = array_intersect($tables, $dbTable);
         }

         # Prepare the SQL script
         $sql = '-- Database Backup --' . PHP_EOL . PHP_EOL;
         $sql .= '-- --------------------------------------------------------' . PHP_EOL . PHP_EOL;
         $sql .= '-- Host: ' . $this->host . PHP_EOL;
         $sql .= '-- Generation Time: ' . date('M j, Y \a\t g:i A') . PHP_EOL;
         $sql .= '-- Server version: ' . $this->db->getAttribute(PDO::ATTR_SERVER_VERSION) . PHP_EOL;
         $sql .= '-- PHP Version: ' . phpversion() . PHP_EOL . PHP_EOL;
         $sql .= '-- Database: `' . help::env('DB_NAME') . '`' . PHP_EOL . PHP_EOL;
         $sql .= '-- Project: ' . help::env('APP_NAME') . PHP_EOL;
         $sql .= '-- --------------------------------------------------------' . PHP_EOL . PHP_EOL;
         #$sql .= '/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;' . PHP_EOL;
         #$sql .= '/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;' . PHP_EOL;
         #$sql .= "/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;" . PHP_EOL . PHP_EOL;
         #$sql .= '-- --------------------------------------------------------' . PHP_EOL . PHP_EOL;

         # If we want to include the DROP DATABASE statement
         if($dbDrop) {
            $sql .= '--' . PHP_EOL;
            $sql .= '-- Drop the database' . PHP_EOL;
            $sql .= '--' . PHP_EOL . PHP_EOL;
            $sql .= 'DROP DATABASE IF EXISTS `' . help::env('DB_NAME') . '`;' . PHP_EOL . PHP_EOL;
            $sql .= 'CREATE DATABASE IF NOT EXISTS `' . help::env('DB_NAME') . '`;' . PHP_EOL . PHP_EOL;
            $sql .= 'USE `' . help::env('DB_NAME') . '`;' . PHP_EOL . PHP_EOL;
            $sql .= '-- --------------------------------------------------------' . PHP_EOL . PHP_EOL;
         }

         # Cycle through each table
         foreach($tables as $table) {
            $sql .= 'DROP TABLE IF EXISTS ' . $table . ';';

            # Select the tables based on the table names

            # Get the table structure
            $create = $this->query('SHOW CREATE TABLE ' . $table)->fetch(PDO::FETCH_ASSOC);
            # Add the table structure to the SQL script
            $sql .= "\n\n" . $create['Create Table'] . ";\n\n";

            # Get the table data
            $data = $this->query('SELECT * FROM ' . $table)->fetchAll(PDO::FETCH_ASSOC);
            # Cycle through each row
            foreach($data as $row) {
               # Prepare the SQL statement
               $sql .= 'INSERT INTO ' . $table . ' VALUES (';
               # Cycle through each field
               foreach($row as $value) {
                  # Add the field value to the SQL statement
                  $value = addslashes($value);
                  # Escape any apostrophes
                  $value = str_replace("\n", "\\n", $value);
                  if(!isset($value)) {
                     $sql .= "''";
                  } else {
                     $sql .= "'" . $value . "'";
                  }
                  $sql .= ',';
               }
               # Remove the last comma
               $sql = substr($sql, 0, -1);
               $sql .= ");\n";
            }
            # Add a new line
            $sql .= "\n\n";
            $sql .= '-- --------------------------------------------------------' . PHP_EOL;
            $sql .= '-- End of data for table `' . $table . '`' . PHP_EOL;
            $sql .= '-- --------------------------------------------------------' . PHP_EOL . PHP_EOL;

         }
         #$sql .= '/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;' . PHP_EOL;
         #$sql .= '/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;' . PHP_EOL;
         #$sql .= '/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;' . PHP_EOL;
         # Save the SQL script to a backup file
         try {
            if(!file_put_contents(help::env('DB_BACKUP_DIR').'/'.$file.'.sql', $sql)) {
               throw new Exception('Could not save the SQL file.');
            }
         } catch (Exception $e) {
            $this->error = $e->getMessage();
            return false;
         }
         return true;
      }

      /**
       * Backup the database to csv file type
       * @param $filename
       * The name of the file to export to when $combine is true
       * @param bool $combine
       * Whether to combine all tables into one file
       * @param bool $dbTablePick
       * Pick specific tables to export
       * @param array $dbTable
       * The tables to export
       * @return bool
       * @throws Exception
       */
      public function backupCsvType($filename,bool $combine = true, bool $dbTablePick = false, array $dbTable = []): bool
      {
         # get all the tables
         $tables = $this->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);

         # disable foreign key checks
         /*$this->query('SET FOREIGN_KEY_CHECKS = 0');
         $this->query('SET UNIQUE_CHECKS = 0');
         $this->query('SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO"');*/

         $count = count($tables);
         $i = 0;

         if($dbTablePick && !empty($dbTable)) {
            $tables = array_intersect($tables, $dbTable);
         }

         # Prepare the CSV script
         $csv = '';

         # If combine is true
         if($combine) {
            # Cycle through each table
            foreach($tables as $table) {
               # Get the table data
               $data = $this->query('SELECT * FROM ' . $table)->fetchAll(PDO::FETCH_ASSOC);
               $columns = $this->query('SHOW COLUMNS FROM ' . $table)->fetchAll(PDO::FETCH_ASSOC);

               # Add the table name to the CSV script
               $csv .= 'Table: ' . $table . PHP_EOL;

               # Add Column names to the CSV script
               /*$csv .= join(',', array_keys($data[0])) . PHP_EOL;*/
               $csv .= join(',', array_column($columns, 'Field')) . PHP_EOL;

               # Cycle through each row
               foreach($data as $row) {
                  # Cycle through each field
                  foreach($row as $value) {
                     # Add the field value to the CSV statement with column names
                     $value = addslashes($value);
                     # Escape any apostrophes
                     $value = str_replace("\n", "\\n", $value);
                     if(!isset($value)) {
                        $csv .= " ";
                     } else {
                        $csv .= " " . $value . " ";
                     }
                     $csv .= ',';

                  }
                  # Remove the last comma
                  $csv = substr($csv, 0, -1);
                  $csv .= "\n";
               }
               # Add a new line
               $csv .= "\n\n";
            }

            # Save the CSV script to a backup file
            try {
               if(!file_put_contents(help::env('DB_BACKUP_DIR').'/'.$filename . '.csv', $csv)) {
                  throw new Exception('Could not save the CSV file.');
               }
            } catch (Exception $e) {
               $this->error = $e->getMessage();
               return false;
            }
         } else {
            # export each table to a separate file with the table name

            while($i < $count) {
               $csv = '';
               $data = $this->query('SELECT * FROM ' . $tables[$i])->fetchAll(PDO::FETCH_ASSOC);
               $columns = $this->query('SHOW COLUMNS FROM ' . $tables[$i])->fetchAll(PDO::FETCH_ASSOC);

               # Add Column names to the CSV script
               /*$csv .= join(',', array_keys($data[0])) . PHP_EOL;*/
               $csv .= join(',', array_column($columns, 'Field')) . PHP_EOL;

               # Cycle through each row
               foreach($data as $row) {
                  # Cycle through each field
                  foreach($row as $value) {
                     # Add the field value to the CSV statement with column names
                     $value = addslashes($value);
                     # Escape any apostrophes
                     $value = str_replace("\n", "\\n", $value);
                     if(!isset($value)) {
                        $csv .= " ";
                     } else {
                        $csv .= " " . $value . " ";
                     }
                     $csv .= ',';

                  }
                  # Remove the last comma
                  $csv = substr($csv, 0, -1);
                  $csv .= "\n";
               }
               # Add a new line
               $csv .= "\n\n";

               # Save the CSV script to a backup file
               try {
                  if(!file_put_contents(help::env('DB_BACKUP_DIR').'/'.$tables[$i] . '.csv', $csv)) {
                     throw new Exception('Could not save the CSV file.');
                  }
               } catch (Exception $e) {
                  $this->error = $e->getMessage();
                  return false;
               }
               $i++;
            }

         }

         return true;
      }

      /**
       * Destructor
       */
      public function __destruct()
      {
         $this->close();
      }

   }