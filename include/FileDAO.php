<?php
    require_once(__DIR__ . "/File.php");
    require_once(__DIR__ . "/UserDAO.php");

    class FileDAO implements FileDAOInterface
    {
        private PDO $conn;
        private UserDAO $userDao;

        public function __construct(PDO $conn, UserDAO $userDao)
        {
            $this->conn = $conn;
            $this->userDao = $userDao;
        }

        public function buildFile(array $data) : File
        {
            return new File;
        }

        public function saveUploadedFIle(array $uploadedFile, string $visibility, int $ownerId) : File
        {
            $file = new File;
            
            $file->setName($uploadedFile["name"]);
            $file->setSize($uploadedFile["size"]);
            $file->setUploadTime(date("Y-m-d H:i:s"));
            $file->setVisibility($visibility);
            $file->setPath("/" . $this->generateFileName());
            $file->setOwnerId($ownerId);
            $file->setAllowedUsersIdsArray(($visibility === "restrict") ? [] : null);

            $stmt = $this->conn->prepare("INSERT INTO files (
                name, size, uploaded, visibility, path, owner_id
            ) VALUES (
                :name, :size, :uploaded, :visibility, :path, :owner_id
            )");

            $stmt->bindParam(":name", $file->getName());
            $stmt->bindParam(":size", $file->getSize());
            $stmt->bindParam(":uploaded", $file->getUploadTime());
            $stmt->bindParam(":visibility", $file->getVisibility());
            $stmt->bindParam(":path", $file->getPath());
            $stmt->bindParam(":owner_id", $file->getOwnerId());

            $stmt->execute();

            $file->setId($this->conn->lastInsertId());
            
            // $this->userDAO->updadeUserUsedStorage($file->size);

            move_uploaded_file($uploadedFile["tmp_name"], FILES_ROOT . $file->getPath());

            return $file;
        }

        private function generateFileName() : string
        {
            do
            {
                $filename = bin2hex(random_bytes(50));
            }
            while(file_exists(FILES_ROOT . "/" . $filename));

            return $filename;
        }
    }
?>