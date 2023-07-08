<?php
    require_once(__DIR__ . "/File.php");
    require_once(__DIR__ . "/UserDAO.php");

    class FileDAO implements FileDAOInterface
    {
        private PDO $conn;
        private UserDAO $userDAO;

        public function __construct(PDO $conn, UserDAO $userDAO)
        {
            $this->conn = $conn;
            $this->userDAO = $userDAO;
        }

        public function buildFile(array $data) : File
        {
            return new File;
        }

        public function saveUploadedFIle(array $uploadedFile, string $visibility, int $ownerId) : File
        {
            $file = new File;
            
            $file->name = $uploadedFile["name"];
            $file->size = $uploadedFile["size"];
            $file->uploaded = date("Y-m-d H:i:s");
            $file->visibility = $visibility;
            $file->path = "/" . $this->generateFileName();
            $file->ownerId = $ownerId;
            $file->allowedUsersId = ($visibility === "restricted") ? [] : null;

            $stmt = $this->conn->prepare("INSERT INTO files (
                name, size, uploaded, visibility, path, owner_id
            ) VALUES (
                :name, :size, :uploaded, :visibility, :path, :owner_id
            )");

            $stmt->bindParam(":name", $file->name);
            $stmt->bindParam(":size", $file->size);
            $stmt->bindParam(":uploaded", $file->uploaded);
            $stmt->bindParam(":visibility", $file->visibility);
            $stmt->bindParam(":path", $file->path);
            $stmt->bindParam(":owner_id", $file->ownerId);

            $stmt->execute();

            $file->id = $this->conn->lastInsertId();
            
            // $this->userDAO->changeUpdadeUserUsedStorage($file->size);

            move_uploaded_file($uploadedFile["tmp_name"], FILES_ROOT . $file->path);

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