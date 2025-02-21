<?
class FastCompressPage {
    private $redis;
    private $lockKey = 'page_lock';
    private $pageKey = 'pages';
    private $compressionThreshold = 5; // seconds
    
    public function __construct() {
        $this->redis = new Redis();
        $this->redis->connect('127.0.0.1', 6379);
    }

    public function save($text) {
        $startTime = microtime(true);
        
        if ($this->acquireLock()) {
            try {
                $id = $this->redis->incr('page_id');
                
                // Проверяем время выполнения
                if ((microtime(true) - $startTime) > $this->compressionThreshold) {
                    $text = $this->compress($text);
                    $this->redis->hSet($this->pageKey . ':compressed', $id, '1');
                }
                
                $this->redis->hSet($this->pageKey, $id, $text);
                $this->redis->rPush('page_list', $id);
                return true;
            } finally {
                $this->releaseLock();
            }
        }
        return false;
    }

    public function get($limit = 50) {
        $ids = $this->redis->lRange('page_list', 0, $limit - 1);
        return array_map(function($id) {
            $text = $this->redis->hGet($this->pageKey, $id);
            if ($this->redis->hGet($this->pageKey . ':compressed', $id)) {
                $text = $this->decompress($text);
            }
            return $text;
        }, $ids);
    }

    private function compress($data) {
        return gzcompress($data, 9);
    }

    private function decompress($data) {
        return gzuncompress($data);
    }

    private function acquireLock() {
        return $this->redis->set($this->lockKey, 1, ['NX', 'EX' => 5]);
    }

    private function releaseLock() {
        $this->redis->del($this->lockKey);
    }
}

// Usage example
$page = new FastCompressPage();
$page->save("Large content here");
print_r($page->get());?>
