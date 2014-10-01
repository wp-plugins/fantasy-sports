<div class="wrap">
    <h2>
        <?=!$bIsEdit ? __("Add Pool") : __("Edit Pools");?>
        <?php if($bIsEdit):?>
        <a class="add-new-h2" href="<?=self::$url;?>"><?=__("Add New");?></a>
        <?php endif;?>
    </h2>
    <?=settings_errors();?>
    <form method="post" action="" enctype="multipart/form-data">
        <input type="hidden" name="val[poolID]" value="<?=$aForms['poolID'];?>" />
        <table class="form-table">
            <tr valign="top">
                <th scope="row"><?=__("Image");?></th>
                <td>
                    <?php if(isset($aForms) && isset($aForms['image']) && $aForms['image'] != null):?>
                        <div class="p_4" id="js_slide_current_image">
                            <img src="<?=$aForms['full_image_path'];?>" width="80px" height="80px" alt="<?=$aForms['poolName'];?>" />
                            <br />
                            <a href="#" onclick="jQuery.admin.newImage(); return false;"><?=__("Image");?><?=__("Click here to upload new image");?></a>
                        </div>
                    <?php endif;?>
                    <div id="js_submit_upload_image" <?php if(isset($aForms) && isset($aForms['image']) && $aForms['image'] != null):?>style="display:none"<?php endif;?>>
                        <input type="file" id='image' name="image" />
                        <div class="extra_info">
                            <?=__("You can upload a jpg, gif or png file");?>
                        </div>
                    </div>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?=__("Name");?> <span class="description">(<?=__("required");?>)</span></th>
                <td>
                    <input type="text" name="val[poolName]" class="regular-text ltr" value="<?=$aForms['poolName'];?>" />
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?=__("Sport");?> <span class="description">(<?=__("required");?>)</span></th>
                <td>
                    <select id="poolSport" class="sport" name="val[type]" onchange="jQuery.fight.loadOrgsBySport();jQuery.fight.displayType();">
                        <?php foreach($aSports as $aSport):?>
                        <option <?=$aForms['type'] == $aSport ? 'selected="true"' : '';?> value="<?=$aSport;?>" class="level-0"><?=$aSport;?></option>
                        <?php endforeach;?>
                    </select>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?=__("Organization");?> <span class="description">(<?=__("required");?>)</span></th>
                <td>
                    <input type="hidden" id="selOrgs" value="<?=$aForms['organization'];?>" />
                    <select name="val[organization]" id="poolOrgs" onchange="jQuery.fight.loadFightersOrTeams()"></select>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?=__("Start Date");?> <span class="description">(<?=__("required");?>)</span></th>
                <td>
                    <input type="text" name="val[startDate]" value="<?=$aForms['startDate'];?>" id="startDate" size="40" maxlength="150" />
                    <?=__("Hour");?>:
                    <select name="val[startHour]">
                        <?php foreach($aPoolHours as $aPoolHour):?>
                        <option value="<?=$aPoolHour;?>" <?=$aForms['startHour'] == $aPoolHours ? 'selected="true"' : '';?>><?=$aPoolHour;?></option>
                        <?php endforeach;?>
                    </select>
                    <?=__("Minute");?>:
                    <select name="val[startMinute]">
                        <?php foreach($aPoolMinutes as $aPoolMinute):?>
                        <option value="<?=$aPoolMinute;?>" <?=$aForms['startMinute'] == $aPoolMinutes ? 'selected="true"' : '';?>><?=$aPoolMinute;?></option>
                        <?php endforeach;?>
                    </select>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?=__("Cut Date");?> <span class="description">(<?=__("required");?>)</span></th>
                <td>
                    <input type="text" name="val[cutDate]" value="<?=$aForms['cutDate'];?>" id="cutDate" size="40" maxlength="150" />
                    <?=__("Hour");?>:
                    <select name="val[cutHour]">
                        <?php foreach($aPoolHours as $aPoolHour):?>
                        <option value="<?=$aPoolHour;?>" <?=$aForms['cutHour'] == $aPoolHours ? 'selected="true"' : '';?>><?=$aPoolHour;?></option>
                        <?php endforeach;?>
                    </select>
                    <?=__("Minute");?>:
                    <select name="val[cutMinute]">
                        <?php foreach($aPoolMinutes as $aPoolMinute):?>
                        <option value="<?=$aPoolMinute;?>" <?=$aForms['cutMinute'] == $aPoolMinutes ? 'selected="true"' : '';?>><?=$aPoolMinute;?></option>
                        <?php endforeach;?>
                    </select>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?=__("Live Pool");?></th>
                <td>
                    <input type="checkbox" name="val[live_pool]" <?=$aForms['live_pool'] == 1 ? 'checked="true"' : '';?> value="1" />
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?=__("Fixture");?> <span class="description">(<?=__("required");?>)</span></th>
                <td>
                    <?php require_once(FANVICTOR__PLUGIN_DIR_VIEW.'pools/fights.php');?>
                </td>
            </tr>
        </table>
        <?php submit_button(); ?>
    </form>
</div>
