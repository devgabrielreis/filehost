<?php
    class File
    {
        private ?int $id;
        private ?string $name;
        private ?int $size;
        private ?string $uploaded;
        private ?string $visibility;
        private ?string $path;
        private ?int $ownerId;
        private ?array $allowedUsersIds;

        public function setId(?int $newId) : void
        {
            $this->id = $newId;
        }

        public function getId() : ?int
        {
            return $this->id;
        }

        public function setName(?string $newName) : void
        {
            $this->name = $newName;
        }

        public function getName() : ?string
        {
            return $this->name;
        }

        public function setSize(?int $newSize) : void
        {
            $this->size = $newSize;
        }

        public function getSize() : ?int
        {
            return $this->size;
        }

        public function setUploadTime(?string $newTime) : void
        {
            $this->uploaded = $newTime;
        }

        public function getUploadTime() : ?string
        {
            return $this->uploaded;
        }

        public function setVisibility(?string $newVisibility) : void
        {
            $this->visibility = $newVisibility;
        }

        public function getVisibility() : ?string
        {
            return $this->visibility;
        }

        public function setPath(?string $newPath) : void
        {
            $this->path = $newPath;
        }

        public function getPath() : ?string
        {
            return $this->path;
        }

        public function setOwnerId($newOwnerId) : void
        {
            $this->ownerId = $newOwnerId;
        }

        public function getOwnerId() : ?int
        {
            return $this->ownerId;
        }

        public function setAllowedUsersIdsArray(?array $allowedUsersArray) : void
        {
            $this->allowedUsersIds = $allowedUsersArray;
        }

        public function addAllowedUser(int $userId) : void
        {
            if($this->allowedUsersIds === null)
            {
                $this->allowedUsersIds = [];
            }

            $this->allowedUsersIds[] = $userId;
        }

        public function getAllowedUsersIds() : ?array
        {
            return $this->allowedUsersIds;
        }
    }

    interface FileDAOInterface
    {
        public function buildFile(array $data) : File;
        public function getFileAllowedUsers($fileId) : array;
        public function saveUploadedFIle(array $uploadedFile, string $visibility, int $ownerId) : File;
        public function getUserFiles(int $userId) : array;
    }
?>