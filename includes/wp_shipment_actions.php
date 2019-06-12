<?php

class shipmentActions{

    function save_label(){
        if ( ! isset( $_POST['create_label_form'] ) || ! wp_verify_nonce( $_POST['create_label_form'], 'create_label' )
        ) {
            echo 'Sorry, your nonce did not verify.';
            exit;
        } else {
            var_dump($_POST);die;
        }
    }
}