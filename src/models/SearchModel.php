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

    // =====================================================
    // 1. CHUẨN HÓA TIẾNG VIỆT KHÔNG DẤU
    // =====================================================
    private function removeAccents(string $str): string {
        $str = mb_strtolower(trim($str), 'UTF-8');
        $accents = [
            'à','á','ả','ã','ạ','ă','ắ','ằ','ẳ','ẵ','ặ','â','ấ','ầ','ẩ','ẫ','ậ',
            'è','é','ẻ','ẽ','ẹ','ê','ế','ề','ể','ễ','ệ',
            'ì','í','ỉ','ĩ','ị',
            'ò','ó','ỏ','õ','ọ','ô','ố','ồ','ổ','ỗ','ộ','ơ','ớ','ờ','ở','ỡ','ợ',
            'ù','ú','ủ','ũ','ụ','ư','ứ','ừ','ử','ữ','ự',
            'ỳ','ý','ỷ','ỹ','ỵ','đ',
        ];
        $noAccents = [
            'a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','a',
            'e','e','e','e','e','e','e','e','e','e','e',
            'i','i','i','i','i',
            'o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o',
            'u','u','u','u','u','u','u','u','u','u','u',
            'y','y','y','y','y','d',
        ];
        return str_replace($accents, $noAccents, $str);
    }

    // =====================================================
    // BƯỚC 1: LỌC STOP WORDS TRƯỚC — trả về câu sạch
    // =====================================================
    private function cleanInput(string $input): string {
        $input = mb_strtolower(trim($input), 'UTF-8');
        $words = preg_split('/\s+/', $input);
        $filtered = array_filter(
            $words,
            fn($w) => !in_array($w, $this->stopWords) && mb_strlen($w, 'UTF-8') > 1
        );
        return implode(' ', $filtered);
    }

    // =====================================================
    // 2. XÂY DỰNG TỪ ĐIỂN TỪ DỮ LIỆU DB
    // =====================================================
    private function buildDictionary(): array {
        $dictionary = [];

        $stmt = $this->db->query("SELECT ten_hh FROM hang_hoa WHERE duoc_phep_ban = 1");
        $productNames = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $stmt = $this->db->query("SELECT ten_dm FROM danh_muc");
        $catNames = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $stmt = $this->db->query("SELECT ten_loai FROM loai_hang_hoa");
        $typeNames = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $allNames = array_merge($productNames, $catNames, $typeNames);

        foreach ($allNames as $name) {
            $name = mb_strtolower(trim($name), 'UTF-8');
            if (!$name) continue;

            // Cụm trước ngoặc/gạch ngang
            if (preg_match('/^([^\(\-]+)/', $name, $matches)) {
                $clean = trim($matches[1]);
                if ($clean) {
                    $dictionary[] = $clean;
                    $dictionary[] = $this->removeAccents($clean);
                }
            }

            // Từ đơn >= 2 ký tự
            $words = preg_split('/[\s\/\-\(\)]+/', $name);
            foreach ($words as $word) {
                $word = trim($word);
                if (mb_strlen($word, 'UTF-8') >= 2 && !in_array($word, $this->stopWords)) {
                    $dictionary[] = $word;
                    $dictionary[] = $this->removeAccents($word);
                }
            }

            // Bigram
            $wordList = preg_split('/\s+/', $name);
            for ($i = 0; $i < count($wordList) - 1; $i++) {
                if (!in_array($wordList[$i], $this->stopWords)) {
                    $bigram = $wordList[$i] . ' ' . $wordList[$i + 1];
                    $dictionary[] = $bigram;
                    $dictionary[] = $this->removeAccents($bigram);
                }
            }

            // Trigram
            for ($i = 0; $i < count($wordList) - 2; $i++) {
                if (!in_array($wordList[$i], $this->stopWords)) {
                    $trigram = $wordList[$i] . ' ' . $wordList[$i + 1] . ' ' . $wordList[$i + 2];
                    $dictionary[] = $trigram;
                    $dictionary[] = $this->removeAccents($trigram);
                }
            }
        }

        $dictionary = array_unique($dictionary);
        usort($dictionary, fn($a, $b) => mb_strlen($b, 'UTF-8') - mb_strlen($a, 'UTF-8'));

        return $dictionary;
    }

    // =====================================================
    // 3. TRÍCH XUẤT TỪ KHÓA — nhận input đã sạch
    // BƯỚC 2: Giới hạn terms, chỉ giữ cụm liên quan nhất
    // =====================================================
    private function extractFromDictionary(string $cleanedInput): array {
        $inputNoAccent = $this->removeAccents($cleanedInput);
        $dictionary    = $this->buildDictionary();

        $matched = [];

        foreach ($dictionary as $term) {
            $termNoAccent = $this->removeAccents($term);

            $hitOriginal = mb_strpos($cleanedInput, $term, 0, 'UTF-8') !== false;
            $hitNoAccent = mb_strpos($inputNoAccent, $termNoAccent, 0, 'UTF-8') !== false;

            if ($hitOriginal || $hitNoAccent) {
                $alreadyCovered = false;
                foreach ($matched as $existing) {
                    if (mb_strpos($existing, $term, 0, 'UTF-8') !== false ||
                        mb_strpos($this->removeAccents($existing), $termNoAccent, 0, 'UTF-8') !== false) {
                        $alreadyCovered = true;
                        break;
                    }
                }
                if (!$alreadyCovered) {
                    $matched[] = $term;
                }
            }
        }

        // Fallback: dùng từng từ trong input đã sạch
        if (empty($matched)) {
            $words   = preg_split('/\s+/', $cleanedInput);
            $matched = array_values(array_filter(
                $words,
                fn($w) => mb_strlen($w, 'UTF-8') > 1
            ));
        }

        // BƯỚC 2: Chỉ giữ tối đa 5 cụm dài nhất, bỏ cụm 1 từ nếu đã có cụm dài hơn chứa nó
        usort($matched, fn($a, $b) => mb_strlen($b, 'UTF-8') - mb_strlen($a, 'UTF-8'));

        $filtered = [];
        foreach ($matched as $term) {
            $covered = false;
            foreach ($filtered as $kept) {
                if (mb_strpos($this->removeAccents($kept), $this->removeAccents($term), 0, 'UTF-8') !== false) {
                    $covered = true;
                    break;
                }
            }
            if (!$covered) {
                $filtered[] = $term;
            }
            if (count($filtered) >= 5) break;
        }

        return $filtered;
    }

    // =====================================================
    // 4. HÀM CHÍNH: TÌM KIẾM SẢN PHẨM
    // =====================================================
    public function searchProducts(string $keyword, array $filters = []): array {

        // BƯỚC 1: Lọc stop words trước khi xử lý
        $cleanedInput = $this->cleanInput($keyword);

        if ($cleanedInput === '') return [];

        // --- Lọc giá ---
        $priceCondition = '';
        switch ($filters['price'] ?? '') {
            case 'under_100k':    $priceCondition = 'AND g.gia_hien_tai < 100000'; break;
            case '100000-200000': $priceCondition = 'AND g.gia_hien_tai BETWEEN 100000 AND 200000'; break;
            case '200000-300000': $priceCondition = 'AND g.gia_hien_tai BETWEEN 200000 AND 300000'; break;
            case '300000-400000': $priceCondition = 'AND g.gia_hien_tai BETWEEN 300000 AND 400000'; break;
            case 'over_400k':     $priceCondition = 'AND g.gia_hien_tai > 400000'; break;
        }

        // --- Sắp xếp ---
        $userSort   = $filters['sort'] ?? 'name_asc';
        $sortClause = match($userSort) {
            'name_desc'  => 'ORDER BY h.ten_hh DESC',
            'price_asc'  => 'ORDER BY g.gia_hien_tai ASC',
            'price_desc' => 'ORDER BY g.gia_hien_tai DESC',
            default      => 'ORDER BY score DESC, h.ten_hh ASC',
        };

        // --- Base JOIN dùng chung ---
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

        // BƯỚC 2: Trích xuất terms từ input đã sạch
        $terms = $this->extractFromDictionary($cleanedInput);

        if (empty($terms)) return [];

        // Chọn fullKeyword thông minh
        $inputWords = preg_split('/\s+/', $this->removeAccents($cleanedInput));
        $bestTerm   = $terms[0];
        $bestScore  = 0;

        foreach ($terms as $term) {
            $termWords  = preg_split('/\s+/', $this->removeAccents($term));
            $matchCount = count(array_intersect($inputWords, $termWords));
            if ($matchCount > $bestScore) {
                $bestScore = $matchCount;
                $bestTerm  = $term;
            }
        }

        $fullKeyword = $bestTerm;
        $scoreParts  = [];
        $likeParts   = [];
        $params      = [];

        foreach ($terms as $i => $term) {
            $wordCount   = count(preg_split('/\s+/', $term));
            $scoreInName = $wordCount >= 2 ? 4 : 2;
            $scoreInDesc = $wordCount >= 2 ? 2 : 1;

            $scoreParts[] = "(CASE WHEN h.ten_hh LIKE :sw{$i} THEN {$scoreInName} ELSE 0 END)";
            $scoreParts[] = "(CASE WHEN h.mo_ta_hh LIKE :sw{$i} THEN {$scoreInDesc} ELSE 0 END)";
            $likeParts[]  = "h.ten_hh LIKE :sw{$i} OR h.mo_ta_hh LIKE :sw{$i}";
            $params[":sw{$i}"] = '%' . $term . '%';
        }

        // Thưởng 5 điểm khớp cụm ở bất kỳ vị trí
        $scoreParts[]      = "(CASE WHEN h.ten_hh LIKE :fullkw THEN 5 ELSE 0 END)";
        $params[':fullkw'] = '%' . $fullKeyword . '%';

        // Thưởng 10 điểm nếu tên BẮT ĐẦU bằng từ khóa
        $scoreParts[]       = "(CASE WHEN h.ten_hh LIKE :startkw THEN 10 ELSE 0 END)";
        $params[':startkw'] = $fullKeyword . '%';

        $scoreExpr     = implode(' + ', $scoreParts);
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

        // BƯỚC 3: Lọc kết quả có điểm quá thấp — phải đạt tối thiểu 3 điểm
        $rows = array_values(array_filter($rows, fn($row) => $row['score'] >= 3));

        // Fallback FULLTEXT nếu không có kết quả
        if (empty($rows)) {
            $ftkw = implode(' ', $terms);
            $sql  = "
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
            $stmt->execute([':kw' => $ftkw . '*']);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        return $rows;
    }
}