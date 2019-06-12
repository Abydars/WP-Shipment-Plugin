<?php 
$page=(isset($_REQUEST['type']))?$_REQUEST['type']:'show';
switch ($page){
        case 'add': include( WPSP_COMP_DIR.'includes/admin/getCreateCompanyForm.php' );
				break;
        case 'show': include( WPSP_COMP_DIR.'includes/admin/getCreateCompany.php' );
					break;
        case 'edit': include( WPSP_COMP_DIR.'includes/admin/getEditCompany.php' );
					break;
        case 'delete': include( WPSP_COMP_DIR.'includes/admin/getDeleteCompany.php' );
					break;
}?>