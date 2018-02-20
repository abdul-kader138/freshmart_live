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
   
    	<span>Print Date : </span><span><?php echo date('d/m/y'); ?></span>
    </div>
</div>
 <h3 style="text-align:center">Purchase Order</h3>
	<table class="table table-bordered table-hover table-striped" style="margin-bottom: 5px;">
	<tr><td align="left">
	    <div><span>Supplier: </span><span><?php echo $supplier->name; ?></span></div>
	<div><span>Address: </span><span><?php echo $supplier->address; ?></span></div>
	    
	    </td><td  align="right">
	       <!--  <div><span>P.O No: </span><span><?php  //echo $inv->purchase_id; ?></span></div> -->

	<div><span>Ref : </span><span><?php echo $inv->reference_no; ?></span></div>

	        
	        
	    </td></tr>
	</table>
<p>&nbsp;</p> 
	<table class="table table-bordered table-hover table-striped" style="margin-bottom: 5px;">

	<thead> 

	<tr> 
    	<th><?php echo $this->lang->line("no"); ?></th> 
	    <th><?php echo $this->lang->line("description"); ?> (<?php echo $this->lang->line("code"); ?>)</th> 
        <th><?php echo $this->lang->line("quantity"); ?></th>
	    <th style="padding-right:20px;"><?php echo $this->lang->line("unit_price"); ?></th> 
        <?php if(TAX1) { echo '<th style="padding-right:20px; text-align:center; vertical-align:middle;">'.$this->lang->line("tax").'</th>'; } ?>
	    <th style="padding-right:20px;"><?php echo $this->lang->line("subtotal"); ?></th> 
	</tr> 

	</thead> 

	<tbody> 
	
	<?php $r = 1; foreach ($rows as $row):?>
			<tr>
            	<td style="text-align:center; width:40px; vertical-align:middle;"><?php echo $r; ?></td>
                <td style="vertical-align:middle;"><?php echo $row->product_name." (".$row->product_code.")"; ?></td>
                <td style="width: 100px; text-align:center; vertical-align:middle;"><?php echo $row->quantity; ?></td>
                <td style="text-align:right; width:100px; padding-right:10px;"><?php echo $this->ion_auth->formatMoney($row->unit_price); ?></td>
                <?php if(TAX1) { echo '<td style="width: 80px; text-align:right; vertical-align:middle;"><!--<small>('.$row->tax.')</small>--> '.$row->val_tax.'</td>'; } ?>
                <td style="text-align:right; width:100px; padding-right:10px;"><?php echo $this->ion_auth->formatMoney($row->gross_total); ?></td> 
			</tr> 
    <?php 
		$r++; 
		endforeach;
	?>
    <?php $col = 4; if(TAX1) { $col += 1; } ?>
    
<?php if(TAX1) { ?>
<tr><td colspan="<?php echo $col; ?>" style="text-align:right; padding-right:10px;"><?php echo $this->lang->line("total"); ?> (<?php echo CURRENCY_PREFIX; ?>)</td><td style="text-align:right; padding-right:10px;"><?php echo $this->ion_auth->formatMoney($inv->inv_total); ?></td></tr>
<?php echo '<tr><td colspan="'.$col.'" style="text-align:right; padding-right:10px;;">'.$this->lang->line("product_tax").' ('. CURRENCY_PREFIX.')</td><td style="text-align:right; padding-right:10px;">'.$this->ion_auth->formatMoney($inv->total_tax).'</td></tr>'; } ?>
<tr><td colspan="<?php echo $col; ?>" style="text-align:right; padding-right:10px; font-weight:bold;"><?php echo $this->lang->line("total_amount"); ?> (<?php echo CURRENCY_PREFIX; ?>)</td><td style="text-align:right; padding-right:10px; font-weight:bold;"><?php echo $this->ion_auth->formatMoney($inv->total); ?></td></tr>

	</tbody> 

	</table> 

<div style="clear: both;"></div>
<p>&nbsp;</p>
<div style="clear: both;"></div>


<div style="clear: both;"></div>

<div class="row-fluid"> 
<div class="span12"> 
<p>&nbsp;</p>
	<p><span style="font-weight:bold; font-size:14px; margin-bottom:5px;">Terms & Conditions:</span></p>
	<table class="table">
	    <tr><td>1</td><td>Terms of Delivery:</td><td>3 days from date to issue of purchase order</td></tr>
	    <tr><td>2</td><td>Quality & Quantity:</td><td>Quality & Quantity of the materials must be approved & certify by the Assigned Authority</td></tr>
	    <tr><td>3</td><td>Important Clause: </td><td>Buyer reserves the right to change/cancle the part/full order without assigning any reason</td></tr>
	    <tr><td>4</td><td>Terms of Payment:</td><td>Final Payment will be made after 30 Days of the completion of full delivery and submission of authorized chalan and bill</td></tr>
	    <tr><td>5</td><td>Weight, Transport Cost & Reject goods:</td><td>Net weight will be finalized at factory, Transport cost will be paid by company. Suppliers need to recieve the goods back if rejected at thire own expenses.</td></tr>
	    <tr><td>6</td><td>Partial Delivery: </td><td>No</td></tr>
	    <tr><td>7</td><td>Advance Paid: </td><td>&nbsp;</td></tr>
	    <tr><td>8</td><td>Other Condition: </td><td>&nbsp;</td></tr>
	</table>

<!-- 
    <?php if($inv->note || $inv->note != "") { ?>
	<p>&nbsp;</p>
	<p><span style="font-weight:bold; font-size:14px; margin-bottom:5px;"><?php echo $this->lang->line("note"); ?>:</span></p>
	<p><?php echo html_entity_decode($inv->note); ?></p>
	
    <?php } ?>
    -->
</div>
</div>

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
<p style="text-transform: capitalize;"><?php echo $inv->chk_name ? $inv->chk_name : '--'; ?> </p>
<p>&nbsp;</p>
<p style="border-top: 1px solid #000;"><?php echo 'Purchase by'; ?></p>
</div> 
    
</td>

<td style="width:23%; text-align:center">

<div style="float:left; margin:5px 15px"> 
<p>&nbsp;</p>
<p style="text-transform: capitalize;"> <?php echo $inv->verify_name ? $inv->verify_name : '--'; ?> </p>
<p>&nbsp;</p>
<p style="border-top: 1px solid #000;"><?php echo "Verify By"; ?></p>
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