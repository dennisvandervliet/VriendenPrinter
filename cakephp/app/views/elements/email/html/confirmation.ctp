<style type="text/css">
    body,td { color:#2f2f2f; font:11px/1.35em Verdana, Arial, Helvetica, sans-serif; }
</style>

<div style="font:11px/1.35em Verdana, Arial, Helvetica, sans-serif;">
<table cellspacing="0" cellpadding="0" border="0" width="98%" style="margin-top:10px; font:11px/1.35em Verdana, Arial, Helvetica, sans-serif; margin-bottom:10px;">
<tr>
    <td align="center" valign="top">
        <!-- [ header starts here] -->
        <table cellspacing="0" cellpadding="0" border="0" width="650">
            <tr>
                <td valign="top"><a href="<?= $data['url']?>"><img src="<?= $data['logo']?>" alt="<?= $data['alt_text']?>"  style="margin-bottom:10px;" border="0"/></a></td>
            </tr>
        </table>
        <!-- [ middle starts here] -->
        <table cellspacing="0" cellpadding="0" border="0" width="650">
            <tr>
                <td valign="top">
                    <p>
                        <strong>Beste <?= $data['fullname']?></strong>,<br/>
                 <?= $data['thankyou_text']?></p>
                  <p>Bevestiging</p>

                    <h3 style="border-bottom:2px solid #eee; font-size:1.05em; padding-bottom:1px; ">Bestelling nr: <?= $data['order_number']?> geplaatst op <?= $this->Time->nice($data['order_date'])?></h3>
                    <table cellspacing="0" cellpadding="0" border="0" width="100%">
                        <thead>
                        <tr>
                            <th align="left" width="48.5%" bgcolor="#d9e5ee" style="padding:5px 9px 6px 9px; border:1px solid #bebcb7; border-bottom:none; line-height:1em;">Adresgegevens:</th>
                            <th width="3%"></th>
                            <th align="left" width="48.5%" bgcolor="#d9e5ee" style="padding:5px 9px 6px 9px; border:1px solid #bebcb7; border-bottom:none; line-height:1em;">Betaling en verzedning</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td valign="top" style="padding:7px 9px 9px 9px; border:1px solid #bebcb7; border-top:0; background:#f8f7f5;">
                            	<?= $data['company']?><br>
                                <?= $data['fullname']?><br>
                                <?= $data['street']?> <br>
                                <?= $data['city']?><br>
                            </td>
                            <td>&nbsp;</td>
                            <td valign="top" style="padding:7px 9px 9px 9px; border:1px solid #bebcb7; border-top:0; background:#f8f7f5;">
                                Betaald met iDeal<br>
                                Verzending via TNT
                             
                                
                                </td>
                        </tr>
                        </tbody>
                    </table>
                    <br/>
                      <table cellspacing="0" cellpadding="0" border="0" width="100%">
                        <thead>
                        <tr>
                            <th align="left" width="80%" bgcolor="#d9e5ee" style="padding:5px 9px 6px 9px; border:1px solid #bebcb7; border-bottom:none; line-height:1em;">Bestelling</th>
                              <th align="left" width="20%" bgcolor="#d9e5ee" style="padding:5px 9px 6px 9px; border:1px solid #bebcb7; border-bottom:none; line-height:1em;"></th>
        
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td valign="top" width="80%" style="padding:7px 9px 9px 9px; border:1px solid #bebcb7; border-top:0; background:#f8f7f5;">
                                Vriendenposter 50 cm x 100 cm
                            </td>
                             <td valign="top" width="20%" style="padding:7px 9px 9px 9px; border:1px solid #bebcb7; border-top:0; background:#f8f7f5;">
                               <?= $this->Number->currency($data['subtotal']/100, 'EUR')?>
                            </td>
                                                </tr>
                                                
                              <tr>
                            <td valign="top" width="80%" style="padding:7px 9px 9px 9px; border:1px solid #bebcb7; border-top:0; background:#f8f7f5;">
                                Verzending
                            </td>
                             <td valign="top" width="20%" style="padding:7px 9px 9px 9px; border:1px solid #bebcb7; border-top:0; background:#f8f7f5;">
                                <?= $this->Number->currency($data['shipping']/100, 'EUR')?>                            </td>
                                                </tr>   
<? if ($data["discount"] == "true"): ?>
          <tr>
                            <td valign="top" width="80%" style="padding:7px 9px 9px 9px; border:1px solid #bebcb7; border-top:0; background:#f8f7f5;">
                                Korting
                            </td>
                             <td valign="top" width="20%" style="padding:7px 9px 9px 9px; border:1px solid #bebcb7; border-top:0; background:#f8f7f5;">
                              Û  1,50
                            </td>
                                                </tr>      
                                           <? endif; ?>                                                                                     
                                     <tr>
                            <td valign="top" width="80%" style="padding:7px 9px 9px 9px; border:1px solid #bebcb7; border-top:0; background:#f8f7f5;">
                                Exclusief BTW
                            </td>
                             <td valign="top" width="20%" style="padding:7px 9px 9px 9px; border:1px solid #bebcb7; border-top:0; background:#f8f7f5;">
                                <?= $this->Number->currency($data['excl_tax']/100, 'EUR')?>
                            </td>
                                                </tr>    
                                                <tr>
                            <td valign="top" width="80%" style="padding:7px 9px 9px 9px; border:1px solid #bebcb7; border-top:0; background:#f8f7f5;">
                                 BTW
                            </td>
                             <td valign="top" width="20%" style="padding:7px 9px 9px 9px; border:1px solid #bebcb7; border-top:0; background:#f8f7f5;">
                                <?= $this->Number->currency($data['tax']/100, 'EUR')?>
                            </td>
                                                </tr>          
                                                             <tr>
                            <td valign="top" width="80%" style="padding:7px 9px 9px 9px; border:1px solid #bebcb7; border-top:0; background:#f8f7f5;">
                                 <strong>Totaal</strong>
                            </td>
                             <td valign="top" width="20%" style="padding:7px 9px 9px 9px; border:1px solid #bebcb7; border-top:0; background:#f8f7f5;">
                             <strong> <?= $this->Number->currency($data['total']/100, 'EUR')?> </strong>
                            </td>
                                                </tr>                        
                        </tbody>
                    </table>
                    <p><?= $data['closing']?></p>
                </td>
          </tr>
        </table>
    </td>
</tr>
</table>
</div>