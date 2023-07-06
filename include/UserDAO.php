<?php
    require_once(__DIR__ . "/User.php");

    class UserDAO implements UserDAOInterface
    {
        private PDO $conn;

        public function __construct(PDO $conn)
        {
            $this->conn = $conn;
        }

        public function buildUser(array $data) : User
        {
            $user = new User();

            $user->id = (isset($data["id"]) ? $data["id"] : null);
            $user->name = (isset($data["name"]) ? $data["name"] : null);
            $user->usedStorage = (isset($data["used_storage"]) ? $data["used_storage"] : null);
            $user->email = (isset($data["email"]) ? $data["email"] : null);
            $user->passwordHash = (isset($data["password_hash"]) ? $data["password_hash"] : null);

            return $user;
        }

        public function createUser(User $user) : int
        {
            $stmt = $this->conn->prepare("INSERT INTO users (
                name, used_storage, email, password_hash
            ) VALUES (
                :name, :used_storage, :email, :password_hash
            )");
        
            $stmt->bindParam(":name", $user->name);
            $stmt->bindValue(":used_storage", 0);
            $stmt->bindParam(":email", $user->email);
            $stmt->bindParam(":password_hash", $user->passwordHash);

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

        public function createToken(int $userId, string $tokenExpirationDate) : ?string
        {
            $user = $this->getUserById($userId);

            if(!$user)
            {
                return null;
            }

            // loop para evitar que dois token sejam iguais
            do
            {
                $token = bin2hex(random_bytes(50));

                $stmt = $this->conn->prepare("SELECT COUNT(*) FROM tokens WHERE token = :token");
                $stmt->bindParam(":token", $token);
                $stmt->execute();
            }
            while ($stmt->fetchColumn() > 0);

            $stmt = $this->conn->prepare("INSERT INTO tokens (
                token, expires, revoked, user_id
            ) VALUES (
                :token, :expires, :revoked, :user_id
            )");

            $stmt->bindParam(":token", $token);
            $stmt->bindParam(":expires", $tokenExpirationDate);
            $stmt->bindValue(":revoked", 0);
            $stmt->bindParam(":user_id", $user->id);

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
    }
?>
