<?php
/*
Copyright (C) License Management Schiavon Maikol

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

function license_management_enterprise() {

    $enterprise = new license_management_enterprise();

    $message = $enterpriseid = $business_name = $userid = "";

    if (isset($_POST['create']) ) {    // CREATE
        $params = $enterprise->before_save("",$_REQUEST);
        $create_enterprise = $enterprise->create($params);
        if($params["call_ajax"]){
            echo $create_enterprise;
        }else{
            echo license_management_get_html_after_save_no_ajax($create_enterprise,"enterprise");
        }
    }
    elseif ( isset($_POST['delete']) ) { // DELETE
        $params = $enterprise->before_save("",$_REQUEST);

        $enterpriseid = $params["enterpriseid"];
        $delete_enterprise = $enterprise->delete($enterpriseid);
        if($params["call_ajax"]){
            $message = $delete_enterprise["result_message"];
            echo $message;
        }else{
            echo license_management_get_html_after_save_no_ajax($delete_enterprise,"enterprise");
        }
    }
    elseif ( isset($_POST['update']) ) { // UPDATE
        
        $params = $enterprise->before_save("",$_REQUEST);
        $update_enterprise = $enterprise->update($params);
        if($params["call_ajax"]){
            $message = $update_enterprise["result_message"];
            echo $message;
        }else{
            echo license_management_get_html_after_save_no_ajax($update_enterprise,"enterprise");
        }
    }else{

        $check = $enterprise->requestCheck($_REQUEST);
        $business_name = $check["business_name"];
        $userid = $check["userid"];

        $button_action = $check["button_action"];
        $mode = $check["mode"];
        $call_ajax = $check["call_ajax"];

        $title_page = __('LBL_ADD_ENTERPRISE', 'license-management');

        $business_name_html = license_management_get_field_html('business_name',$business_name,true,1);

        require_once "utils/license-management-dashboard-utils.php";
        $tool_dachboardUtils = new DashboardUtils;

        $user_html = $tool_dachboardUtils->get_user_html($userid);
        
    ?>
    <div class="wrap" id="license-management-container">
        <?php if(!$call_ajax) { echo "<h2>".$title_page."</h2>"; } ?>
        <?php if (isset($message) && !empty($message) ): ?><div class="updated"><p><?php echo $message; ?></p></div><?php endif; ?>
            <?php if(!$call_ajax) { ?> <form method="post" id="form-entity" action="<?php echo $_SERVER['REQUEST_URI']; ?>"> <?php } ?>

            <?php if(!$call_ajax) { ?> <input type='submit' name="<?php echo $button_action; ?>" value='Save' class='btn btn-dft-license-save'>
                <?php } else { ?>
                    <input type='button' name="<?php echo $button_action; ?>" value='Save' class='btn btn-dft-license-save' onclick="license_management_save_ajax('enterprise');"> 
            <?php } ?>
            
            <?php if ($mode != 'create') { ?>
                <input type='submit' name="delete" value='Delete' class='btn btn-dft-license-delete' onclick="return confirm(' <?php _e('LBL_CONFIRM_DELETE_ENTERPRISE', 'license-management'); ?> ? ')">
            <?php } ?>
            <!-- TODO 
            <?php $link_back = admin_url('admin.php?page=license_management_list'); ?>
            <a class="btn btn-dft-license" href="<?php echo $link_back; ?>"><i class="fas fa-solar-panel"></i><?php _e('LBL_DASHBOARD', 'license-management'); ?></a>
            -->
            <table class='wp-list-table widefat'>
                <tr>
                    <th class="ss-th-width"><?php _e('Business Name', 'license-management'); ?> *</th>
                    <td><?php echo $business_name_html; ?></td>
                </tr>
                <tr>
                    <th class="ss-th-width"><?php _e('Customer User', 'license-management'); ?> *</th>
                    <td><?php echo $user_html; ?></td>
                </tr>
            </table>
          
            <?php if(!$call_ajax) { ?> </form> <?php } ?>
    </div>
    <?php
    }
}

class license_management_enterprise{
    public function get_table_name(){
        global $wpdb;
        $table_name = $wpdb->prefix . "license_management_enterprise";
        return $table_name;
    }

    public function create($params,$request = ''){
        global $wpdb;

        $result = $entityid = 0;
        $result_message = "";
        $now = date("Y-m-d H:i:s");
        $table_name = $this->get_table_name();

        if(empty($params)){
            $params = $this->before_save("",$request);
        }

        $business_name = $params["business_name"];
        $userid = $params["userid"];
        $call_ajax = $params["call_ajax"];
        
        if($params["error"]){
            $result_message = __('ALERT_MANDATORY_FIELD', 'license-management');
        } else{
            if(!license_management_check_duplicate("enterprise",$business_name)){
                
                $ex_insert = $wpdb->insert(
                        $table_name, //table
                        array('business_name' => $business_name, 'userid' => $userid, 'createdtime' => $now, 'deleted' => 0), //data
                        array('%s', '%s') //data format
                );
                $entityid = $wpdb->insert_id;
                if($ex_insert){
                    $result = 1;
                    $result_message = __('Inserted', 'license-management');
                }else{
                    $result_message = __('Error Inserted', 'license-management');
                }
            }else{
                $result_message = __('ALERT_ENTERPRISE_EXIST', 'license-management');
            }
        }

        if($call_ajax){
            require_once "utils/license-management-dashboard-utils.php";
            $tool_dachboardUtils = new DashboardUtils;

            $user_name = $tool_dachboardUtils->get_user_name($userid);
            $entityname = array("business_name"=>$business_name,"user_name"=>$user_name);
            $table_html = license_management_get_row_table_list("enterprise",$entityid,$entityname);
            return json_encode(array("entityid"=>$entityid,"html"=>$table_html,"result_message"=>$result_message,"result"=>$result));
        }else{
            return array("entityid"=>$entityid, "result"=>$result, "result_message"=>$result_message);
        }
    }

    public function delete($enterpriseid){
        global $wpdb;
        $table_name = $this->get_table_name();
        $now = date("Y-m-d H:i:s");

        if(isset($enterpriseid) && !empty($enterpriseid)){
            $wpdb->query($wpdb->prepare("UPDATE $table_name SET deleted = 1, deletedtime = '$now' WHERE id = %s", $enterpriseid));
            $result_message = __('ENTERPRISE_SUCCESS_DELETE', 'license-management');
        }else{
            $result_message = __('ENTERPRISE_ERROR_DELETE', 'license-management');
        }

        return array("result_message"=>$result_message);
    }

    public function update($params){
        global $wpdb;

        $result = false;
        $result_message = "";
        $now = date("Y-m-d H:i:s");

        $table_name = $this->get_table_name();

        $business_name = $params["business_name"];
        $userid = $params["userid"];
        $enterpriseid = $params["enterpriseid"];

        $changelog = license_management_set_changelog($business_name,"enterprise","update",$userid);
        if($changelog){
            $ex_update = $wpdb->update(
                    $table_name, //table
                    array('business_name' => $business_name, 'userid' => $userid, 'modifiedtime' => $now), //data
                    array('ID' => $enterpriseid), //where
                    array('%s'), //data format
                    array('%s') //where format
            );
            if($ex_update){
                $result = true;
                $result_message = __('Updated', 'license-management');
            }else{
                $result_message = __('Error Updated', 'license-management');
            }
        }else{
            $result_message = __('Error Updated', 'license-management');
        }

        return array("result_message"=>$result_message, "result"=>$result);
    }

    public function getButtonsAction($call_ajax, $mode, $button_action){
    }

    public function requestCheck($request){
        $enterpriseid = $business_name = $userid = "";
        $retrive = array();
        $call_ajax = false;
        $mandatory_field = array("business_name","userid");
        
        if(isset($request["enterpriseid"])){ $enterpriseid = intval($request["enterpriseid"]); }
        if(isset($request["business_name"])){ $business_name = sanitize_text_field($request["business_name"]); }
        if(isset($request["userid"])){ $userid = intval($request["userid"]); }

        if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "license_management_request"){
            $call_ajax = true;
        }

        $mode = $button_action = "create";
        if( !empty($request) && isset($request["id"]) ){
            $enterpriseid = intval($request["id"]);
    
            if( isset($request['update']) || isset($request['delete']) ){
                $mode = $button_action = "update";
            }else{
                $mode = "editview";
                $button_action = "update";

                $retrive = license_management_retrive($enterpriseid,"enterprise");
                if(!empty($retrive)){
                    $mode = "update";
    
                    $business_name = $retrive[0]->business_name;
                    $userid = $retrive[0]->userid;
                    // echo "<pre>";print_r($retrive);echo "</pre>";
                }
            }
        }

        $params = array("error"=>false,"mode"=>$mode,"button_action"=>$button_action,"call_ajax"=>$call_ajax,"enterpriseid"=>$enterpriseid,"business_name"=>$business_name,"userid"=>$userid);

        foreach($params as $param_name => $param_val){
            if(in_array($param_name,$mandatory_field) && empty($param_val)){
                $params["error"] = true;
            }
        }

        return $params;
    }

    public function before_save($params = "",$request = ""){
        
        if(empty($params) && !empty($request)){
            $check = $this->requestCheck($request);
            $enterpriseid = $check["enterpriseid"];
            $business_name = $check["business_name"];
            $userid = $check["userid"];
            $call_ajax = $check["call_ajax"];
            $error = $check["error"];

            $params = array('error'=>$error, 'call_ajax'=>$call_ajax, 'enterpriseid' => $enterpriseid, 'business_name' => $business_name, 'userid' => $userid);
        }

        return $params;
    }
}