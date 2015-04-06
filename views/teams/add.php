<div class="wrap">
    <h2>
        <?=!$bIsEdit ? __("Add Teams", FV_DOMAIN) : __("Edit Teams", FV_DOMAIN);?>
        <?php if($bIsEdit):?>
        <a class="add-new-h2" href="<?=self::$urladdnew;?>"><?=__("Add New", FV_DOMAIN);?></a>
        <?php endif;?>
    </h2>
    <?=settings_errors();?>
    <form method="post" action="" enctype="multipart/form-data">
        <input type="hidden" name="val[teamID]" value="<?=$aForms['teamID'];?>" />
        <table class="form-table">
            <tr valign="top">
                <th scope="row"><?=__("Image", FV_DOMAIN);?></th>
                <td>
                    <?php if(isset($aForms) && isset($aForms['image']) && $aForms['image'] != null):?>
                        <div class="p_4" id="js_slide_current_image">
                            <img src="<?=$aForms['full_image_path'];?>" width="80px" height="80px" alt="<?=$aForms['name'];?>" />
                            <br />
                            <a href="#" onclick="jQuery.admin.newImage(); return false;"><?=__("Click here to upload new image", FV_DOMAIN);?></a>
                        </div>
                    <?php endif;?>
                    <div id="js_submit_upload_image" <?php if(isset($aForms) && isset($aForms['image']) && $aForms['image'] != null):?>style="display:none"<?php endif;?>>
                        <input type="file" id='image' name="image" />
                        <div class="extra_info">
                            <?=__("You can upload a jpg, gif or png file", FV_DOMAIN);?>
                        </div>
                    </div>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?=__("Sport");?> <span class="description">(<?=__("required", FV_DOMAIN);?>)</span></th>
                <td>
                    <?php if($aSports != null):?>
                        <select name="val[organization]">
                        <?php foreach($aSports as $aSport):?>
                            <?php if(is_array($aSport['child']) && $aSport['child'] != null):?>
                            <option disabled="true"><?=$aSport['name'];?></option>
                            <?php foreach($aSport['child'] as $aOrg):?>
                                <?php if($aOrg['is_active'] == 1):?>
                                <option value="<?=$aOrg['id'];?>" style="padding-left: 20px" <?php if($aForms['organization_id'] == $aOrg['id']):?>selected="true"<?php endif;?>>
                                    <?=$aOrg['name'];?>
                                </option>
                                <?php endif;?>
                            <?php endforeach;?>
                            <?php endif;?>
                        <?php endforeach;?>
                        </select>
                    <?php endif;?>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?=__("Name");?> <span class="description">(<?=__("required", FV_DOMAIN);?>)</span></th>
                <td>
                    <input type="text" name="val[name]" class="regular-text ltr" value="<?=$aForms['name'];?>" />
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?=__("Nick name", FV_DOMAIN);?></th>
                <td>
                    <input type="text" name="val[nickName]" class="regular-text ltr" value="<?=$aForms['nickName'];?>" />
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?=__("Home page link", FV_DOMAIN);?></th>
                <td>
                    <input type="text" name="val[homepageLink]" class="regular-text ltr" value="<?=$aForms['homepageLink'];?>" />
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?=__("City name", FV_DOMAIN);?></th>
                <td>
                    <input type="text" name="val[cityname]" class="regular-text ltr" value="<?=$aForms['cityname'];?>" />
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?=__("Team name", FV_DOMAIN);?></th>
                <td>
                    <input type="text" name="val[teamname]" class="regular-text ltr" value="<?=$aForms['teamname'];?>" />
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?=__("Record", FV_DOMAIN);?></th>
                <td>
                    <input type="text" name="val[record]" class="regular-text ltr" value="<?=$aForms['record'];?>" />
                </td>
            </tr>
        </table>
        <?php submit_button(); ?>
    </form>
</div>
