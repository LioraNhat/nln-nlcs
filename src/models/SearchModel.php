<?php
namespace App\Models;
use App\Core\BaseModel;
use PDO;

class SearchModel extends BaseModel {

    private array $stopWords = [
        'tìm', 'cho', 'tôi', 'mua', 'cần', 'muốn', 'kiếm',
        'giúp', 'ơi', 'ạ', 'nhé', 'nào', 'đâu', 'có', 'không',
        'hãy', 'được', 'một', 'và', 'với', 'các', 'những',
        'bán', 'xem', 'thử', 'loại', 'sản', 'phẩm', 'ở', 'là',
    ];

    /**
     * Lấy toàn bộ tên sản phẩm từ DB, tách thành danh sách cụm từ quan trọng
     * Đây là "từ điển" được train từ dữ liệu thực tế
     */
    private function buildDictionary(): array {
        $stmt = $this->db->query("SELECT ten_hh FROM hang_hoa WHERE duoc_phep_ban = 1");
        $rows = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $dictionary = [];

        foreach ($rows as $name) {
            $name = mb_strtolower(trim($name), 'UTF-8');

            // Lấy cụm từ trước dấu ngoặc — VD: "cá basa kho tiêu (khay 300g)" → "cá basa kho tiêu"
            if (preg_match('/^([^\(]+)/', $name, $matches)) {
                $clean = trim($matches[1]);
                if ($clean) $dictionary[] = $clean;
            }

            // Tách từng từ đơn có độ dài >= 2 ký tự
            $words = preg_split('/[\s\/\-\(\)]+/', $name);
            foreach ($words as $word) {
                $word = trim($word);
                if (mb_strlen($word, 'UTF-8') >= 2 && !in_array($word, $this->stopWords)) {
                    $dictionary[] = $word;
                }
            }

            // Tách cụm 2 từ liền nhau — VD: "cá basa", "basa kho", "kho tiêu"
            $wordList = preg_split('/\s+/', $name);
            for ($i = 0; $i < count($wordList) - 1; $i++) {
                $bigram = $wordList[$i] . ' ' . $wordList[$i + 1];
                if (!in_array($wordList[$i], $this->stopWords)) {
                    $dictionary[] = $bigram;
                }
            }
        }

        // Loại trùng, sắp xếp dài trước để ưu tiên cụm dài match trước
        $dictionary = array_unique($dictionary);
        usort($dictionary, fn($a, $b) => mb_strlen($b, 'UTF-8') - mb_strlen($a, 'UTF-8'));

        return $dictionary;
    }

    /**
     * So khớp câu nói user với từ điển DB
     * Trả về cụm từ khóa tốt nhất tìm được
     */
    private function extractFromDictionary(string $input): string {
        $input = mb_strtolower(trim($input), 'UTF-8');
        $dictionary = $this->buildDictionary();

        $matched = [];

        foreach ($dictionary as $term) {
            if (mb_strpos($input, $term, 0, 'UTF-8') !== false) {
                // Tránh match trùng — nếu đã có cụm dài hơn chứa term này thì bỏ qua
                $alreadyCovered = false;
                foreach ($matched as $existing) {
                    if (mb_strpos($existing, $term, 0, 'UTF-8') !== false) {
                        $alreadyCovered = true;
                        break;
                    }
                }
                if (!$alreadyCovered) {
                    $matched[] = $term;
                }
            }
        }

        // Nếu tìm được từ điển thì dùng, không thì fallback lọc stop words
        if (!empty($matched)) {
            return implode(' ', $matched);
        }

        // Fallback: lọc stop words từ câu gốc
        $words = preg_split('/\s+/', $input);
        $filtered = array_filter($words, fn($w) =>
            !in_array($w, $this->stopWords) && mb_strlen($w, 'UTF-8') > 1
        );
        return implode(' ', $filtered);
    }

