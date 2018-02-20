<script src="<?php echo base_url(); ?>assets/media/js/jquery.dataTables.columnFilter.js" type="text/javascript"></script>
<style type="text/css">
.text_filter { width: 100% !important; font-weight: normal !important; border: 0 !important; box-shadow: none !important;  border-radius: 0 !important;  padding:0 !important; margin:0 !important; font-size: 1em !important;}
.select_filter { width: 100% !important; padding:0 !important; height: auto !important; margin:0 !important;}
</style>
<script>
             $(document).ready(function() {
                $('#prData').dataTable( {
					"aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                    "aaSorting": [[ 0, "desc" ]],
                    "iDisplayLength": <?php echo ROWS_PER_PAGE; ?>,
                    <?php if(BSTATESAVE) { echo '"bStateSave": true,'; } ?>
			'bProcessing'    : true,
			'bServerSide'    : true,
			<?php $no_cost = array('salesman', 'viewer'); 
                            if (!$this->ion_auth->in_group($no_cost)) { 
			?>
                    'sAjaxSource'    : '<?php echo base_url(); ?>index.php?module=products&view=getdatatableajaxadjustment',
                        <?php } else { ?>
                    'sAjaxSource'    : '<?php echo base_url(); ?>index.php?module=products&view=getdatatableajaxadjustment',
                        <?php } ?>
					'fnServerData': function(sSource, aoData, fnCallback)
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
								{
									"sExtends": "csv",
									"sFileName": "<?php echo $this->lang->line("products"); ?>.csv",
                   		 			"mColumns": [ 0, 1, 2, 3]
								},
								{
									"sExtends": "pdf",
									"sFileName": "<?php echo $this->lang->line("products"); ?>.pdf",
									"sPdfOrientation": "landscape",
                   		 			"mColumns": [ 0, 1, 2, 3]
								},
								"print"
						]
					},
					"aoColumns": [
					  null, null, null, null,
					  { "bSortable": false }
					]
					
                } ).columnFilter({ aoColumns: [

						{ type: "text", bRegex:true },
						{ type: "text", bRegex:true },
						{ type: "text", bRegex:true },
						{ type: "text", bRegex:true },
						
						null
                     ]});
			
            });
                    
</script>
        
<?php if($message) { echo "<div class=\"alert alert-error\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $message . "</div>"; } ?>
<?php if($success_message) { echo "<div class=\"alert alert-success\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $success_message . "</div>"; } ?>
<div class="btn-group pull-right" style="margin-left: 25px;">
 <a class="btn btn-primary dropdown-toggle" data-toggle="dropdown" href="#">
<?php echo $this->lang->line("all_warehouses"); ?>
<span class="caret"></span>
</a>
<ul class="dropdown-menu">
    <?php
	foreach($warehouses as $warehouse) {
		echo "<li><a href='index.php?module=products&view=warehouse&warehouse_id=".$warehouse->id."'>".$warehouse->name."</a></li>";	
	}
	?>
    </ul>
    </div>
<h3 class="title"><?php echo $page_title; ?></h3>

	<p class="introtext"><?php echo $this->lang->line("list_results"); ?></p>
    
	<table id="prData" class="table table-bordered table-hover table-striped table-condensed" style="margin-bottom: 5px;">
		<thead>
        <tr>
			<th><?php echo $this->lang->line("product_code"); ?></th>
            <th><?php echo $this->lang->line("product_name"); ?></th>
            <th><?php echo $this->lang->line("quantity"); ?></th>
            <th>Adjustment Quantity</th>
            <th style="min-width:115px; text-align:center;"><?php echo $this->lang->line("actions"); ?></th>
		</tr>
        </thead>
		<tbody>
	
			<tr>
            	<td colspan="9" class="dataTables_empty">Loading data from server</td>
			</tr>

        </tbody>
        
        <tfoot>
        <tr>
			<th><?php echo $this->lang->line("product_code"); ?></th>
            <th><?php echo $this->lang->line("product_name"); ?></th>
            <th style="min-width:50px;"><?php echo $this->lang->line("quantity"); ?></th>
            <th>Adjustment <br/>Quantity</th>
            <th style="min-width:115px; text-align:center;"><?php echo $this->lang->line("actions"); ?></th>
		</tr>
        </tfoot>
	</table>
<div id="picModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="picModalLabel" aria-hidden="true">
 <div class="modal-header">
<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
<h3 id="myModalLabel"></h3>
</div>
<div class="modal-body" style="text-align:center; height:200px;">
<img id="product_image" src="" style="height:100%;" />
</div>
<div class="modal-footer">
<button class="btn btn-danger" data-dismiss="modal" aria-hidden="true">Close</button>
</div>
</div>