<div class="wrap">
    <h2>
        <?=!$bIsEdit ? __("Add Fighters") : __("Edit Fighters");?>
        <?php if($bIsEdit):?>
        <a class="add-new-h2" href="<?=self::$urladdnew;?>"><?=__("Add New");?></a>
        <?php endif;?>
    </h2>
    <?=settings_errors();?>
    <form method="post" action="" enctype="multipart/form-data">
        <input type="hidden" name="val[fighterID]" value="<?=$aForms['fighterID'];?>" />
        <table class="form-table">
            <tr valign="top">
                <th scope="row"><?=__("Image");?></th>
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
                <th scope="row"><?=__("Age");?></th>
                <td>
                    <input type="text" name="val[age]" class="regular-text ltr" value="<?=$aForms['age'];?>" />
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?=__("Fight camp");?></th>
                <td>
                    <input type="text" name="val[fightCamp]" class="regular-text ltr" value="<?=$aForms['fightCamp'];?>" />
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?=__("Strengths");?></th>
                <td>
                    <input type="text" name="val[strengths]" class="regular-text ltr" value="<?=$aForms['strengths'];?>" />
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?=__("Home page link");?></th>
                <td>
                    <input type="text" name="val[homepageLink]" class="regular-text ltr" value="<?=$aForms['homepageLink'];?>" />
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?=__("Height");?></th>
                <td>
                    <input type="text" name="val[height]" class="regular-text ltr" value="<?=$aForms['height'];?>" />
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?=__("Weight");?></th>
                <td>
                    <input type="text" name="val[weight]" class="regular-text ltr" value="<?=$aForms['weight'];?>" />
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
