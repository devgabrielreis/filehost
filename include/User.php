<?php
    class User
    {
        public ?int $id;
        public ?string $name;
        public ?int $usedStorage;
        public ?string $email;
        public ?string $passwordHash;
    }

    interface UserDAOInterface
    {
        public function buildUser(array $data) : User;
        public function createUser(User $user) : int;
        public function destroyUser(int $userId) : void;
        public function changeName(int $userId, string $newName) : bool;
        public function changeEmail(int $userId, string $newEmail) : bool;
        public function changePassword(int $userId, string $newPasswordHash) : bool;
        public function getUserByToken(string $token) : ?User;
        public function getUserById(int $userId) : ?User;
        public function getUserByName(string $name) : ?User;
        public function getUserByEmail(string $email) : ?User;
        public function createToken(int $userId, string $tokenExpirationDate) : ?string;
        public function revokeToken(string $token) : void;
    }
?>
