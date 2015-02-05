<?php
class Fanvictor_Component_Block_Userbalance extends Phpfox_Component
{
    public function process()
    {
        $aUser = Phpfox::getService('fanvictor.payment')->getUserData();
        $this->template()->assign(array(
            'aUser' => $aUser
        ));
        return 'block';
    }
}
?>