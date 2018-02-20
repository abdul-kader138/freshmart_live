<link href="<?php echo $this->config->base_url(); ?>assets/css/datepicker.css" rel="stylesheet">
<style type="text/css">
    .table th {
        text-align: center;
    }

    .table td {
        vertical-align: middle;
    }

    .table td:last-child {
        text-align: center !important;
    }
</style>
<script src="<?php echo $this->config->base_url(); ?>assets/js/jquery-ui.js"></script>
<link href="<?php echo $this->config->base_url(); ?>assets/css/redactor.css" rel="stylesheet">
<script src="<?php echo $this->config->base_url(); ?>assets/js/redactor.min.js"></script>
<script src="<?php echo $this->config->base_url(); ?>assets/js/validation.js"></script>
<?php
$pr_value = sizeof($inv_products);
$cno = $pr_value + 1;

?>
<script type="text/javascript">
$(document).ready(function () {

    $('#byTab a, #noteTab a').click(function (e) {
        e.preventDefault();
        $(this).tab('show');
    });
    //$('#byTab #select_by_code, #noteTab a:last').tab('show');
    //$('#byTab #select_by_codes, #noteTab a:last').tab('show');
    $('#byTab #select_by_name, #noteTab a:last').tab('show');
    $("#date").datepicker({
        format: "<?php echo JS_DATE; ?>",
        autoclose: true
    });
    $("#date").datepicker("setDate", new Date());
    $('form').form();

    var count = <?php echo $cno; ?>;
    var an = <?php echo $cno; ?>;
    var tax_rates = <?php echo json_encode($tax_rates); ?>;
    var DT = <?php echo DEFAULT_TAX; ?>;
    $('#code').keydown(function (e) {
        var item_cost, item_name, item_code, pr_tax;

        if (e.keyCode == 13) {

            item_code = $(this).val();

            $.ajax({
                type: "get",
                async: false,
                url: "<?php echo $this->config->base_url(); ?>index.php?module=inventories&view=scan_item",
                data: {
            <?php echo $this->security->get_csrf_token_name(); ?>:
            "<?php echo $this->security->get_csrf_hash() ?>", code
        :
            item_code,
                wh:$("#warehouse").val()

        }
        ,
        dataType: "json",
            success
        :
        function (data) {

            $("#product_name").val(data.name);
            $("#um").val(data.um);
            $("#product_code").val(data.code);

        }

        ,
        error: function () {
            alert('<?php echo $this->lang->line('code_error'); ?>');
            item_name = false;
        }

    });

    e.preventDefault();
    return false;
}

})
;

$('#code').bind('keypress', function (e) {
    if (e.keyCode == 13) {
        e.preventDefault();
        return false;
    }
});

$("#dyTable").on("click", '.del', function () {

    var delID = $(this).attr('id');

    row_id = $("#row_" + delID);
    row_id.remove();

    an--;

});

<?php
if ($this->input->post('submit')) {
    echo "$('.item_name').hide();";
}
?>
$(".show_hide").slideDown('slow');

$('.show_hide').click(function () {
    $(".item_name").slideToggle();
});

$("#name").autocomplete({
    source: function (request, response) {
        $.ajax({
            url: "<?php echo site_url('module=inventories&view=suggestions'); ?>",
            data: {
        <?php echo $this->security->get_csrf_token_name(); ?>:
        "<?php echo $this->security->get_csrf_hash() ?>", term
        :
        $("#name").val()
    },
    dataType: "json",
    type: "get",
    success: function (data) {
        response(data);
    },
    error: function (result) {
        alert('<?php echo $this->lang->line('no_suggestions'); ?>');
        $('.ui-autocomplete-input').removeClass("ui-autocomplete-loading");
        $('#codes').val('');
        return false;
    }
});
},
minLength: 2,
    select
:
function (event, ui) {
    $(this).removeClass('ui-autocomplete-loading');


    var item_code;
    var item_cost;
    var pr_tax;
    var item_name = ui.item.label;

    $.ajax({
        type: "get",
        async: false,
        url: "<?php echo $this->config->base_url(); ?>index.php?module=inventories&view=add_item",
        data: {
    <?php echo $this->security->get_csrf_token_name(); ?>:
    "<?php echo $this->security->get_csrf_hash() ?>", name
:
    item_name
}
,
dataType: "json",
    success
:
function (data) {

    $("#product_name").val(item_name);
    $("#um").val(data.um);
    $("#product_code").val(data.code);

}
,
error: function () {
    alert('<?php echo $this->lang->line('code_error'); ?>');
    $('.ui-autocomplete-loading').removeClass("ui-autocomplete-loading");
    item_name = false;
}

})
;

if (item_name == false) {
    $(this).val('');
    return false;
}

},
close: function () {
    $('#name').val('');
}
})
;

$("#codes").autocomplete({
    source: function (request, response) {
        $.ajax({
            url: "<?php echo site_url('module=inventories&view=codeSuggestions'); ?>",
            data: {
        <?php echo $this->security->get_csrf_token_name(); ?>:
        "<?php echo $this->security->get_csrf_hash() ?>", term
        :
        $("#codes").val()
    },
    dataType: "json",
    type: "get",
    success: function (data) {
        response(data);
    },
    error: function (result) {
        alert('<?php echo $this->lang->line('no_suggestions'); ?>');
        $('.ui-autocomplete-input').removeClass("ui-autocomplete-loading");
        $('#codes').val('');
        return false;
    }
});
},
minLength: 2,
    select
:
function (event, ui) {
    $(this).removeClass('ui-autocomplete-loading');

    var item_cost;
    var pr_tax;
    var item_code = ui.item.label;


    $.ajax({
        type: "get",
        async: false,
        url: "<?php echo $this->config->base_url(); ?>index.php?module=inventories&view=scan_item",
        data: {
    <?php echo $this->security->get_csrf_token_name(); ?>:
    "<?php echo $this->security->get_csrf_hash() ?>", code
:
    item_code,
        wh:$("#warehouse").val()
}
,
dataType: "json",
    success
:
function (data) {

    $("#product_name").val(data.name);
    $("#um").val(data.um);
    $("#product_code").val(data.code);


}
,
error: function () {
    alert('<?php echo $this->lang->line('code_error'); ?>');
    item_name = false;
}

})
;


},
close: function () {
    $('#codes').val('');
}
})
;

$(".ui-autocomplete ").addClass('span4');
$('#item_name').bind('keypress', function (e) {
    if (e.keyCode == 13) {
        e.preventDefault();
        return false;
    }
});
$("form").submit(function () {

});

$('#supplier_l').on('click', function () {
    setTimeout(function () {
        $('#supplier_s').trigger('liszt:open');
    }, 0);
});
$('#warehouse_l').on('click', function () {
    setTimeout(function () {
        $('#warehouse_s').trigger('liszt:open');
    }, 0);
});
$("#add_options").draggable({refreshPositions: true});

$("#product_name").val("");
$("#um").val("");
$("#product_code").val("");
$("#count_quantity").val(0);

})
;
</script>

