<?php
    class User {
        public $id;
        public $name;
        public $email;
        public $keyprogramming;
        public $education;
        public $profile;
        public $url;

        public function __construct($id, $name, $email, $keyprogramming, $education, $profile, $url) {
            $this->id = $id;
            $this->name = $name;
            $this->email = $email;
            $this->keyprogramming = $keyprogramming;
            $this->education = $education;
            $this->profile = $profile;
            $this->url = $url;
        }

    }

?>