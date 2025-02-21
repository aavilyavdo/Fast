<?
class QuickPage {
    private $db;
    
    public function __construct() {
        $this->db = new SQLite3('book.db');
        $this->db->exec('CREATE TABLE IF NOT EXISTS pages (id INTEGER PRIMARY KEY, content TEXT)');
    }

    public function save($text) {
        $stmt = $this->db->prepare('INSERT INTO pages (content) VALUES (:content)');
        $stmt->bindValue(':content', $text, SQLITE3_TEXT);
        return $stmt->execute();
    }

    public function get() {
        return $this->db->query('SELECT content FROM pages ORDER BY id DESC LIMIT 50');
    }
}

// Использование
$page = new QuickPage();
$page->save("Новый текст");

$result = $page->get();
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    echo $row['content'] . "\n";
}