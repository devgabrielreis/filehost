<?php
    class User
    {
        private ?int $id;
        private ?string $name;
        private ?int $usedStorage;
        private ?string $email;
        private ?string $passwordHash;

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

        public function setUsedStorage(?int $newValue) : void
        {
            $this->usedStorage = $newValue;
        }

        public function getUsedStorage() : ?int
        {
            return $this->usedStorage;
        }

        public function setEmail(?string $newEmail) : void
        {
            $this->email = $newEmail;
        }

        public function getEmail() : ?string
        {
            return $this->email;
        }

        public function setPassword(?string $newPassword, bool $alreadyHashed = false) : void
        {
            if(!$alreadyHashed)
            {
                $newPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            }

            $this->passwordHash = $newPassword;
        }

        public function getPasswordHash() : ?string
        {
            return $this->passwordHash;
        }

        public function comparePassword($password) : bool
        {
            return password_verify($password, $this->passwordHash);
        }
    }

    interface UserDAOInterface
    {
        public function buildUser(array $data) : User;
        public function createUser(User $user) : int;
        public function destroyUser(int $userId) : void;
        public function changeName(int $userId, string $newName) : bool;
        public function changeEmail(int $userId, string $newEmail) : bool;
        public function changePassword(int $userId, string $newPasswordHash) : bool;
        public function getLoggedUser() : ?User;
        public function getUserByToken(string $token) : ?User;
        public function getUserById(int $userId) : ?User;
        public function getUserByName(string $name) : ?User;
        public function getUserByEmail(string $email) : ?User;
        public function createToken(int $userId, string $tokenExpirationDate) : ?string;
        public function revokeToken(string $token) : void;
    }
?>
