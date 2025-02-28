<?php
    class Template {
        private $title;
        private $currentPage;

        public function __construct($title, $currentPage) {
            $this->title = $title;
            $this->currentPage = $currentPage;
        }

        public function head() {
            $title = $this->title;
            $currentPage = $this->currentPage;
            include __DIR__ . '/../tr/components/head.php';
        }

        public function header() {
            $currentPage = $this->currentPage;
            include  __DIR__ . '/../tr/components/header.php';
        }

        public function footer() {
            include  __DIR__ . '/../tr/components/footer.php';
        }

        public function pageLeftMenu() {
            include  __DIR__ . '/../tr/components/pageLeftMenu.php';
        }

        public function leftMenuProfile() {
            include  __DIR__ . '/../tr/components/leftMenuProfile.php';
        }

        public function uyelikBilgiler() {
            include  __DIR__ . '/../tr/components/uyelikBilgiler.php';
        }
    }
?>