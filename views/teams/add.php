<div class="wrap">
    <h2>
        <?=!$bIsEdit ? __("Add Teams") : __("Edit Teams");?>
        <?php if($bIsEdit):?>
        <a class="add-new-h2" href="<?=self::$url;?>"><?=__("Add New");?></a>
        <?php endif;?>
    </h2>
    <?=settings_errors();?>
    <form method="post" action="" enctype="multipart/form-data">
        <input type="hidden" name="val[teamID]" value="<?=$aForms['teamID'];?>" />
        <table class="form-table">
            <tr valign="top">
                <th scope="row">Image<?=__("Manage Withdrawls");?></th>
                <td>
                    <?php if(isset($aForms) && isset($aForms['image']) && $aForms['image'] != null):?>
                        <div class="p_4" id="js_slide_current_image">
                            <img src="<?=$aForms['full_image_path'];?>" width="80px" height="80px" alt="<?=$aForms['name'];?>" />
                            <br />
                            <a href="#" onclick="jQuery.admin.newImage(); return false;"><?=__("Click here to upload new image");?></a>
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
                <th scope="row"><?=__("Organization");?> <span class="description">(<?=__("required");?>)</span></th>
                <td>
                    <select name="val[organization]">
                        <option value="">--Please select organization--</option>
                        <?php foreach($aOrgs as $aOrg):?>
                        <option <?=$aForms['organization_id'] == $aOrg['organizationID'] ? 'selected="true"' : '';?> value="<?=$aOrg['organizationID'];?>" class="level-0"><?=$aOrg['description'];?></option>
                        <?php endforeach;?>
                    </select>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?=__("Name");?> <span class="description">(<?=__("required");?>)</span></th>
                <td>
                    <input type="text" name="val[name]" class="regular-text ltr" value="<?=$aForms['name'];?>" />
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?=__("Nick name");?></th>
                <td>
                    <input type="text" name="val[nickName]" class="regular-text ltr" value="<?=$aForms['nickName'];?>" />
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?=__("Home page link");?></th>
                <td>
                    <input type="text" name="val[homepageLink]" class="regular-text ltr" value="<?=$aForms['homepageLink'];?>" />
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?=__("City name");?></th>
                <td>
                    <input type="text" name="val[cityname]" class="regular-text ltr" value="<?=$aForms['cityname'];?>" />
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?=__("Team name");?></th>
                <td>
                    <input type="text" name="val[teamname]" class="regular-text ltr" value="<?=$aForms['teamname'];?>" />
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?=__("Record");?></th>
                <td>
                    <input type="text" name="val[record]" class="regular-text ltr" value="<?=$aForms['record'];?>" />
                </td>
            </tr>
        </table>
        <?php submit_button(); ?>
    </form>
</div>
