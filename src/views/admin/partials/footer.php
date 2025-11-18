<?php // File: src/views/admin/partials/footer.php ?>

<div class="modal fade" id="confirm_modal" role='dialog'>
    <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Xác nhận</h5>
            </div>
            <div class="modal-body">
                <div id="delete_content"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id='confirm' onclick="">Tiếp tục</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="uni_modal" role='dialog'>
    <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id='submit' onclick="$('#uni_modal form').submit()">Lưu</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Thoát</button>
            </div>
        </div>
    </div>
</div>
<script>
    // Các hàm modal của bạn (uni_modal, _conf)
    $(document).ready(function(){
        window.uni_modal = function($title = '' , $url='',$size=""){
            start_loader() // Bạn cần đảm bảo hàm start_loader() tồn tại
            $.ajax({
                url:$url,
                error:err=>{
                    console.log()
                    alert("An error occured")
                },
                success:function(resp){
                    if(resp){
                        $('#uni_modal .modal-title').html($title)
                        $('#uni_modal .modal-body').html(resp)
                        if($size != ''){
                            $('#uni_modal .modal-dialog').addClass($size+'  modal-dialog-centered')
                        }else{
                            $('#uni_modal .modal-dialog').removeAttr("class").addClass("modal-dialog modal-md modal-dialog-centered")
                        }
                        $('#uni_modal').modal({
                            show:true,
                            backdrop:'static',
                            keyboard:false,
                            focus:true
                        })
                        end_loader() // Bạn cần đảm bảo hàm end_loader() tồn tại
                    }
                }
            })
        }
        window._conf = function($msg='',$func='',$params = []){
            $('#confirm_modal #confirm').attr('onclick',$func+"("+$params.join(',')+")")
            $('#confirm_modal .modal-body').html($msg)
            $('#confirm_modal').modal('show')
        }
    })
</script>
<script>
    $.widget.bridge('uibutton', $.ui.button)
</script>
<script src="<?php echo BASE_URL ?>plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo BASE_URL ?>plugins/chart.js/Chart.min.js"></script>
<script src="<?php echo BASE_URL ?>plugins/sparklines/sparkline.js"></script>
<script src="<?php echo BASE_URL ?>plugins/select2/js/select2.full.min.js"></script>
<script src="<?php echo BASE_URL ?>plugins/jqvmap/jquery.vmap.min.js"></script>
<script src="<?php echo BASE_URL ?>plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
<script src="<?php echo BASE_URL ?>plugins/jquery-knob/jquery.knob.min.js"></script>
<script src="<?php echo BASE_URL ?>plugins/moment/moment.min.js"></script>
<script src="<?php echo BASE_URL ?>plugins/daterangepicker/daterangepicker.js"></script>
<script src="<?php echo BASE_URL ?>plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<script src="<?php echo BASE_URL ?>plugins/summernote/summernote-bs4.min.js"></script>
<script src="<?php echo BASE_URL ?>plugins/datatables/jquery.dataTables.min.js"></script>
<script src="<?php echo BASE_URL ?>plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="<?php echo BASE_URL ?>plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="<?php echo BASE_URL ?>plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="<?php echo BASE_URL ?>dist/js/adminlte.js"></script>