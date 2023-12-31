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
            $file = new File();

            $file->setId($data["id"]);
            $file->setName($data["name"]);
            $file->setSize($data["size"]);
            $file->setUploadTime($data["uploaded"]);
            $file->setVisibility($data["visibility"]);
            $file->setPath($data["path"]);
            $file->setOwnerId($data["owner_id"]);
            $file->setAllowedUsersIdsArray(($file->getVisibility() === "restrict") ? $this->getFileAllowedUsers($file->getId()) : null);

            return $file;
        }

        public function getFileAllowedUsers($fileId) : array
        {
            $stmt = $this->conn->prepare("SELECT user_id FROM permissions WHERE file_id = :file_id");
            $stmt->bindParam(":file_id", $fileId);
            $stmt->execute();

            $data = $stmt->fetchAll();
            $allowedUsers = [];

            foreach($data as $user)
            {
                $allowedUsers[] = $user["user_id"];
            }

            return $allowedUsers;
        }

        public function saveUploadedFIle(array $uploadedFile, string $visibility, int $ownerId) : File
        {
            if(!$this->userDao->userHasEnoughStorageSpace($ownerId, $uploadedFile["size"]))
            {
                throw new Exception("User " . $ownerId . " does not have enough storege space for uploade file.");
            }

            $file = new File;
            
            $file->setName(basename($uploadedFile["name"]));
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
            
            $this->userDao->updadeUserUsedStorage($ownerId, $file->getSize());

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

        public function getFile(int $fileId) : ?File
        {
            $stmt = $this->conn->prepare("SELECT * FROM files WHERE id = :id");
            $stmt->bindParam(":id", $fileId);
            $stmt->execute();

            if($stmt->rowCount() === 0)
            {
                return null;
            }

            $data = $stmt->fetch();

            return $this->buildFile($data);
        }

        public function getUserFiles(int $userId) : array
        {
            $stmt = $this->conn->prepare("SELECT * FROM files WHERE owner_id = :owner_id");
            $stmt->bindParam(":owner_id", $userId);
            $stmt->execute();

            $data = $stmt->fetchAll();
            $files = [];

            foreach($data as $file)
            {
                $files[] = $this->buildFile($file);
            }

            return $files;
        }

        public function renameFile(int $fileId, string $newName) : void
        {
            $stmt = $this->conn->prepare("UPDATE files SET name = :name WHERE id = :id");
            $stmt->bindParam(":name", $newName);
            $stmt->bindParam(":id", $fileId);
            $stmt->execute();
        }

        public function deleteFile(int $fileId) : void
        {
            $stmt = $this->conn->prepare("SELECT path, size, owner_id FROM files WHERE id = :id");
            $stmt->bindParam(":id", $fileId);
            $stmt->execute();

            if($stmt->rowCount() === 0)
            {
                return;
            }

            $data = $stmt->fetch();
            $path = $data["path"];
            $size = $data["size"];
            $ownerId = $data["owner_id"];

            $stmt = $this->conn->prepare("DELETE FROM permissions WHERE file_id = :file_id");
            $stmt->bindParam(":file_id", $fileId);
            $stmt->execute();

            $stmt = $this->conn->prepare("DELETE FROM files WHERE id = :id");
            $stmt->bindParam(":id", $fileId);
            $stmt->execute();

            $this->userDao->updadeUserUsedStorage($ownerId, -$size);

            unlink(FILES_ROOT . "/" . $path);
        }
    }
?>