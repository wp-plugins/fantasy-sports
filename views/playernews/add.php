<div class="wrap">
    <h2>
        <?=!$bIsEdit ? __("Add Player News", FV_DOMAIN) : __("Edit Player News", FV_DOMAIN);?>
        <?php if($bIsEdit):?>
        <a class="add-new-h2" href="<?=self::$url;?>"><?=__("Add New", FV_DOMAIN);?></a>
        <?php endif;?>
    </h2>
    <?=settings_errors();?>
    <form method="post" action="" enctype="multipart/form-data">
        <input type="hidden" name="val[id]" value="<?=$aForms['id'];?>" />
        <table class="form-table">
            <tr valign="top">
                <th scope="row"><?=__("Player");?> <span class="description">(<?=__("required", FV_DOMAIN);?>)</span></th>
                <td>
                    <?php if($aPlayers != null):?>
                        <select name="val[playerID]">
                            <option value="0"><?=__("Select a player", FV_DOMAIN);?></option>
                        <?php foreach($aPlayers as $aPlayer):?>
                            <option value="<?=$aPlayer['id'];?>" <?php if($aForms['playerID'] == $aPlayer['id']):?>selected="true"<?php endif;?>>
                                <?=$aPlayer['name'];?>
                            </option>
                        <?php endforeach;?>
                        </select>
                    <?php endif;?>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?=__("Date");?> <span class="description">(<?=__("required", FV_DOMAIN);?>)</span></th>
                <td>
                    <input type="text" name="val[updated]" id="date" class="regular-text ltr" value="<?=$aForms['updated'];?>" />
                    <?=__("example", FV_DOMAIN);?>: 2015-05-08 20:10:00
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?=__("Title");?> <span class="description">(<?=__("required", FV_DOMAIN);?>)</span></th>
                <td>
                    <input type="text" name="val[title]" class="regular-text ltr" value="<?=$aForms['title'];?>" />
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?=__("Content", FV_DOMAIN);?> <span class="description">(<?=__("required", FV_DOMAIN);?>)</span></th>
                <td>
                    <textarea rows="5" class="large-text code" name="val[content]"><?=$aForms['content'];?></textarea>
                </td>
            </tr>
        </table>
        <?php submit_button(); ?>
    </form>
</div>