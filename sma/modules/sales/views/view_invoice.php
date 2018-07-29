<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo $page_title." ".$this->lang->line("no")." ".$inv->id; ?></title>
    <link rel="shortcut icon" href="<?php echo $this->config->base_url(); ?>assets/img/favicon.ico">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="<?php echo $this->config->base_url(); ?>assets/css/<?php echo THEME; ?>.css" rel="stylesheet">
    <script type="text/javascript" src="<?php echo $this->config->base_url(); ?>assets/js/jquery.js"></script>
</head>

<body>

<img src="<?php echo $this->config->base_url(); ?>assets/img/<?php echo $inv->status; ?>.png"  alt="<?php echo $inv->status; ?>" style="float: right; position: absolute; top:0; right: 0;"/>
<div id="wrap">
    <img src="<?php echo $this->config->base_url(); ?>assets/uploads/logos/<?php echo $biller->logo; ?>" alt="SITE_NAME" />
    <div class="row-fluid">



        <div class="span6">
            <h5><?php echo $this->lang->line("billed_to"); ?>:<h5>
                    <h4><?php echo $customer->name; ?></h4>
                    <p>
                        <?php if($customer->address != "-") { echo  $this->lang->line("address").": ".$customer->address.", ".$customer->city.", ".$customer->postal_code; } ?>
                        <br> <?php echo $this->lang->line("tel").": ".$customer->phone; ?>
                        <br><?php echo $this->lang->line("email").": ".$customer->email; ?></p>
        </div>
    </div>
    <div style="clear: both;"></div>
    <p>&nbsp;</p>
    <div class="row-fluid">
        <div class="span6">

            <p style="font-weight:bold;"><?php echo $this->lang->line("reference_no"); ?>: <?php echo $inv->reference_no; ?></p>

            <p style="font-weight:bold;"><?php echo $this->lang->line("date"); ?>: <?php echo date(PHP_DATE, strtotime($inv->date)); ?></p>
            <p>&nbsp;</p>
        </div>
        <div style="clear: both;"></div>
    </div>

    <table class="table table-bordered table-hover table-striped" style="margin-bottom: 5px;">

        <thead>

        <tr>

            <th style="text-align:center; vertical-align:middle;"><?php echo $this->lang->line("no"); ?></th>
            <th style="vertical-align:middle;"><?php echo $this->lang->line("description"); ?></th>
            <?php if(PRODUCT_SERIAL) { echo '<th style="text-align:center; vertical-align:middle;">'.$this->lang->line("serial_no").'</th>'; } ?>

            <!--<?php if(TAX1) { echo '<th style="text-align:center; vertical-align:middle;">'.$this->lang->line("tax").'</th>'; } ?>-->

            <th style="text-align:center; vertical-align:middle;"><?php echo $this->lang->line("quantity"); ?></th>
            <th style="padding-right:20px; text-align:center; vertical-align:middle;"><?php echo $this->lang->line("unit_price"); ?></th>
            <?php if(DISCOUNT_OPTION == 2) { echo '<th style="text-align:center; vertical-align:middle;">'.$this->lang->line("discount").'</th>'; } ?>
            <?php if(TAX1) { echo '<th style="padding-right:20px; text-align:center; vertical-align:middle;">'.$this->lang->line("tax").'</th>'; } ?>
            <th style="padding-right:20px; text-align:center; vertical-align:middle;"><?php echo $this->lang->line("subtotal"); ?></th>
        </tr>

        </thead>

        <tbody>

        <?php $r = 1; foreach ($rows as $row):?>
            <tr>
                <td style="text-align:center; width:40px; vertical-align:middle;"><?php echo $r; ?></td>
                <td style="vertical-align:middle;"><?php echo $row->product_name." (".$row->product_code.")"; ?>
                    <?php if($row->details) { echo '<div>'.html_entity_decode($row->details).'</div>'; } ?></td>

                <?php if(PRODUCT_SERIAL) { echo '<td style="width: 100px; text-align:center; vertical-align:middle;">'.$row->serial_no.'</td>'; } ?>

                <!--<?php if(TAX1) { echo '<td style="width: 70px; text-align:center; vertical-align:middle;">'.$row->tax.'</td>'; } ?>-->

                <td style="width: 70px; text-align:center; vertical-align:middle;"><?php echo $row->quantity; ?></td>
                <td style="width: 80px; text-align:right; padding-right:10px; vertical-align:middle;"><?php echo $this->ion_auth->formatMoney($row->unit_price); ?></td>

                <?php if(DISCOUNT_OPTION == 2) { echo '<td style="width: 80px; text-align:center; vertical-align:middle;">'.$row->discount_val.'</td>'; } ?>
                <?php if(TAX1) { echo '<td style="width: 80px; text-align:right; vertical-align:middle;"><!--<small>('.$row->tax.')</small>--> '.$this->ion_auth->formatMoney($row->val_tax).'</td>'; } ?>

                <td style="width: 100px; text-align:right; padding-right:10px; vertical-align:middle;"><?php echo $this->ion_auth->formatMoney($row->gross_total); ?></td>
            </tr>
            <?php
            $r++;
        endforeach;
        ?>
        <?php $col = 4;  if(PRODUCT_SERIAL) { $col += 1; } if(DISCOUNT_OPTION == 2) { $col += 1; } if(TAX1) { $col += 1; } ?>

        <?php if(TAX1 || TAX2 || DISCOUNT_OPTION == 1 || DISCOUNT_OPTION == 2) { echo '<tr><td colspan="'.$col.'" style="text-align:right; padding-right:10px;;">'.$this->lang->line("total").' ('. CURRENCY_PREFIX.')</td><td style="text-align:right; padding-right:10px;">'.$this->ion_auth->formatMoney($inv->inv_total).'</td></tr>'; } ?>
        <?php if(DISCOUNT_OPTION == 1 || DISCOUNT_OPTION == 2) { echo '<tr><td colspan="'.$col.'" style="text-align:right; padding-right:10px;;">'.$this->lang->line("discount").' ('. CURRENCY_PREFIX.')</td><td style="text-align:right; padding-right:10px;">'.$this->ion_auth->formatMoney($inv->inv_discount).'</td></tr>'; } ?>
        <?php if(TAX1) { echo '<tr><td colspan="'.$col.'" style="text-align:right; padding-right:10px;;">'.$this->lang->line("product_tax").' ('. CURRENCY_PREFIX.')</td><td style="text-align:right; padding-right:10px;">'.$this->ion_auth->formatMoney($inv->total_tax).'</td></tr>'; } ?>
        <?php if(TAX2) { echo '<tr><td colspan="'.$col.'" style="text-align:right; padding-right:10px;;">'.$this->lang->line("invoice_tax").' ('. CURRENCY_PREFIX.')</td><td style="text-align:right; padding-right:10px;">'.$this->ion_auth->formatMoney($inv->total_tax2).'</td></tr>'; } ?>
        <?php if($inv->shipping != 0) { echo '<tr><td colspan="'.$col.'" style="text-align:right; padding-right:10px;;">'.$this->lang->line("shipping").' ('. CURRENCY_PREFIX.')</td><td style="text-align:right; padding-right:10px;">'.$this->ion_auth->formatMoney($inv->shipping).'</td></tr>'; } ?>
        <?php if($inv->return_amount != 0) { echo '<tr><td colspan="'.$col.'" style="text-align:right; padding-right:10px;;">Return Amount ('. CURRENCY_PREFIX.')</td><td style="text-align:right; padding-right:10px;">'.$this->ion_auth->formatMoney($inv->return_amount).'</td></tr>'; } ?>
        <tr><td colspan="<?php echo $col; ?>" style="text-align:right; padding-right:10px; font-weight:bold;"><?php echo $this->lang->line("total_amount"); ?> (<?php echo CURRENCY_PREFIX; ?>)</td><td style="text-align:right; padding-right:10px; font-weight:bold;"><?php echo $this->ion_auth->formatMoney($inv->total+$inv->shipping -$inv->return_amount); ?></td></tr>

        </tbody>

    </table>
    <div style="clear: both;"></div>
    <div class="row-fluid">
        <div class="span12">
            <?php if($inv->note || $inv->note != "") { ?>
                <p>&nbsp;</p>
                <p><span style="font-weight:bold; font-size:14px; margin-bottom:5px;"><?php echo $this->lang->line("note"); ?>:</span></p>
                <p><?php echo html_entity_decode($inv->note); ?></p>

            <?php } ?>
        </div>
    </div>
    <div style="clear: both;"></div>
    <div class="row-fluid">
        <div class="span5">
            <p>&nbsp;</p>
            <p><?php echo $this->lang->line("issued_by"); ?>: <?php echo $inv->user; ?> </p>
            <p>&nbsp;</p>
            <p style="border-bottom: 1px solid #666;">&nbsp;</p>
            <p><?php echo $this->lang->line("signature")." &amp; ".$this->lang->line("stamp"); ; ?></p>
        </div>
    </div>

</div>
</body>
</html>