<?php
if($this->input->post('submit')) {

    $v = "";
    /*if($this->input->post('name')){
         $v .= "&name=".$this->input->post('name');
     }*/
    if($this->input->post('reference_no')){
        $v .= "&reference_no=".$this->input->post('reference_no');
    }
    if($this->input->post('customer')){
        $v .= "&customer=".$this->input->post('customer');
    }
    if($this->input->post('biller')){
        $v .= "&biller=".$this->input->post('biller');
    }
    if($this->input->post('warehouse')){
        $v .= "&warehouse=".$this->input->post('warehouse');
    }
    if($this->input->post('paid_by')){
        $v .= "&paid_by=".$this->input->post('paid_by');
    }
    if($this->input->post('user')){
        $v .= "&user=".$this->input->post('user');
    }
    if($this->input->post('start_date')){
        $v .= "&start_date=".$this->input->post('start_date');
    }
    if($this->input->post('end_date')) {
        $v .= "&end_date=".$this->input->post('end_date');
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
        $( "#start_date" ).datepicker({
            format: "<?php echo JS_DATE; ?>",
            autoclose: true
        });

        $( "#end_date" ).datepicker({
            format: "<?php echo JS_DATE; ?>",
            autoclose: true
        });
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
            'sAjaxSource'    : '<?php echo base_url(); ?>index.php?module=reports&view=getSalesByCustomer<?php
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
                null,  null, null, null,{ "mRender": currencyFormate }, { "mRender": currencyFormate }, { "mRender": currencyFormate }, { "mRender": currencyFormate },{ "mRender": currencyFormate },{ "mRender": currencyFormate }
            ],

            "fnFooterCallback": function ( nRow, aaData, iStart, iEnd, aiDisplay ) {
                console.log(aaData);
                var row_total = 0.0; var tax_total =0; var tax2_total = 0; var return_total=0.0;var discount_total=0.0;var gross_total=0.0; var qty_total=0.0;
                for ( var i=0 ; i<aaData.length ; i++ )
                {
                    if(aaData[ aiDisplay[i] ][4] !=null || aaData[ aiDisplay[i] ][4] != undefined)qty_total += parseFloat(aaData[ aiDisplay[i] ][4]);
                    if(aaData[ aiDisplay[i] ][6] !=null || aaData[ aiDisplay[i] ][6] != undefined) row_total += parseFloat(aaData[ aiDisplay[i] ][6]);
                    if(aaData[ aiDisplay[i] ][7] !=null || aaData[ aiDisplay[i] ][7] != undefined) discount_total += parseFloat(aaData[ aiDisplay[i] ][7]);
                    if(aaData[ aiDisplay[i] ][8] !=null || aaData[ aiDisplay[i] ][8] != undefined) return_total += parseFloat(aaData[ aiDisplay[i] ][8]);
                    if(aaData[ aiDisplay[i] ][9] !=null || aaData[ aiDisplay[i] ][9] != undefined) gross_total += parseFloat(aaData[ aiDisplay[i] ][9]);
                }

                var nCells = nRow.getElementsByTagName('th');
                nCells[4].innerHTML = currencyFormate(parseFloat(qty_total).toFixed(2));
                nCells[6].innerHTML = currencyFormate(parseFloat(row_total).toFixed(2));
                nCells[7].innerHTML = currencyFormate(parseFloat(discount_total).toFixed(2));
                nCells[8].innerHTML = currencyFormate(parseFloat(return_total).toFixed(2));
                nCells[9].innerHTML = currencyFormate(parseFloat(gross_total).toFixed(2));
            }

        } ).columnFilter({ aoColumns: [

            { type: "text", bRegex:true },
            { type: "text", bRegex:true },
            { type: "text", bRegex:true },
            null,null,null
        ]});

    } );

</script>

<link href="<?php echo $this->config->base_url(); ?>assets/css/datepicker.css" rel="stylesheet">

