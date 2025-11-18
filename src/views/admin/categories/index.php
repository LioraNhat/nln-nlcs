<?php // File: src/views/admin/categories/index.php ?>

<?php
// Ghi chú: Biến $categories được truyền từ AdminController::categories()
// Bạn có thể xóa 4 dòng 'if(false)' này nếu muốn
?>
<?php if(false): // Tạm thời vô hiệu hóa flash message ?>
<script>
    alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>


<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">Danh sách danh mục</h3>
        <div class="card-tools">
            <a href="/admin/manageCategory" class="btn btn-flat btn-primary"><span class="fas fa-plus"></span>  Thêm</a>
        </div>
    </div>
    <div class="card-body">
        <div class="container-fluid">
            <table class="table table-bordered table-stripped" id="category-table">
                <colgroup>
                    <col width="10%">
                    <col width="30%">
                    <col width="40%">
                    <col width="20%">
                </colgroup>
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Mã Danh Mục</th>
                        <th>Tên Danh Mục</th>
                        <th>Hoạt động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // Dùng biến $categories từ Controller
                    foreach($categories as $i => $category): 
                    ?>
                        <tr>
                            <td class="text-center"><?php echo $i + 1; ?></td>
                            <td><?php echo $category['ID_DM'] ?></td>
                            <td><?php echo $category['TEN_DM'] ?></td>
                            <td align="center">
                                <button type="button" class="btn btn-flat btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
                                    Hành động
                                    <span class="sr-only">Toggle Dropdown</span>
                                </button>
                                <div class="dropdown-menu" role="menu">
                                    <a class="dropdown-item" href="/admin/manageCategory?id=<?php echo $category['ID_DM'] ?>"><span class="fa fa-edit text-primary"></span> Sửa</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item delete_data" href="javascript:void(0)" data-id="<?php echo $category['ID_DM'] ?>"><span class="fa fa-trash text-danger"></span> Xóa</a>
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
    $(document).ready(function(){
        
        $('.delete_data').click(function(){
            _conf("Bạn chắc chắn xóa danh mục này không?","delete_category",[$(this).attr('data-id')])
        })
        
        $('#category-table').dataTable({
            language: { // Giữ nguyên phần tùy chỉnh ngôn ngữ của bạn
                paginate: { previous: "Trước", next: "Sau" },
                info: "Hiển thị _START_ đến _END_ của _TOTAL_ mục",
                lengthMenu: "Hiển thị _MENU_ mục",
                search: "Tìm kiếm:",
                zeroRecords: "Không tìm thấy kết quả nào"
            }
        });
    })

    // Hàm xử lý logic xóa
    function delete_category($id){
        start_loader();
        $.ajax({
            // ĐÃ SỬA URL: Trỏ đến /admin/deleteCategory
            url: '/admin/deleteCategory',
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