<div class="wrap">    <h2>        <?=!$bIsEdit ? __("Add Players", FV_DOMAIN) : __("Edit Players", FV_DOMAIN);?>        <?php if($bIsEdit):?>        <a class="add-new-h2" href="<?=self::$urladdnew;?>"><?=__("Add New", FV_DOMAIN);?></a>        <?php endif;?>    </h2>    <?=settings_errors();?>    <input type="hidden" id="teamsData" value='<?=str_replace("'", "*", json_encode($aTeams));?>' />    <input type="hidden" id="positionsData" value='<?=str_replace("'", "\'", json_encode($aPositions));?>' />    <input type="hidden" id="selectTeam" value='<?=$aForms['team_id'];?>' />    <input type="hidden" id="selectPosition" value='<?=$aForms['position_id'];?>' />    <form method="post" action="" enctype="multipart/form-data">        <input type="hidden" name="val[id]" value="<?=$aForms['id'];?>" />        <table class="form-table">            <tr valign="top">                <th scope="row"><?=__("Image", FV_DOMAIN);?></th>                <td>                    <?php if(isset($aForms) && isset($aForms['image']) && $aForms['image'] != null):?>                        <div class="p_4" id="js_slide_current_image">                            <img src="<?=$aForms['full_image_path'];?>" width="80px" height="80px" alt="<?=$aForms['name'];?>" />                            <?php if(isset($aForms) && $aForms['siteID'] > 0):;?>                            <br />                            <a href="#" onclick="jQuery.admin.newImage(); return false;"><?=__("Click here to upload new image", FV_DOMAIN);?></a>                            <?php endif;?>                        </div>                    <?php endif;?>                    <div id="js_submit_upload_image" <?php if(isset($aForms) && isset($aForms['image']) && $aForms['image'] != null):?>style="display:none"<?php endif;?>>                        <input type="file" id='image' name="image" />                        <div class="extra_info">                            <?=__("You can upload a jpg, gif or png file", FV_DOMAIN);?>                        </div>                    </div>                </td>            </tr>            <?php if(isset($aForms) && $aForms['siteID'] > 0 || !$bIsEdit):;?>            <tr valign="top">                <th scope="row"><?=__("Organization");?> <span class="description">(<?=__("required", FV_DOMAIN);?>)</span></th>                <td>                    <?php if($aSports != null):?>                        <select id="org" name="val[org_id]" onchange="jQuery.players.loadTeams();jQuery.players.loadPositions();">                        <?php foreach($aSports as $aSport):?>                            <?php if(!empty($aSport['child'])):?>                            <option disabled="true"><?=$aSport['name'];?></option>                            <?php foreach($aSport['child'] as $aOrg):?>                                <option value="<?=$aOrg['id'];?>" style="padding-left: 20px" <?php if($aForms['org_id'] == $aOrg['id']):?>selected="true"<?php endif;?>>                                    <?=$aOrg['name'];?>                                </option>                            <?php endforeach;?>                            <?php endif;?>                        <?php endforeach;?>                        </select>                    <?php endif;?>                </td>            </tr>            <tr valign="top">                <th scope="row"><?="Teams";?></th>                <td id="htmlTeams"></td>            </tr>            <tr valign="top">                <th scope="row"><?="Position";?> <span class="description">(<?=__("required", FV_DOMAIN);?>)</span></th>                <td id="htmlPositions"></td>            </tr>            <tr valign="top">                <th scope="row"><?=__("Indicator", FV_DOMAIN);?></th>                <td>                    <?php if($aIndicators != null):?>                    <select name="val[indicator_id]">                        <option value="0">None</option>                        <?php foreach($aIndicators as $aIndicator):?>                        <option <?=$aForms['indicator_id'] == $aIndicator['id'] ? 'selected="true"' : '';?> value="<?=$aIndicator['id'];?>">                            <?=$aIndicator['name'];?>                        </option>                        <?php endforeach;?>                    </select>                    <?php endif;?>                </td>            </tr>            <?php endif;?>            <tr valign="top">                <th scope="row"><?=__("Name");?> <span class="description">(<?=__("required", FV_DOMAIN);?>)</span></th>                <td>                    <input type="text" name="val[name]" class="regular-text ltr" value="<?=$aForms['name'];?>" <?php if(isset($aForms) && $aForms['siteID'] == 0):;?>disabled="true"<?php endif;?> />                </td>            </tr>            <tr valign="top">                <th scope="row"><?=__("Salary");?> <span class="description">(<?=__("required", FV_DOMAIN);?>)</span></th>                <td>                    <input type="text" name="val[salary]" class="regular-text ltr" value="<?=  number_format($aForms['salary']);?>" onkeyup="this.value = accounting.formatNumber(this.value)" />                </td>            </tr>        </table>        <?php submit_button(); ?>    </form></div><script type="text/javascript">jQuery(window).load(function(){    jQuery.players.setData();    jQuery.players.loadTeams();    jQuery.players.loadPositions();})</script>