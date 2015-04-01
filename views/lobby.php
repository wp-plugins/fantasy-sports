<div class="f-lobby" data-filter="true">
    <input type="hidden" id="submitUrl" value="<?=FANVICTOR_URL_SUBMIT_PICKS;?>" />
    <div class="f-title">
        <div>
            <?php if(get_option('fanvictor_create_contest')):?>
            <a class="f-create-contest f-button f-primary f-right" href="<?=FANVICTOR_URL_CREATE_CONTEST;?>">Create Contest</a>
            <?php endif;?>
            <div class="f-nextgame" id="contestCountdown">
                <span class="f-next-game-label"><?=__('Next contest starts in:');?></span>
                <span class="f-value" id="lobbyCountdown"></span>
            </div>
            <h1>Lobby</h1>
        </div>
    </div>
    <div data-filter-group="true" class="f-text-search">
        <ul class="f-unstyled">
            <li>
                <form onsubmit="return false;" action="#" class="f-search-os" id="f-foo">
                    <input type="text" data-filter-type="search" placeholder="Search all contests..." class="f-search-input">
                    <input type="reset" value="" class="f-search-reset">
                </form>
            </li>
        </ul>
    </div>
    <div data-filter-group="true" data-filter-hide="true" data-filter-condition="search=" class="f-filter">
        <h2>Sport</h2>
        <div data-filter-group="true" class="f-sport f-selector">
            <ul>
                <label class="f-all f-checked">
                    <li>
                        <input type="radio" checked="checked" value="" name="sport-select" data-filter-type="sport"><?=__('All sports');?>
                    </li>
                </label>
                <?php if(!empty($aSports)):?>
                <?php foreach($aSports as $aSport):?>
                    <?php if(!empty($aSport['child'])):?>
                    <?php foreach($aSport['child'] as $sport):?>
                    <label>
                        <li>
                            <input type="radio" value="<?=$sport['id'];?>" name="sport-select" data-filter-type="sport"><?=$sport['name'];?>	
                        </li>
                    </label>
                    <?php endforeach;?>
                    <?php endif;?>
                <?php endforeach;?>
                <?php endif;?>
            </ul>
        </div>
        <div class="f-separator"></div>
        <div class="f-type f-selector">
            <h2>Contest Type</h2>
            <ul>
                <label class="f-checked">
                    <li>
                        <input type="radio" checked="checked" value="all" name="contest-type" data-filter-type="type"><?=__('All contest types');?>
                    </li>
                </label>
                <label>
                    <li>
                        <input type="radio" value="headtohead" name="contest-type" data-filter-type="type"><?=__('Head-to-head')?>
                    </li>
                </label>
                <label>
                    <li>
                        <input type="radio" value="league" name="contest-type" data-filter-type="type"><?=__('Leagues')?>
                    </li>
                </label>
            </ul>
        </div>
        <div class="f-separator"></div>
        <div class="f-size f-selector f-filter-condition-hidden">
            <h2>Size</h2>
            <ul>
                <label class="f-checked">
                    <li>
                        <input type="radio" checked="checked" value="all" name="contest-size" data-filter-type="size"><?=__('All');?>
                    </li>
                </label>
                <label>
                    <li>
                        <input type="radio" value="3" name="contest-size" data-filter-type="size">3
                    </li>
                </label>
                <label>
                    <li>
                        <input type="radio" value="4-10" name="contest-size" data-filter-type="size">4-10
                    </li>
                </label>
                <label>
                    <li>
                        <input type="radio" value="11+" name="contest-size" data-filter-type="size">11+
                    </li>
                </label>
            </ul>
        </div>
        <div data-filter-hide="true" data-filter-condition="type=league" class="f-separator f-filter-condition-hidden"></div>
        <div class="f-entryfee">
            <div>
                <div id="rangeSlider"></div>
                <div class="ui-rangeSlider-label ui-rangeSlider-leftLabel">
                    <div class="ui-rangeSlider-label-value"></div>
                    <div class="ui-rangeSlider-label-inner"></div>
                </div>
                <div class="ui-rangeSlider-label ui-rangeSlider-rightLabel">
                    <div class="ui-rangeSlider-label-value"></div>
                    <div class="ui-rangeSlider-label-inner"></div>
                </div>
            </div>
        </div>
        <section data-filter-group="true">
            <div data-filter-persist="custom" class="f-panel active">
                <div class="f-separator"></div>
                <div class="f-startTime f-selector">
                    <h2>Start time</h2>
                    <ul>
                        <label class="f-checked">
                            <li>
                                <input type="radio" checked="checked" value="all" name="startTime" data-filter-type="start"><?=__('All start times')?>
                            </li>
                        </label>
                        <label class="f-">
                            <li>
                                <input type="radio" value="next" name="startTime" data-filter-type="start"><?=__('Next available');?>
                            </li>
                        </label>
                        <label class="f-">
                            <li>
                                <input type="radio" value="today" name="startTime" data-filter-type="start"><?=__('Today')?>
                            </li>
                        </label>
                    </ul>
                </div>
            </div>
        </section>
        <div class="f-clear"></div>
    </div>
    <div class="f-filter f-filters-disabled">
        <p class="f-filter-warning"><?=__('Filters are disabled while searching')?></p>
        <button class="f-button f-primary green" id="f-clear-search-link"><?=__('Clear search')?></button>
    </div>
    <div class="f-items">
        <div id="wrapContest">
            <div class="f-body">
                <div class="f-updatesAvailable">
                    <?=__('New contests are available.');?> <a href="#"><?=__('Refresh');?></a>
                </div>
                <div class="f-inner" style="">
                    <table class="f-items" id="lobbyContent">
                        <thead>
                            <tr>
                                <th class="f-title">
                                    <?=__('Contest');?>
                                </th>
                                <th class="f-gametype">
                                    <?=__('Type');?>
                                </th>
                                <th class="f-entries">
                                    <?=__('Entries');?>
                                </th>
                                <th class="f-entryfee">
                                    <?=__('Entry');?>
                                </th>
                                <th class="f-prizes">
                                    <?=__('Prizes');?>
                                </th>
                                <th class="f-starttime">
                                    <?=__('Starts&nbsp;(ET)');?>
                                </th>
                                <th class="f-entry">&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="f-empty-view">
            <h1><?=__('No contests match your search or filter settings');?></h1>
        </div>
        <div class="f-clear"></div>
    </div>
    
    <div class="f-pager"></div>
</div>
<?php require_once('dlg_info.php');?>