    public function searchProducts(string $keyword, array $filters = []): array {
        $keyword = $this->extractFromDictionary($keyword);

        if ($keyword === '') return [];

        // Lọc giá
        $priceCondition = '';
        switch ($filters['price'] ?? '') {
            case 'under_100k':    $priceCondition = 'AND g.gia_hien_tai < 100000'; break;
            case '100000-200000': $priceCondition = 'AND g.gia_hien_tai BETWEEN 100000 AND 200000'; break;
            case '200000-300000': $priceCondition = 'AND g.gia_hien_tai BETWEEN 200000 AND 300000'; break;
            case '300000-400000': $priceCondition = 'AND g.gia_hien_tai BETWEEN 300000 AND 400000'; break;
            case 'over_400k':     $priceCondition = 'AND g.gia_hien_tai > 400000'; break;
        }

        // Sắp xếp — chỉ áp dụng khi user chọn, còn mặc định để score quyết định
        $userSort = $filters['sort'] ?? 'name_asc';
        $sortClause = match($userSort) {
            'name_desc'  => 'ORDER BY h.ten_hh DESC',
            'price_asc'  => 'ORDER BY g.gia_hien_tai ASC',
            'price_desc' => 'ORDER BY g.gia_hien_tai DESC',
            default      => 'ORDER BY score DESC, h.ten_hh ASC', // mặc định: liên quan nhất lên đầu
        };

        $baseJoin = "
            FROM hang_hoa h
            LEFT JOIN lo_hang l 
                ON l.id_hh = h.id_hh 
                AND l.so_luong_con_lai > 0 
                AND l.id_trang_thai_lo IN ('TTL01','TTL02')
            LEFT JOIN gia_ban_hien_tai g 
                ON g.id_lo = l.id_lo
            LEFT JOIN thoi_diem t 
                ON t.id_td = g.id_td 
                AND NOW() BETWEEN t.ngay_bd_gia_ban AND t.ngay_kt_gia_ban
            WHERE h.duoc_phep_ban = 1
            $priceCondition
        ";

        // Tách từng từ để tính score
        $words = array_filter(
            preg_split('/\s+/', $keyword),
            fn($w) => mb_strlen($w, 'UTF-8') >= 2
        );

        // Xây score expression: mỗi từ khớp trong ten_hh = 2 điểm, trong mo_ta_hh = 1 điểm
        // Khớp cụm đầy đủ trong ten_hh = 5 điểm (thưởng)
        $scoreParts = [];
        $params = [];

        foreach ($words as $i => $word) {
            $scoreParts[] = "(CASE WHEN h.ten_hh LIKE :sw{$i} THEN 2 ELSE 0 END)";
            $scoreParts[] = "(CASE WHEN h.mo_ta_hh LIKE :sw{$i} THEN 1 ELSE 0 END)";
            $params[":sw{$i}"] = '%' . $word . '%';
        }

        // Thưởng thêm nếu khớp nguyên cụm từ khóa
        $scoreParts[] = "(CASE WHEN h.ten_hh LIKE :fullkw THEN 5 ELSE 0 END)";
        $params[':fullkw'] = '%' . $keyword . '%';

        $scoreExpr = implode(' + ', $scoreParts);

        // Điều kiện LIKE: có ít nhất 1 từ khớp
        $likeParts = [];
        foreach ($words as $i => $word) {
            $likeParts[] = "h.ten_hh LIKE :sw{$i} OR h.mo_ta_hh LIKE :sw{$i}";
        }
        $likeCondition = implode(' OR ', $likeParts);

        $sql = "
            SELECT 
                h.id_hh, h.ten_hh, h.mo_ta_hh, h.link_anh,
                g.gia_hien_tai, l.so_luong_con_lai,
                ($scoreExpr) AS score
            $baseJoin
                AND ($likeCondition)
            GROUP BY h.id_hh
            $sortClause
            LIMIT 40
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Nếu vẫn không có → thử FULLTEXT
        if (empty($rows)) {
            $sql = "
                SELECT 
                    h.id_hh, h.ten_hh, h.mo_ta_hh, h.link_anh,
                    g.gia_hien_tai, l.so_luong_con_lai,
                    MATCH(h.ten_hh, h.mo_ta_hh) AGAINST (:kw IN BOOLEAN MODE) AS score
                $baseJoin
                    AND MATCH(h.ten_hh, h.mo_ta_hh) AGAINST (:kw IN BOOLEAN MODE)
                GROUP BY h.id_hh
                ORDER BY score DESC
                LIMIT 40
            ";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':kw' => $keyword . '*']);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        return $rows;
    }
}