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
    <div class="row-fluid text-center" style="margin-bottom:20px;">
        <img src="<?php echo base_url().'assets/img/'.LOGO2; ?>" alt="<?php echo SITE_NAME; ?>">
    </div>
    <div class="row-fluid">


        <div class="span6">

             <h3 class="inv">Purchase Requisition Details</h3>
            <p style="font-weight:bold;"><?php echo $this->lang->line("pr_reference_no"); ?>: <?php echo $inv->reference_no; ?></p>

            <p style="font-weight:bold;"><?php echo "Print Date"; ?>: <?php echo date('d/m/y'); ?></p>
        </div>
        <div style="clear: both;"></div>
    </div>
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

        <?php $grandTotal=0; $taxTotal=0;$r = 1; foreach ($rows as $row):?>
            <tr>
                <td style="text-align:center; width:40px; vertical-align:middle;"><?php echo $r; ?></td>
                <td style="vertical-align:middle;"><?php echo $row->product_name." (".$row->product_code.")"; ?></td>
                <td style="width: 100px; text-align:center; vertical-align:middle;"><?php echo $row->quantity; ?></td>
                <td style="text-align:right; width:100px; padding-right:10px;"><?php echo $this->ion_auth->formatMoney($row->unit_price); ?></td>
                <?php if(TAX1) { echo '<td style="width: 80px; text-align:right; vertical-align:middle;"><!--<small>('.$row->tax.')</small>--> '.$row->val_tax.'</td>'; } ?>
                <td style="text-align:right; width:100px; padding-right:10px;"><?php echo $this->ion_auth->formatMoney(($row->unit_price*$row->quantity)); ?></td>
            </tr>
            <?php
            $r++;
            $grandTotal=($grandTotal+($row->unit_price*$row->quantity));
            $taxTotal=($taxTotal+$row->val_tax);
        endforeach;
        ?>
        <?php $col = 4; if(TAX1) { $col += 1; } ?>

        <?php if(TAX1) { ?>
            <tr><td colspan="<?php echo $col; ?>" style="text-align:right; padding-right:10px;"><?php echo $this->lang->line("total"); ?> (<?php echo CURRENCY_PREFIX; ?>)</td><td style="text-align:right; padding-right:10px;"><?php echo $grandTotal; ?></td></tr>
            <?php echo '<tr><td colspan="'.$col.'" style="text-align:right; padding-right:10px;;">'.$this->lang->line("product_tax").' ('. CURRENCY_PREFIX.')</td><td style="text-align:right; padding-right:10px;">'.$taxTotal.'</td></tr>'; } ?>
        <tr><td colspan="<?php echo $col; ?>" style="text-align:right; padding-right:10px; font-weight:bold;"><?php echo $this->lang->line("total_amount"); ?> (<?php echo CURRENCY_PREFIX; ?>)</td><td style="text-align:right; padding-right:10px; font-weight:bold;"><?php echo ($grandTotal+$taxTotal); ?></td></tr>

        </tbody>

    </table>
    <div style="clear: both;"></div>


</div>
</body>
</html>