<h3><?php echo $page_title; ?> <?php if ($this->input->post('start_date')) {
echo" # ". $this->input->post('start_date') . " - " . $this->input->post('end_date');}
    
  ?>&nbsp; &nbsp;&nbsp;<a href="#" class="btn btn-default btn-sm toggle_form"><?php echo $this->lang->line("show_hide"); ?></a>
    <br/>
    <div id="whName"></div>
</h3>

<div class="form">
    <p>Please customise the report below.</p>
    <?php $attrib = array('class' => 'form-horizontal'); echo form_open("module=reports&view=customer_sales", $attrib); ?>
    <div class="control-group">
        <label class="control-label" for="reference_no"><?php echo $this->lang->line("reference_no"); ?></label>
        <div class="controls"> <?php echo form_input('reference_no', (isset($_POST['reference_no']) ? $_POST['reference_no'] : ""), 'class="span4 tip" title="Filter Sales by Reference No" id="reference_no"');?>
        </div>
    </div>
    <!--<div class="control-group">
  <label class="control-label" for="name"><?php echo $this->lang->line("product_name"); ?></label>
  <div class="controls"> <?php echo form_input('name', (isset($_POST['name']) ? $_POST['name'] : ""), 'class="span4" id="name"');?>
  </div>
</div>-->
    <div class="control-group">
        <label class="control-label" for="customer"><?php echo $this->lang->line("customer"); ?></label>
        <div class="controls"> <?php
            $cu[""] = "";
            foreach($customers as $customer){
                $cu[$customer->id] = $customer->name;
            }
            echo form_dropdown('customer', $cu, (isset($_POST['customer']) ? $_POST['customer'] : ""), 'class="span4" id="customer" data-placeholder="'.$this->lang->line("select")." ".$this->lang->line("customer").'"');  ?> </div>
    </div>
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
        <label class="control-label" for="start_date"><?php echo $this->lang->line("start_date"); ?></label>
        <div class="controls"> <?php echo form_input('start_date', (isset($_POST['start_date']) ? $_POST['start_date'] : ""), 'class="span4" id="start_date"');?> </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="end_date"><?php echo $this->lang->line("end_date"); ?></label>
        <div class="controls"> <?php echo form_input('end_date', (isset($_POST['end_date']) ? $_POST['end_date'] : ""), 'class="span4" id="end_date"');?> </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="end_date">Paid By</label>
        <div class="controls">
            <select name="paid_by" id="paid_by">
                <option value="">Select Payment Mode</option>
                <option value="cash" <?php if($_POST['paid_by']=='cash') echo "selected"; ?>>Cash</option>
                <option value="CC" <?php if($_POST['paid_by']=='CC') echo "selected"; ?>>Cards</option>
                <option value="CC_cash" <?php if($_POST['paid_by']=='CC_cash') echo "selected"; ?>>Card & Cash</option>
                <option value="Cheque" <?php if($_POST['paid_by']=='Cheque') echo "selected"; ?>><?php echo $this->lang->line("cheque"); ?></option>
                <option value="Credit" <?php if($_POST['paid_by']=='Credit') echo "selected"; ?>>Credit</option>
            </select>
        </div>
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
            <th><?php echo $this->lang->line("customer"); ?></th>
            <th>Code</th>
            <th>Name</th>
            <th>UM</th>
            <th><?php echo $this->lang->line("product_qty"); ?></th>
            <th>Price</th>
            <th>Total</th>
            <th>Discount</th>
            <th>Return</th>
            <th>Gross Total</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td colspan="6" class="dataTables_empty">Loading data from server</td>
        </tr>

        </tbody>
        <tfoot>

        <tr>
            <th><?php echo $this->lang->line("customer"); ?></th>
            <th>Code</th>
            <th>Name</th>
            <th>UM</th>
            <th><?php echo $this->lang->line("product_qty"); ?></th>
            <th>Price</th>
            <th>Total</th>
            <th>Discount</th>
            <th>Return</th>
            <th>Gross Total</th>
        </tr>
        </tfoot>
    </table>

<?php } ?>
<p>&nbsp;</p>

