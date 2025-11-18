<?php // File: src/views/admin/product_types/index.php (Đã cập nhật) ?>

<?php
// Biến $productTypes (từ Controller) giờ đã có TEN_DM
?>

<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">Danh sách Loại Hàng Hóa (Thể loại)</h3>
        <div class="card-tools">
            <a href="/admin/manageProductType" class="btn btn-flat btn-primary"><span class="fas fa-plus"></span>  Thêm</a>
        </div>
    </div>
    <div class="card-body">
        <div class="container-fluid">
            <table class="table table-bordered table-stripped" id="product-type-table">
                <colgroup>
                    <col width="5%">
                    <col width="25%">
                    <col width="30%">
                    <col width="25%">
                    <col width="15%">
                </colgroup>
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Danh mục (Cha)</th> <th>Mã Loại (ID_LHH)</th>
                        <th>Tên Loại (TEN_LHH)</th>
                        <th>Hoạt động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    foreach($productTypes as $i => $pt): 
                    ?>
                        <tr>
                            <td class="text-center"><?php echo $i + 1; ?></td>
                            <td><?php echo $pt['TEN_DM'] ?></td> <td><?php echo $pt['ID_LHH'] ?></td>
                            <td><?php echo $pt['TEN_LHH'] ?></td>
                            <td align="center">
                                <button type="button" class="btn btn-flat btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
                                    Hành động
                                    <span class="sr-only">Toggle Dropdown</span>
                                </button>
                                <div class="dropdown-menu" role="menu">
                                    <a class="dropdown-item" href="/admin/manageProductType?id=<?php echo $pt['ID_LHH'] ?>"><span class="fa fa-edit text-primary"></span> Sửa</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item delete_data" href="javascript:void(0)" data-id="<?php echo $pt['ID_LHH'] ?>"><span class="fa fa-trash text-danger"></span> Xóa</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    // Toàn bộ JS giữ nguyên như cũ, nó đã đúng
    $(document).ready(function(){
        
        $('.delete_data').click(function(){
            _conf("Bạn chắc chắn xóa Loại hàng hóa này không?","delete_product_type",[$(this).attr('data-id')])
        })
        
        $('#product-type-table').dataTable({
            language: {
                paginate: { previous: "Trước", next: "Sau" },
                info: "Hiển thị _START_ đến _END_ của _TOTAL_ mục",
                lengthMenu: "Hiển thị _MENU_ mục",
                search: "Tìm kiếm:",
                zeroRecords: "Không tìm thấy kết quả nào"
            }
        });
    })

    function delete_product_type($id){
        start_loader();
        $.ajax({
            url: '/admin/deleteProductType',
            method:"POST",
            data:{id: $id},
            dataType:"json",
            error:err=>{
                console.log(err)
                alert_toast("An error occured.",'error');
                end_loader();
            },
            success:function(resp){
                if(typeof resp== 'object' && resp.status == 'success'){
                    location.reload(); 
                } else if (resp.status == 'failed' && !!resp.msg) {
                    alert_toast(resp.msg,'error');
                    end_loader();
                } else {
                    alert_toast("An error occured.",'error');
                    end_loader();
                }
            }
        })
    }
</script>