<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title><?php echo $page_title." ".$this->lang->line("no")." ".$inv->id; ?></title>
<link rel="shortcut icon" href="<?php echo $this->config->base_url(); ?>assets/img/favicon.ico">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="<?php echo $this->config->base_url(); ?>assets/css/<?php echo THEME; ?>.css" rel="stylesheet">
<script type="text/javascript" src="<?php echo $this->config->base_url(); ?>assets/js/jquery.js"></script>
<style type="text/css">
html, body { height: 100%; /* font-family: "Segoe UI", Candara, "Bitstream Vera Sans", "DejaVu Sans", "Bitstream Vera Sans", "Trebuchet MS", Verdana, "Verdana Ref", sans-serif; */ }
#wrap { padding: 20px; }
.table th { text-align:center; }
</style>
</head>

<body>
<div id="wrap">
<div class="row-fluid" style="margin-bottom:10px;">
    <div class="span6">
<img src="<?php echo base_url().'assets/img/'.LOGO2; ?>" alt="<?php echo SITE_NAME; ?>" width="100">
</div>
<div class="span6" style="text-align:right">
   
    	<span>Print Date : </span><span><?php echo date(PHP_DATE, strtotime(date('m/d/y'))); ?></span>
    </div>
</div>
 <h3 style="text-align:center">MRN</h3>
	<table class="table table-bordered table-hover table-striped" style="margin-bottom: 5px;">
	<tr><td align="left">
	    <div><span>Supplier: </span><span><?php echo $supplier->name; ?></span></div>
	<div><span>Address: </span><span><?php echo $supplier->address; ?></span></div>
	    
	    </td><td  align="right">
	        <!-- <div><span>P.O No: </span><span><?php  //echo $inv->purchase_id; ?></span></div>-->

	<div><span>MRN Ref : </span><span><?php echo $inv->mr_reference_no; ?></span></div>

	        
	        
	    </td></tr>
	</table>
<p>&nbsp;</p> 
	<table class="table table-bordered table-hover table-striped" style="margin-bottom: 5px;">

	<thead> 

	<tr> 
    	<th><?php echo $this->lang->line("no"); ?></th> 
	    <th><?php echo $this->lang->line("description"); ?> (<?php echo $this->lang->line("code"); ?>)</th> 
        <th><?php echo $this->lang->line("pquantity"); ?></th>
        <th><?php echo $this->lang->line("rquantity"); ?></th>
	    <th style="padding-right:20px;"><?php echo $this->lang->line("unit_price"); ?></th>
        <?php if(TAX1) { echo '<th style="padding-right:20px; text-align:center; vertical-align:middle;">'.$this->lang->line("tax").'</th>'; } ?>
	    <th style="padding-right:20px;"><?php echo $this->lang->line("subtotal"); ?></th> 
	</tr> 

	</thead> 

	<tbody> 
	
	<?php $grandTotal=0; $taxTotal=0;$r = 1; foreach ($rows as $row):
//	     if($row->mr_item_status){
	   ?>
			<tr>
            	<td style="text-align:center; width:40px; vertical-align:middle;"><?php echo $r; ?></td>
                <td style="vertical-align:middle;"><?php echo $row->purchase_item_name." (".$row->purchase_item_code.")"; ?></td>
                <td style="width: 100px; text-align:center; vertical-align:middle;"><?php echo $row->po_qty; ?></td>
                <td style="width: 100px; text-align:center; vertical-align:middle;"><?php echo $row->received_qty; ?></td>
                <td style="text-align:right; width:100px; padding-right:10px;"><?php echo $this->ion_auth->formatMoney($row->price); ?></td>
                <?php if(TAX1) { echo '<td style="width: 80px; text-align:right; vertical-align:middle;"><!--<small>('.$row->tax.')</small>--> '.$row->val_tax.'</td>'; } ?>
                <td style="text-align:right; width:100px; padding-right:10px;"><?php echo $this->ion_auth->formatMoney($row->price*$row->received_qty); ?></td>
			</tr> 
    <?php 
		$r++;
             $grandTotal=($grandTotal+($row->price*$row->received_qty));
             $taxTotal=($taxTotal+$row->val_tax);
//	     }
		endforeach;
	?>
    <?php $col = 5; if(TAX1) { $col += 1; } ?>
    
<?php if(TAX1) { ?>
<tr><td colspan="<?php echo $col; ?>" style="text-align:right; padding-right:10px;"><?php echo $this->lang->line("total"); ?> (<?php echo CURRENCY_PREFIX; ?>)</td><td style="text-align:right; padding-right:10px;"><?php echo $grandTotal; ?></td></tr>
<?php echo '<tr><td colspan="'.$col.'" style="text-align:right; padding-right:10px;;">'.$this->lang->line("product_tax").' ('. CURRENCY_PREFIX.')</td><td style="text-align:right; padding-right:10px;">'.$taxTotal.'</td></tr>'; } ?>
<tr><td colspan="<?php echo $col; ?>" style="text-align:right; padding-right:10px; font-weight:bold;"><?php echo $this->lang->line("total_amount"); ?> (<?php echo CURRENCY_PREFIX; ?>)</td><td style="text-align:right; padding-right:10px; font-weight:bold;"><?php echo($grandTotal+$taxTotal); ?></td></tr>

	</tbody> 

	</table> 

<div style="clear: both;"></div>
<p>&nbsp;</p>
<div style="clear: both;"></div>


<div style="clear: both;"></div>

<p>&nbsp;</p>
<table width="100%">
    <tr>
        <td style="width:23%; text-align:center">
<div style="float:left; margin:5px 15px"> 
<p>&nbsp;</p>
<p style="text-transform: capitalize;">------------</p>
<p>&nbsp;</p>
<p style="border-top: 1px solid #000;">Supplier</p>
</div>
</td>

<td style="width:23%; text-align:center">
<div style="float:left; margin:5px 15px"> 
<p>&nbsp;</p>
<p style="text-transform: capitalize;"><?php echo $inv->user ? $inv->user : '--'; ?> </p>
<p>&nbsp;</p>
<p style="border-top: 1px solid #000;">Prepare By</p>
</div>
</td>


<td style="width:23%; text-align:center">
    
<div style="float:left; margin:5px 15px"> 
<p>&nbsp;</p>
<p style="text-transform: capitalize;"> <?php echo $inv->app_name ? $inv->app_name : '--'; ?> </p>
<p>&nbsp;</p>
<p style="border-top: 1px solid #000;"><?php echo "Approve By"; ?></p>
</div>
    
</td>

</tr>
</table>

</div>
</body>
</html>