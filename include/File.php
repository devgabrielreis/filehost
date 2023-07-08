<?php
    class File
    {
        public int $id;
        public string $name;
        public int $size;
        public string $uploaded;
        public string $visibility;
        public string $path;
        public int $ownerId;
        public ?array $allowedUsersId;
    }

    interface FileDAOInterface
    {
        public function buildFile(array $data) : File;
        public function saveUploadedFIle(array $uploadedFile, string $visibility, int $ownerId) : File;
    }
?>