<?php if ($message) {
    echo "<div class=\"alert alert-error\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $message . "</div>";
} ?>
<?php if ($this->session->set_flashdata('success_message')) {
    echo "<div class=\"alert alert-success\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $this->session->set_flashdata('success_message') . "</div>";
} ?>
<?php if ($success_message) {
    echo "<div class=\"alert alert-success\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $success_message . "</div>";
} ?>


<h3 class="title"><?php echo $page_title; ?></h3>
<p><?php echo $this->lang->line("enter_info"); ?></p>

<?php $attrib = array('class' => 'form-horizontal', 'id' => 'addCountAbc', "method" => "post");
echo form_open("module=inventories&view=add_quantity", $attrib);
?>

<div class="control-group">
    <label class="control-label" for="product_name">Product Name</label>

    <div
        class="controls"> <?php echo form_input('product_name', (isset($_POST['product_name']) ? $_POST['product_name'] : $product_name), 'class="span4 tip" readonly = "readonly" id="product_name" required="required" data-error="' . $this->lang->line("product_name") . ' ' . $this->lang->line("is_required") . '"'); ?> </div>
</div>

<div class="control-group">
    <label class="control-label" for="current_quantity">UM</label>

    <div
        class="controls"> <?php echo form_input('um', (isset($_POST['um']) ? $_POST['um'] : $um), 'class="span4 tip"  readonly = "readonly" id="um" required="required" data-error="' . $this->lang->line("um") . ' ' . $this->lang->line("is_required") . '"'); ?> </div>
