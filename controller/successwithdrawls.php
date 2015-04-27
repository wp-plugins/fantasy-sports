<?php
Class Successwithdrawls
{
	public function process()
	{
        if(isset($_SESSION['withdrawlID']))
        {
            redirect(admin_url().'admin.php?page=withdrawls', __('Successfully updated', FV_DOMAIN));
        }
        else
        {
            redirect(admin_url().'admin.php?page=withdrawls');
        }
    }
}
?>