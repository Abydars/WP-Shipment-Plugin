<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if (!class_exists('WPSP_CAA')) :
    
    final class WPSP_CAA {
        
        function getAgentNameStringByIDS($agents){
            $agent_names = array();
            foreach( $agents as $agent_id ){
                $agent = get_userdata($agent_id);
                $agent_names[] = $agent->display_name;
            }
            return implode(',<br>', $agent_names);
        }
        
        function isRuleMatch($rules){
            $flag = TRUE;
            foreach ($rules as $rule_key=>$rule){
                if( !$rule['status'] ) continue;
                if( ! (isset($_POST[$rule_key]) && $this->is_rule_option_match($rule_key,$rule['options'])) ){
                    $flag = FALSE;
                    break;
                }
            }
            return $flag;
        }
        
        function is_rule_option_match( $rule_key, $options ){
            $flag = false;
            foreach ( $options as $option ){
                if( $_POST[$rule_key] == $option ){
                    $flag = true;
                    break;
                }
            }
            return $flag;
        }
    
    }

endif;

$GLOBALS['WPSP_CAA'] = new WPSP_CAA();
