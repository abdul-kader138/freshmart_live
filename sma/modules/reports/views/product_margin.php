<?php
if($this->input->post('submit')) {

    $v = "";

    if($this->input->post('warehouse')){
        $v .= "&warehouse=".$this->input->post('warehouse');
    }
    if($this->input->post('category')){
        $v .= "&category=".$this->input->post('category');
    }

        ?>
    <script type="application/javascript">
        $(document).ready(function(){
            $("#whName").text();
            $("#whName").text("Warehouse - "+$("#warehouse option:selected").text());
        })

    </script>
<?php
}
?>
<script src="<?php echo base_url(); ?>assets/media/js/jquery.dataTables.columnFilter.js" type="text/javascript"></script>
<style type="text/css">
    .text_filter { width: 100% !important; font-weight: normal !important; border: 0 !important; box-shadow: none !important;  border-radius: 0 !important;  padding:0 !important; margin:0 !important; font-size: 1em !important;}
    .select_filter { width: 100% !important; padding:0 !important; height: auto !important; margin:0 !important;}
    .table td { width: 12.5%; display: table-cell; }
    .table th { text-align: center; }
    .table td:nth-child(5) { font-size:90%; }
    .table td:nth-child(6), .table tfoot th:nth-child(6), .table td:nth-child(7), .table tfoot th:nth-child(7), .table td:nth-child(8), .table tfoot th:nth-child(8) { text-align:right; }
</style>
<link href="<?php echo $this->config->base_url(); ?>assets/css/datepicker.css" rel="stylesheet">
<script src="<?php echo $this->config->base_url(); ?>assets/js/query-ui.js"></script>
<script type="text/javascript">
    $(document).ready(function(){

        <?php if(!isset($_POST['submit'])) { echo '$( "#end_date" ).datepicker("setDate", new Date());'; } ?>
        <?php if($this->input->post('submit')) { echo "$('.form').hide();"; } ?>
        $(".toggle_form").slideDown('slow');

        $('.toggle_form').click(function(){
            $(".form").slideToggle();
            return false;
        });

    });
</script>
<script>
    $(document).ready(function() {

        $('#fileData').dataTable( {
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "aaSorting": [[ 1, "desc" ]],
            "iDisplayLength": <?php echo ROWS_PER_PAGE; ?>,
            'bProcessing'    : true,
            'bServerSide'    : true,
            'sAjaxSource'    : '<?php echo base_url(); ?>index.php?module=reports&view=geProductMarginByCategory<?php
					if($this->input->post('submit')) { echo $v; } ?>',
            'fnServerData': function(sSource, aoData, fnCallback, fnFooterCallback)
            {
                aoData.push( { "name": "<?php echo $this->security->get_csrf_token_name(); ?>", "value": "<?php echo $this->security->get_csrf_hash() ?>" } );
                $.ajax
                ({
                    'dataType': 'json',
                    'type'    : 'POST',
                    'url'     : sSource,
                    'data'    : aoData,
                    'success' : fnCallback
                });
            },
            "oTableTools": {
                "sSwfPath": "assets/media/swf/copy_csv_xls_pdf.swf",
                "aButtons": [
                    "csv",
                    "xls",
                    {
                        "sExtends": "pdf",
                        "sPdfOrientation": "landscape",
                        "sPdfMessage": ""
                    },
                    "print"
                ]
            },
            "aoColumns": [
                null,  null, null, null,null,null,null,null
            ],

            "fnFooterCallback": function ( nRow, aaData, iStart, iEnd, aiDisplay ) {
            }

        } ).columnFilter({ aoColumns: [

            { type: "text", bRegex:true },
            { type: "text", bRegex:true },
            { type: "text", bRegex:true },
            { type: "text", bRegex:true },
            { type: "text", bRegex:true },
            { type: "text", bRegex:true },
            { type: "text", bRegex:true },
            null
        ]});

    } );

</script>

<link href="<?php echo $this->config->base_url(); ?>assets/css/datepicker.css" rel="stylesheet">

<h3><?php echo $page_title; ?>&nbsp; &nbsp;&nbsp;<a href="#" class="btn btn-default btn-sm toggle_form"><?php echo $this->lang->line("show_hide"); ?></a>
    <br/>
    <div id="whName"></div>
</h3>

<div class="form">
    <p>Please customise the report below.</p>
    <?php $attrib = array('class' => 'form-horizontal'); echo form_open("module=reports&view=product_margin", $attrib); ?>

    <div class="control-group">
        <label class="control-label" for="warehouse"><?php echo $this->lang->line("warehouse"); ?></label>
        <div class="controls"> <?php
            //	   		$wh[""] = "";
            foreach($warehouses as $warehouse){
                $wh[$warehouse->id] = $warehouse->name;
            }
            echo form_dropdown('warehouse', $wh, (isset($_POST['warehouse']) ? $_POST['warehouse'] : ""), 'class="span4" id="warehouse" data-placeholder="'.$this->lang->line("select")." ".$this->lang->line("warehouse").'"');  ?> </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="category"><?php echo $this->lang->line("category"); ?></label>
        <div class="controls"> <?php
            $ct[""] = $this->lang->line("select")." ".$this->lang->line("category");
            foreach($categories as $category){
                $ct[$category->id] = $category->name;
            }
            echo form_dropdown('category', $ct, (isset($_POST['category']) ? $_POST['category'] : ""), 'id="category"  data-error="'.$this->lang->line("main_category").' '.$this->lang->line("is_required").'"');  ?> </div>
    </div>

    <div class="control-group">
        <div class="controls"> <?php echo form_submit('submit', $this->lang->line("submit"), 'class="btn btn-primary"');?> </div>
    </div>
    <?php echo form_close();?>

</div>
<div class="clearfix"></div>


<?php if($this->input->post('submit')) { ?>

    <table id="fileData" class="table table-bordered table-hover table-striped table-condensed" style="margin-bottom: 5px;">
        <thead>
        <tr>
            <th>Category</th>
            <th>Code</th>
            <th>Name</th>
            <th>Quantity</th>
            <th width="5px">UM</th>
            <th>Price</th>
            <th>Cost</th>
            <th>Margin(Price)</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td colspan="8" class="dataTables_empty">Loading data from server</td>
        </tr>

        </tbody>
        <tfoot>

        <tr>
        </tr>
        </tfoot>
    </table>

<?php } ?>
<p>&nbsp;</p>

