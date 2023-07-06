<?php
    class Message
    {
        public string $text;
        public string $type;

        public const TYPE_SUCCESS = "success";
        public const TYPE_ERROR = "error";

        public function __construct()
        {
            $this->text = "";
            $this->type = "";
        }
        public function load() : void
        {
            if(empty($_SESSION["msg_text"]) || empty($_SESSION["msg_type"]))
            {
                return;
            }

            $this->text = $_SESSION["msg_text"];
            $this->type = $_SESSION["msg_type"];
        }

        public function set(string $text, string $type) : void
        {
            $_SESSION["msg_text"] = $text;
            $_SESSION["msg_type"] = $type;

            $this->text = $text;
            $this->type = $type;
        }

        public function destroy() : void
        {
            $_SESSION["msg_text"] = "";
            $_SESSION["msg_type"] = "";

            $this->text = "";
            $this->type = "";
        }
    }
?>
