<?php // File: src/views/admin/categories/manage.php ?>

<?php
// Ghi chú: Biến $category và $is_update được truyền từ AdminController::manageCategory()
?>

<div class="card card-outline card-info">
    <div class="card-header">
        <h3 class="card-title"><?php echo $is_update ? "Cập nhật " : "Thêm mới " ?> Danh mục</h3>
    </div>
    <div class="card-body">
        <form action="" id="category-form">
            
            <?php if($is_update): ?>
                <input type="hidden" name="id_update" value="1">
            <?php endif; ?>

            <div class="form-group">
                <label for="ID_DM" class="control-label">Mã Danh Mục (vd: DM01, DM02)</label>
                <input 
                    type="text" 
                    name="ID_DM" 
                    class="form-control form no-resize" 
                    value="<?php echo $category ? $category['ID_DM'] : ''; ?>"
                    <?php echo $is_update ? 'readonly' : '' ?> required
                >
            </div>

            <div class="form-group">
                <label for="TEN_DM" class="control-label">Tên danh mục</label>
                <textarea 
                    name="TEN_DM" 
                    id="TEN_DM" 
                    cols="30" rows="2" 
                    class="form-control form no-resize"
                    required
                ><?php echo $category ? $category['TEN_DM'] : ''; ?></textarea>
            </div>
            
        </form>
    </div>
    <div class="card-footer">
        <button class="btn btn-flat btn-primary" form="category-form">Lưu</button>
        <a class="btn btn-flat btn-default" href="/admin/categories">Thoát</a>
    </div>
</div>

<script>
    $(document).ready(function(){
        $('#category-form').submit(function(e){
            e.preventDefault();
            var _this = $(this)
            $('.err-msg').remove();
            start_loader();
            
            $.ajax({
                // ĐÃ SỬA URL: Trỏ đến /admin/saveCategory
                url: '/admin/saveCategory',
                data: new FormData($(this)[0]),
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                type: 'POST',
                dataType: 'json',
                error:err=>{
                    console.log(err)
                    alert_toast("An error occured",'error');
                    end_loader();
                },
                success:function(resp){
                    if(typeof resp =='object' && resp.status == 'success'){
                        // ĐÃ SỬA URL: Chuyển hướng về /admin/categories
                        location.href = "/admin/categories";
                    }else if(resp.status == 'failed' && !!resp.msg){
                        var el = $('<div>')
                        el.addClass("alert alert-danger err-msg").text(resp.msg)
                        _this.prepend(el)
                        el.show('slow')
                        $("html, body").animate({ scrollTop: _this.closest('.card').offset().top }, "fast");
                        end_loader()
                    }else{
                        alert_toast("An error occured",'error');
                        end_loader();
                        console.log(resp)
                    }
                }
            })
        })
    })
</script>