<?php
namespace App\Controllers;
use App\Core\BaseController;
use App\Models\SearchModel;

class SearchController extends BaseController {

    private $searchModel;

    public function __construct() {
        parent::__construct();
        $this->searchModel = new SearchModel();
    }

    public function search() {
        $query = trim($_GET['q'] ?? '');
        $filters = [
            'price' => $_GET['price'] ?? '',
            'sort'  => $_GET['sort'] ?? 'name_asc',
        ];

        $results = [];
        if ($query !== '') {
            $results = $this->searchModel->searchProducts($query, $filters);
        }

        $this->renderView('search/index', [
            'title'      => 'Kết quả tìm kiếm: ' . htmlspecialchars($query),
            'searchTerm' => $query,
            'products'   => $results,
            'filters'    => $filters,
        ]);
    }
}