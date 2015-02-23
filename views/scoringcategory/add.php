<div class="wrap">
    <h2>
        <?=!$bIsEdit ? __("Add Scoring Category") : __("Edit Scoring Category");?>
        <?php if($bIsEdit):?>
        <a class="add-new-h2" href="<?=self::$urladdnew;?>"><?=__("Add New");?></a>
        <?php endif;?>
    </h2>
    <?=settings_errors();?>
    <form method="post" action="" enctype="multipart/form-data">
        <input type="hidden" name="val[id]" value="<?=$aForms['id'];?>" />
        <input type="hidden" id="scoringTypes" value='<?=json_encode($aScoringTypes);?>' />
        <input type="hidden" id="selectType" value="<?=$aForms['scoring_type'];?>" />
        <table class="form-table">
            <tr valign="top">
                <th scope="row"><?=__("Organization");?> <span class="description">(<?=__("required");?>)</span></th>
                <td>
                    <?php if($aSports != null):?>
                        <select name="val[org_id]" id="org" <?php if(isset($aForms) && $aForms['siteID'] == 0):;?>disabled="true"<?php endif;?> onchange="jQuery.scoringcat.loadScoringType()">
                        <?php foreach($aSports as $aSport):?>
                            <?php if(is_array($aSport['child']) && $aSport['child'] != null):?>
                            <option disabled="true"><?=$aSport['name'];?></option>
                            <?php foreach($aSport['child'] as $aOrg):?>
                                <?php if($aOrg['is_active'] == 1):?>
                                <option value="<?=$aOrg['id'];?>" style="padding-left: 20px" <?php if($aForms['org_id'] == $aOrg['id']):?>selected="true"<?php endif;?>>
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
                <th scope="row"><?=__("Type");?></th>
                <td>
                    <select name="val[scoring_type]" <?php if(isset($aForms) && $aForms['siteID'] == 0):;?>disabled="true"<?php endif;?> id="htmlScoringTypes">
                    </select>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?=__("Name");?> <span class="description">(<?=__("required");?>)</span></th>
                <td>
                    <input type="text" name="val[name]"  <?php if(isset($aForms) && $aForms['siteID'] == 0):;?>disabled="true"<?php endif;?> class="regular-text ltr" value="<?=$aForms['name'];?>" />
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?=__("Point");?> <span class="description">(<?=__("required");?>)</span></th>
                <td>
                    <input type="text" name="val[points]" class="regular-text ltr" value="<?=$aForms['points'];?>" />
                </td>
            </tr>
        </table>
        <?php submit_button(); ?>
    </form>
</div>
