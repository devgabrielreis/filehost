<?php
    require_once(__DIR__ . "/User.php");

    class UserDAO implements UserDAOInterface
    {
        private PDO $conn;

        const USER_MAX_STORAGE_SPACE = 1000000000;

        public function __construct(PDO $conn)
        {
            $this->conn = $conn;
        }

        public function buildUser(array $data) : User
        {
            $user = new User();

            $user->setId(isset($data["id"]) ? $data["id"] : null);
            $user->setName(isset($data["name"]) ? $data["name"] : null);
            $user->setUsedStorage(isset($data["used_storage"]) ? $data["used_storage"] : null);
            $user->setEmail(isset($data["email"]) ? $data["email"] : null);
            $user->setPassword(isset($data["password_hash"]) ? $data["password_hash"] : null, true);

            return $user;
        }

        public function createUser(User $user) : int
        {
            $stmt = $this->conn->prepare("INSERT INTO users (
                name, used_storage, email, password_hash
            ) VALUES (
                :name, :used_storage, :email, :password_hash
            )");
        
            $stmt->bindParam(":name", $user->getName());
            $stmt->bindValue(":used_storage", 0);
            $stmt->bindParam(":email", $user->getEmail());
            $stmt->bindParam(":password_hash", $user->getPasswordHash());

            $stmt->execute();

            return $this->conn->lastInsertId();
        }

        public function destroyUser(int $userId) : void
        {
            $stmt = $this->conn->prepare("DELETE FROM users WHERE id = :id");
            $stmt->bindParam(":id", $userId);
            $stmt->execute();
        }

        public function changeName(int $userId, string $newName) : bool
        {
            if($this->getUserByName($newName))
            {
                return false;
            }

            $stmt = $this->conn->prepare("UPDATE users SET name = :name WHERE id = :id");
            $stmt->bindParam(":name", $newName);
            $stmt->bindParam(":id", $userId);
            $stmt->execute();

            return $stmt->rowCount() > 0;
        }

        public function changeEmail(int $userId, string $newEmail) : bool
        {
            if($this->getUserByEmail($newEmail))
            {
                return false;
            }

            $stmt = $this->conn->prepare("UPDATE users SET email = :email WHERE id = :id");
            $stmt->bindParam(":email", $newEmail);
            $stmt->bindParam(":id", $userId);
            $stmt->execute();

            return $stmt->rowCount() > 0;
        }

        public function changePassword(int $userId, string $newPasswordHash) : bool
        {
            $stmt = $this->conn->prepare("UPDATE users SET password_hash = :password_hash WHERE id = :id");
            $stmt->bindParam(":password_hash", $newPasswordHash);
            $stmt->bindParam(":id", $userId);
            $stmt->execute();

            return $stmt->rowCount() > 0;
        }

        public function getLoggedUser() : ?User
        {
            if(empty($_SESSION["token"]))
            {
                return null;
            }

            $loggedUser = $this->getUserByToken($_SESSION["token"]);

            return $loggedUser;
        }

        public function getUserByToken(string $token) : ?User
        {
            $stmt = $this->conn->prepare("SELECT * FROM tokens WHERE token = :token");
            $stmt->bindParam(":token", $token);
            $stmt->execute();

            if($stmt->rowCount() < 1)
            {
                return null;
            }

            $data = $stmt->fetch();

            if($data["revoked"] === 1)
            {
                return null;
            }

            $now = strtotime(date("Y-m-d H:i:s"));
            $expires = strtotime($data["expires"]);

            if($now > $expires)
            {
                return null;
            }
            
            return $this->getUserById($data["user_id"]);
        }

        public function getUserById(int $userId) : ?User
        {
            $stmt = $this->conn->prepare("SELECT * FROM users WHERE id = :id");
            $stmt->bindParam(":id", $userId);
            $stmt->execute();

            if($stmt->rowCount() === 0)
            {
                return null;
            }

            return $this->buildUser($stmt->fetch());
        }

        public function getUserByName(string $name) : ?User
        {
            $stmt = $this->conn->prepare("SELECT * FROM users WHERE name = :name");
            $stmt->bindParam(":name", $name);
            $stmt->execute();

            if($stmt->rowCount() === 0)
            {
                return null;
            }

            return $this->buildUser($stmt->fetch());
        }

        public function getUserByEmail(string $email) : ?User
        {
            $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = :email");
            $stmt->bindParam(":email", $email);
            $stmt->execute();

            if($stmt->rowCount() === 0)
            {
                return null;
            }

            return $this->buildUser($stmt->fetch());
        }

        public function userHasEnoughStorageSpace(int $userId, int $fileSize) : bool
        {
            $stmt = $this->conn->prepare("SELECT used_storage FROM users WHERE id = :id");
            $stmt->bindParam(":id", $userId);
            $stmt->execute();

            if($stmt->rowCount() === 0)
            {
                return false;
            }

            $data = $stmt->fetch();
            $usedSpace = $data["used_storage"];

            return $usedSpace + $fileSize <= $this::USER_MAX_STORAGE_SPACE;
        }

        public function updadeUserUsedStorage(int $userId, int $change) : void
        {
            $stmt = $this->conn->prepare("UPDATE users SET used_storage = used_storage + :change WHERE id = :id");
            $stmt->bindParam(":id", $userId);
            $stmt->bindParam(":change", $change);
            $stmt->execute();
        }

        public function createToken(int $userId, string $tokenExpirationDate) : ?string
        {
            $user = $this->getUserById($userId);

            if(!$user)
            {
                return null;
            }

            $token = $this->generateNewToken();

            $stmt = $this->conn->prepare("INSERT INTO tokens (
                token, expires, revoked, user_id
            ) VALUES (
                :token, :expires, :revoked, :user_id
            )");

            $stmt->bindParam(":token", $token);
            $stmt->bindParam(":expires", $tokenExpirationDate);
            $stmt->bindValue(":revoked", 0);
            $stmt->bindParam(":user_id", $user->getId());

            $stmt->execute();

            return $token;
        }

        public function revokeToken(string $token) : void
        {
            if(empty($token))
            {
                return;
            }

            $stmt = $this->conn->prepare("UPDATE tokens SET revoked = 1 WHERE token = :token");
            $stmt->bindParam(":token", $token);
            $stmt->execute();
        }

        public function generateNewToken() : string
        {
            do
            {
                $token = bin2hex(random_bytes(50));

                $stmt = $this->conn->prepare("SELECT COUNT(*) FROM tokens WHERE token = :token");
                $stmt->bindParam(":token", $token);
                $stmt->execute();
            }
            while ($stmt->fetchColumn() > 0);

            return $token;
        }
    }
?>