</div>

<div class="control-group">
    <label class="control-label" for="count_quantity">Count Quantity</label>

    <div
        class="controls"> <?php echo form_input('count_quantity', (isset($_POST['count_quantity']) ? $_POST['count_quantity'] : $count_quantity), 'class="span4 tip" id="count_quantity" required="required" data-error="' . $this->lang->line("count_quantity") . ' ' . $this->lang->line("is_required") . '"'); ?> </div>
</div>

<div class="control-group">
    <label class="control-label" for="warehouse"><?php echo $this->lang->line("warehouse"); ?></label>

    <div class="controls"> <?php
        foreach ($warehouses as $warehouse) {
            $wh[$warehouse->id] = $warehouse->name;
        }

        echo form_dropdown('warehouse', $wh, (isset($_POST['warehouse']) ? $_POST['warehouse'] : ""), 'class="form-control input-sm" name="warehouse" id="warehouse"'); ?> </div>
</div>

<input type="hidden" name="product_code" id="product_code" value=""/>
<div class="control-group">
    <div
        class="controls"><?php echo form_submit('submit', $this->lang->line("submit"), 'class="btn btn-primary" style="padding: 6px 15px;"'); ?></div>
</div>
<?php echo form_close(); ?>
<div class="control-group">
    <div class="controls">
        <div class="span4" id="drag">
            <div class="add_options clearfix" id="add_options">
                <div id="draggable"><?php echo $this->lang->line('draggable'); ?></div>
                <div class="fancy-tab-container">
                    <ul class="nav nav-tabs three-tabs fancy" id="byTab">
                        <li class="active"><a href="#by_code"
                                              id="select_by_code"><?php echo $this->lang->line("barcode_scanner"); ?></a>
                        </li>
                        <li><a href="#by_codes"
                               id="select_by_codes"><?php echo $this->lang->line("product_code"); ?></a></li>
                        <li><a href="#by_name" id="select_by_name"><?php echo $this->lang->line("product_name"); ?></a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane tab-bg"
                             id="by_code"> <?php echo form_input('code', '', 'class="input-block-level ttip" id="code" data-placement="top" data-trigger="focus" placeholder="' . $this->lang->line("barcode_scanner") . '" title="' . $this->lang->line("use_barcode_scanner_tip") . '"'); ?> </div>
                        <div class="tab-pane tab-bg"
                             id="by_codes"> <?php echo form_input('codes', '', 'class="input-block-level ttip" id="codes" data-placement="top" data-trigger="focus" placeholder="' . $this->lang->line("product_code") . '" title="' . $this->lang->line("au_pr_name_tip") . '"'); ?> </div>
                        <div class="tab-pane tab-bg active"
                             id="by_name"> <?php echo form_input('name', '', 'class="input-block-level ttip" id="name" data-placement="top" data-trigger="focus" placeholder="' . $this->lang->line("product_name") . '" title="' . $this->lang->line("au_pr_name_tip") . '"'); ?> </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="clearfix"></div>


<script>

    function getSubtottal(id) {
        var value = document.getElementById('qnt_' + id).value;
        var unit_cost = document.getElementById('unit_cost' + id).value;
        var total = parseFloat(value) * parseFloat(unit_cost);
        document.getElementById('sub_' + id).innerHTML = total.toFixed(2);

    }
</script>

