<div class="wrap">    <h2>        <?=!$bIsEdit ? __("Add Events") : __("Edit Events");?>        <?php if($bIsEdit):?>        <a class="add-new-h2" href="<?=self::$urladdnew;?>"><?=__("Add New");?></a>        <?php endif;?>    </h2>    <?=settings_errors();?>    <form method="post" action="" enctype="multipart/form-data">        <input type="hidden" id="sportData" value='<?=$aSports;?>' />        <input type="hidden" id="selType" value='<?=$aForms['type'];?>' />        <input type="hidden" id="selOrg" value='<?=$aForms['organization'];?>' />        <input type="hidden" id="positionData" value='<?=$aPositions;?>' />        <input type="hidden" id="lineupData" value='<?=$aForms['lineup'];?>' />        <input type="hidden" name="val[poolID]" value="<?=$aForms['poolID'];?>" />        <table class="form-table">            <tr valign="top">                <th scope="row"><?=__("Image");?></th>                <td>                    <?php if(isset($aForms) && isset($aForms['image']) && $aForms['image'] != null):?>                        <div class="p_4" id="js_slide_current_image">                            <img src="<?=$aForms['full_image_path'];?>" width="80px" height="80px" alt="<?=$aForms['poolName'];?>" />                            <br />                            <a href="#" onclick="jQuery.admin.newImage(); return false;"><?=__("Image");?><?=__("Click here to upload new image");?></a>                        </div>                    <?php endif;?>                    <div id="js_submit_upload_image" <?php if(isset($aForms) && isset($aForms['image']) && $aForms['image'] != null):?>style="display:none"<?php endif;?>>                        <input type="file" id='image' name="image" />                        <div class="extra_info">                            <?=__("You can upload a jpg, gif or png file");?>                        </div>                    </div>                </td>            </tr>            <tr valign="top">                <th scope="row"><?=__("Name");?> <span class="description">(<?=__("required");?>)</span></th>                <td>                    <input type="text" name="val[poolName]" class="regular-text ltr" value="<?=$aForms['poolName'];?>" />                </td>            </tr>            <tr valign="top">                <th scope="row"><?=__("Sport");?> <span class="description">(<?=__("required");?>)</span></th>                <td id="sportResult">                    <?php if($aSports != null):?>                        <select id="poolOrgs" name="val[organization]" onchange="jQuery.fight.displayType(); jQuery.fight.loadPosition(); jQuery.fight.loadFightersOrTeams();">                        <?php foreach($aSports as $aSport):?>                            <?php if(!empty($aSport['child']) && is_array($aSport['child']) && $aSport['child'] != null):?>                            <option disabled="true"><?=$aSport['name'];?></option>                            <?php foreach($aSport['child'] as $aOrg):?>                                <?php if($aOrg['is_active'] == 1):?>                                <option value="<?=$aOrg['id'];?>" is_team="<?=$aOrg['is_team'];?>" only_playerdraft="<?=$aOrg['only_playerdraft'];?>" style="padding-left: 20px" <?php if($aForms['organization'] == $aOrg['id']):?>selected="true"<?php endif;?>>                                    <?=$aOrg['name'];?>                                </option>                                <?php endif;?>                            <?php endforeach;?>                            <?php endif;?>                        <?php endforeach;?>                        </select>                    <?php endif;?>                </td>            </tr>            <tr valign="top">                <th scope="row"><?=__("Start Date");?> <span class="description">(<?=__("required");?>)</span></th>                <td>                    <input type="text" name="val[startDate]" value="<?=$aForms['startDateOnly'];?>" id="startDate" size="40" maxlength="150" />                    <?=__("Hour");?>:                    <select name="val[startHour]">                        <?php foreach($aPoolHours as $aPoolHour):?>                        <option value="<?=$aPoolHour;?>" <?=($aForms['startHour'] == $aPoolHour) ? 'selected="true"' : '';?>><?=$aPoolHour;?></option>                        <?php endforeach;?>                    </select>                    <?=__("Minute");?>:                    <select name="val[startMinute]">                        <?php foreach($aPoolMinutes as $aPoolMinute):?>                        <option value="<?=$aPoolMinute;?>" <?=$aForms['startMinute'] == $aPoolMinute ? 'selected="true"' : '';?>><?=$aPoolMinute;?></option>                        <?php endforeach;?>                    </select>                </td>            </tr>            <tr valign="top">                <th scope="row"><?=__("Cut Date");?> <span class="description">(<?=__("required");?>)</span></th>                <td>                    <input type="text" name="val[cutDate]" value="<?=$aForms['cutDateOnly'];?>" id="cutDate" size="40" maxlength="150" />                    <?=__("Hour");?>:                    <select name="val[cutHour]">                        <?php foreach($aPoolHours as $aPoolHour):?>                        <option value="<?=$aPoolHour;?>" <?=$aForms['cutHour'] == $aPoolHour ? 'selected="true"' : '';?>><?=$aPoolHour;?></option>                        <?php endforeach;?>                    </select>                    <?=__("Minute");?>:                    <select name="val[cutMinute]">                        <?php foreach($aPoolMinutes as $aPoolMinute):?>                        <option value="<?=$aPoolMinute;?>" <?=$aForms['cutMinute'] == $aPoolMinute ? 'selected="true"' : '';?>><?=$aPoolMinute;?></option>                        <?php endforeach;?>                    </select>                </td>            </tr>            <tr valign="top">                <th scope="row"><?=__("Live Event");?></th>                <td>                    <input type="checkbox" name="val[live_pool]" <?=$aForms['live_pool'] == 1 ? 'checked="true"' : '';?> value="1" />                </td>            </tr>            <tr valign="top" class="for_playerdraft">                <th scope="row"><?=__("Salary Cap");?></th>                <td>                    <input type="text" name="val[salary_remaining]" value="<?=  number_format($aForms['salary_remaining']);?>" onkeyup="this.value = accounting.formatNumber(this.value)"/>                </td>            </tr>            <tr valign="top" class="for_playerdraft">                <th scope="row"><?=__("Lineup");?></th>                <td id="lineupResult"></td>            </tr>            <tr valign="top" class="exclude_fixture">                <th scope="row"><?=__("Fixture");?> <span class="description">(<?=__("required");?>)</span></th>                <td>                    <?php require_once(FANVICTOR__PLUGIN_DIR_VIEW.'pools/fights.php');?>                </td>            </tr>        </table>        <?php submit_button(); ?>    </form></div>