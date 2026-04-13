<?php
namespace App\Controllers;
use App\Core\BaseController;
use App\Models\PaymentModel;
use App\Models\OrderModel;

class PaymentController extends BaseController {

    private $paymentModel;
    private $orderModel;

    public function __construct() {
        parent::__construct();
        $this->paymentModel = new PaymentModel();
        $this->orderModel   = new OrderModel();
    }

    // Tạo đơn ZaloPay và redirect sang trang thanh toán
    public function createZaloPayOrder() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/');
            return;
        }

        $id_dh   = $_POST['id_dh'] ?? '';
        $order   = $this->paymentModel->getOrderById($id_dh);

        if (!$order) {
            $_SESSION['error'] = 'Không tìm thấy đơn hàng!';
            $this->redirect('/account/orders');
            return;
        }

        $config = [
            'app_id'   => (int)$_ENV['ZALOPAY_APP_ID'],
            'key1'     => $_ENV['ZALOPAY_KEY1'],
            'key2'     => $_ENV['ZALOPAY_KEY2'],
            'endpoint' => $_ENV['ZALOPAY_ENDPOINT'],
        ];

        $transID     = $id_dh . '_' . time();
        $app_trans_id = date("ymd") . "_" . $transID;
        $amount      = (int)$order['thanh_tien'];

        $embeddata = json_encode([
            'redirecturl' => (defined('BASE_PATH') ? 'http://localhost' . BASE_PATH : 'http://localhost') . '/payment/result?id_dh=' . $id_dh
        ]);

        $zaloOrder = [
            'app_id'       => $config['app_id'],
            'app_time'     => round(microtime(true) * 1000),
            'app_trans_id' => $app_trans_id,
            'app_user'     => $_SESSION['user']['id_tk'] ?? 'guest',
            'item'         => '[]',
            'embed_data'   => $embeddata,
            'amount'       => $amount,
            'description'  => 'GreenMeal - Thanh toán đơn hàng #' . $id_dh,
            'bank_code'    => 'zalopayapp',
            'callback_url' => (defined('BASE_PATH') ? 'http://localhost' . BASE_PATH : 'http://localhost') . '/payment/callback',
        ];

        // Tạo MAC
        $data = $zaloOrder['app_id'] . '|' . $zaloOrder['app_trans_id'] . '|' . $zaloOrder['app_user']
              . '|' . $zaloOrder['amount'] . '|' . $zaloOrder['app_time']
              . '|' . $zaloOrder['embed_data'] . '|' . $zaloOrder['item'];
        $zaloOrder['mac'] = hash_hmac('sha256', $data, $config['key1']);

        // Gọi API ZaloPay
        $context = stream_context_create([
            'http' => [
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($zaloOrder)
            ]
        ]);

        $resp   = file_get_contents($config['endpoint'], false, $context);
        $result = json_decode($resp, true);

        if ($result['return_code'] == 1) {
            // Lưu mã giao dịch
            $this->paymentModel->updateMaGiaoDich($id_dh, $app_trans_id);

            // Redirect sang ZaloPay
            header('Location: ' . $result['order_url']);
            exit;
        } else {
            $_SESSION['error'] = 'Lỗi tạo đơn ZaloPay: ' . ($result['return_message'] ?? 'Không xác định');
            $this->redirect('/account/orders');
        }
    }

    // ZaloPay callback (server-to-server)
    public function callback() {
        $key2  = $_ENV['ZALOPAY_KEY2'];
        $input = json_decode(file_get_contents('php://input'), true);

        $mac = hash_hmac('sha256', $input['data'], $key2);

        $result = [];
        if ($mac !== $input['mac']) {
            $result['return_code'] = -1;
            $result['return_message'] = 'MAC không hợp lệ';
        } else {
            $data     = json_decode($input['data'], true);
            $id_dh    = explode('_', $data['app_trans_id'])[1] ?? '';

            $this->paymentModel->updateThanhToan($id_dh);

            $result['return_code']    = 1;
            $result['return_message'] = 'success';
        }

        echo json_encode($result);
        exit;
    }

    // Trang kết quả sau khi thanh toán
    public function result() {
        $id_dh  = $_GET['id_dh'] ?? '';
        $status = $_GET['status'] ?? ''; // ZaloPay trả về 1=thành công

        $order = $this->paymentModel->getOrderById($id_dh);

        $this->renderView('payment/result', [
            'title'  => 'Kết quả thanh toán',
            'order'  => $order,
            'status' => $status,
            'id_dh'  => $id_dh
        ]);
    }
}