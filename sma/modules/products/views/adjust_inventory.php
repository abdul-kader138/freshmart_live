<style type="text/css">
    .loader {
        background-color: #CF4342;
        color: white;
        top: 30%;
        left: 50%;
        margin-left: -50px;
        position: fixed;
        padding: 3px;
        width: 100px;
        height: 100px;
        background: url('<?php echo $this->config->base_url(); ?>assets/img/wheel.gif') no-repeat center;
    }

    .blackbg {
        z-index: 5000;
        background-color: #666;
        -ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=50)";
        filter: alpha(opacity=20);
        opacity: 0.2;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        position: absolute;
    }
</style>
<link href="<?php echo $this->config->base_url(); ?>assets/css/bootstrap-fileupload.css" rel="stylesheet">
<script src="<?php echo $this->config->base_url(); ?>assets/js/validation.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $('form').form();
        $('#category').change(function () {
                var v = $(this).val();
                $('#loading').show();
                $.ajax({
                    type: "get",
                    async: false,
                    url: "index.php?module=products&view=getSubCategories",
                    data: {
                <?php echo $this->security->get_csrf_token_name(); ?>:
                "<?php echo $this->security->get_csrf_hash() ?>", category_id
                :
                v
            },
            dataType
        :
        "html",
            success
        :
        function (data) {
            if (data != "") {
                $('#subcat_data').empty();
                $('#subcat_data').html(data);
            } else {
                $('#subcat_data').empty();
                var default_data = '<select name="subcategory" class="span4" id="subcategory" data-placeholder="<?php echo $this->lang->line("select_category_to_load"); ?>"></select>';
                $('#subcat_data').html(default_data);
                bootbox.alert('<?php echo $this->lang->line('no_subcategory'); ?>');
            }
        }

        ,
        error: function () {
            bootbox.alert('<?php echo $this->lang->line('ajax_error'); ?>');
            $('#loading').hide();
        }

    });
    $("form select").chosen({
        no_results_text: "No results matched",
        disable_search_threshold: 5,
        allow_single_deselect: true
    });
    $('#loading').hide(); });});

</script>

<script type="text/javascript">
    $(document).ready(function () {
        $('form').form();
        $('#cf1').change(function () {
                var v = $(this).val();
                $('#loading').show();
                $.ajax({
                    type: "get",
                    async: false,
                    url: "index.php?module=products&view=getRacks",
                    data: {
                <?php echo $this->security->get_csrf_token_name(); ?>:
                "<?php echo $this->security->get_csrf_hash() ?>", shelf_id
                :
                v
            },
            dataType
        :
        "html",
            success
        :
        function (data) {

            if (data != "") {
                $('#rack_data').empty();
                $('#rack_data').html(data);
            } else {
                $('#rack_data').empty();
                var default_data = '<select name="rack" class="span4" id="cf3" data-placeholder="<?php echo $this->lang->line("select_category_to_load"); ?>"></select>';
                $('#rackt_data').html(default_data);
                //bootbox.alert('<?php echo $this->lang->line('no_subcategory'); ?>');
            }
        }

        ,
        error: function () {
            bootbox.alert('<?php echo $this->lang->line('ajax_error'); ?>');
            $('#loading').hide();
        }

    });
    $("form select").chosen({
        no_results_text: "No results matched",
        disable_search_threshold: 5,
        allow_single_deselect: true
    });
    $('#loading').hide();
    })
    ;
    })
    ;

</script>
<?php if ($message) {
    echo "<div class=\"alert alert-error\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $message . "</div>";
} ?>

<h3 class="title"><?php echo $page_title; ?></h3>
<p><?php echo $this->lang->line("enter_product_info"); ?></p>

<?php $attrib = array('class' => 'form-horizontal');
echo form_open_multipart("module=products&view=save_adjust_inventory&id=" .$id."&warehouse_id=".$warehouse_id); ?>

<div class="control-group">
    <label class="control-label" for="code"><?php echo $this->lang->line("product_code"); ?></label>

    <div
        class="controls"> <?php echo form_input('code', $product->code, 'class="span4 tip" id="code" title="' . $this->lang->line("pr_code_tip") . '" required="required" data-error="' . $this->lang->line("product_code") . ' ' . $this->lang->line("is_required") . '"'); ?> </div>
</div>
<div class="control-group">
    <label class="control-label" for="name"><?php echo $this->lang->line("product_name"); ?></label>

    <div
        class="controls"> <?php echo form_input('name', $product->name, 'class="span4 tip" id="name" title="' . $this->lang->line("pr_name_tip") . '" required="required" data-error="' . $this->lang->line("product_name") . ' ' . $this->lang->line("is_required") . '"'); ?> </div>
</div>


<div class="control-group">
    <label class="control-label" for="unit"><?php echo $this->lang->line("product_unit"); ?></label>

    <div
        class="controls"> <?php echo form_input('unit', $product->unit, 'class="span4 tip" id="unit" title="' . $this->lang->line("pr_unit_tip") . '" required="required" data-error="' . $this->lang->line("product_unit") . ' ' . $this->lang->line("is_required") . '"'); ?> </div>
</div>
<div class="control-group">
    <label class="control-label" for="size">Adjustment Quantity</label>

    <div
        class="controls"> <?php echo form_input('aj_quantity', 0, 'class="span4 tip" id="size" title="Adjustment" required="required" '); ?>
        <select name="qnt_sign" id="qnt_sign" required>
            <option>+</option>
            <option>-</option>
        </select>
    </div>
</div>

<div class="control-group">
    <div class="controls"> <?php echo form_submit('submit', "Save", 'class="btn btn-primary"'); ?> </div>
</div>
<?php echo form_close(); ?>
<div id="loading" style="display: none;">
    <div class="blackbg"></div>
    <div class="loader"></div>
</div>
<style>
    .chzn-container {
        width: 50px !important;
    }
</style>


