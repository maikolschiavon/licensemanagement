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

function license_management_service() {

    $service = new license_management_service();

    $call_ajax = false;
    $message = $service_name = $licenseid = $service_status = $date_end = $messages = "";
    
    if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "license_management_request"){
        $call_ajax = true;
    }

    if (isset($_POST['create']) ) {   // CREATE
        $params = $service->before_save("",$_REQUEST);
        $create_service = $service->create($params);
        if($params["call_ajax"]){
            echo $create_service;
        }else{
            echo license_management_get_html_after_save_no_ajax($create_service,"service");
        }
    }
    else if ( isset($_POST['delete']) ) { // DELETE
        $params = $service->before_save("",$_REQUEST);
        $serviceid = $params["serviceid"];
        $licenseid = $params["licenseid"];

        $delete_service = $service->delete($serviceid,$licenseid);
        if($params["call_ajax"]){
            $message = $delete_service["result_message"];
            echo $message;
        }else{
            echo license_management_get_html_after_save_no_ajax($delete_service,"service");
        }
    }
    else if ( isset($_POST['update']) ) { // UPDATE

        $params = $service->before_save("",$_REQUEST);
        $update_service = $service->update($params);
        $message = $update_service["result_message"];

        if($params["call_ajax"]){
            echo $message;
        }else{
            echo license_management_get_html_after_save_no_ajax($update_service,"service");
        }
    }else{

        $check = $service->requestCheck($_REQUEST);
        $serviceid = $check["serviceid"];
        $licenseid = $check["licenseid"];
        $service_status = $check["service_status"];
        $date_end = $check["date_end"];
        $messages = $check["messages"];

        $button_action = $check["button_action"];
        $mode = $check["mode"];

        $license_html = license_management_get_field_html("licenseid",$licenseid,true,15);
        $service_name_html = license_management_get_field_html("serviceid",$serviceid,true,15);
        $service_status_html = license_management_get_field_html("service_status",$service_status,true,15);

        ?>
        <div class="wrap" id="license-management-container">
           
            <?php if(!$call_ajax) {  ?> <h2> <?php _e('LBL_CREATE_SERVICE', 'license-management'); ?> </h2> <?php } ?>
            <?php if (isset($message) && !empty($message) ): ?><div class="updated"><p><?php echo $message; ?></p></div><?php endif; ?>
            <?php if(!$call_ajax) { ?> <form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>"> <?php } ?>

                <?php echo $service->getButtonsAction($call_ajax, $mode, $button_action); ?>

                <table class='wp-list-table widefat'>
                    <tr>
                        <th class="ss-th-width"> <?php _e('Service Name', 'license-management'); ?> *</th>
                        <td>
                        <div class="input-group"> 
                            <?php echo $service_name_html; ?>
                            <div class="input-group-btn">
                                <?php if(!$call_ajax) { ?>
                                    <button type="button" class="btn btn-default" aria-label="Help" data-toggle="collapse" data-target="#collapseServiceCreate" aria-expanded="false" aria-controls="collapseServiceCreate">
                                        <span class="glyphicon glyphicon-plus"></span>
                                    </button>
                                <?php }else{ ?>
                                    <button type="button" class="btn btn-default" onclick="license_management_modal('create_service_lbl')">
                                        <span class="glyphicon glyphicon-plus"></span>
                                    </button>
                                <?php } ?>
                            </div>
                        </div>

                        <div class="collapse" id="collapseServiceCreate" aria-expanded="true" style="">
                            <div class="collapse-response-message" style="display: none;"></div>
                            <div class="well">
                                <div class="row">
                                    <label><?php _e('LBL_NEW_SERVICE', 'license-management');?></label>
                                    <input type="text" id="new_service_name" value="<?php echo $service_name; ?>" class="ss-field-width"/>
                                    <input type='button' onclick="license_management_save_ajax('create_service');" value='<?php _e('LBL_ADD', 'license-management'); ?>' class='btn btn-dft-license-save'>
                                </div>
                            </div>
                        </div>
                        </td>
                    
                    </tr>
                    <tr>
                        <th class="ss-th-width"> <?php _e('License Plate', 'license-management'); ?> *</th>
                        <td><?php echo $license_html; ?></td>
                    </tr>
                    <tr>
                        <th class="ss-th-width"> <?php _e('Service Status', 'license-management'); ?>  *</th>
                        <td><?php echo $service_status_html; ?></td>
                    </tr>
                    <tr>
                        <th class="ss-th-width"> <?php _e('Date End', 'license-management'); ?> </th>
                        <td><input type="date" value="<?php _e($date_end); ?>" aria-required="true" aria-invalid="false" name="date_end"></td> <!-- TODO  min=" _e($today); " -->
                    </tr>
                    <tr>
                        <th class="ss-th-width"><?php _e('Messages', 'license-management'); ?></th>
                        <td><textarea type="text" name="messages" class="ss-field-width form-control"><?php echo $messages; ?></textarea></td>
                    </tr>
                </table>
            
                <?php if(!$call_ajax) { ?> </form> <?php } ?>
        </div>
        <?php
    }
}

class license_management_service{
    public function get_table_name(){
        global $wpdb;
        $table_name = $wpdb->prefix . "license_management_service_rel";
        return $table_name;
    }

    public function create($params){
        global $wpdb;

        $result = 0;
        $result_message = "";
        $now = date("Y-m-d H:i:s");
        $table_name = $this->get_table_name();

        $serviceid      = $params["serviceid"];
        $licenseid      = $params["licenseid"];
        $service_status = $params["service_status"];
        $date_end = $params["date_end"];
        $messages = $params["messages"];
        $createduserid = $this->get_current_user_id();
        $call_ajax = $params["call_ajax"];
        
        if($params["error"]){
            $result_message = __('ALERT_MANDATORY_FIELD', 'license-management');
        } else{  
            if(!license_management_check_duplicate("service",$serviceid,$licenseid)){
                $ex_insert = $wpdb->insert(
                    $table_name, //table
                    array('serviceid' => $serviceid, 'licenseid' => $licenseid, 'createdtime' => $now, 'service_status' => $service_status, 'date_end' => $date_end, 'messages' => $messages, 'createduserid' => $createduserid, 'deleted' => 0), //data
                    array('%s', '%s') //data format			
                );
                if($ex_insert){
                    $result = 1;
                    $result_message = __('Inserted', 'license-management');
                }else{
                    $result_message = __('Error Inserted', 'license-management');
                }
            }else{
                $result_message = __('ALERT_SERVICE_EXIST', 'license-management');
            }
        }

        if($call_ajax){
            return json_encode(array("result"=>$result, "result_message"=>$result_message));
        }
        else{
            return array("result"=>$result, "result_message"=>$result_message);
        }
    }

    public function delete($serviceid,$licenseid){
        global $wpdb;
        $table_name = $this->get_table_name();
        $now = date("Y-m-d H:i:s");

        if(isset($serviceid) && !empty($serviceid) && !empty($licenseid)){
            $wpdb->query($wpdb->prepare("UPDATE $table_name SET deleted = 1, deletedtime = '$now' WHERE serviceid = %d AND licenseid = $licenseid", $serviceid));
            $result_message = __('SERVICE_SUCCESS_DELETE', 'license-management');
        }else{
            $result_message = __('SERVICE_ERROR_DELETE', 'license-management');
        }

        return array("result_message"=>$result_message);
    }

    public function update($params){
        global $wpdb;

        $result = false;
        $result_message = "";
        $now = date("Y-m-d H:i:s");

        $table_name = $this->get_table_name();

        $serviceid      = $params["serviceid"];
        $licenseid      = $params["licenseid"];
        $service_status = $params["service_status"];
        $date_end = $params["date_end"];
        $messages = $params["messages"];

        $changelog = license_management_set_changelog($serviceid,"service","update",$licenseid);
        if($changelog){
            /*$wpdb->update(
                    $table_name, //table
                    array('service_name' => $service_name, 'licenseid' => $licenseid, 'service_status' => $service_status, 'date_end' => $date_end, 'modifiedtime' => $now), //data
                    array('ID' => $serviceid), //where
                    array('%s'), //data format
                    array('%s') //where format
            );*/
            $ex_update = $wpdb->update(
                $table_name, //table
                array('licenseid' => $licenseid, 'service_status' => $service_status, 'date_end' => $date_end, 'messages' => $messages, 'modifiedtime' => $now), //data
                array('serviceid' => $serviceid, "licenseid" => $licenseid ) //where
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
        $html = "";

        if(!$call_ajax) {
            $html .= "<input type='submit' name='$button_action' value='".__('LBL_SAVE', 'license-management')."' class='btn btn-dft-license-save'>";
        } else {
            $html .= '<input type="button" name="'.$button_action.'" value="'.__('LBL_SAVE', 'license-management').'" class="btn btn-dft-license-save" id="btn-service-save" onclick="license_management_save_ajax(\'service\');"> ';
        }
        
        if ($mode != 'create') { 
            // $message_confirm = _e('LBL_CONFIRM_DELETE_SERVICE', 'license-management'); // Vuoi cancellare il servizio?
            $html .= "<input type='submit' name='delete' value='Delete' class='btn btn-dft-license-delete' onclick='return confirm(\' ".__('LBL_CONFIRM_DELETE_SERVICE', 'license-management')." \')'>";
         }

        return $html;
    }

    public function requestCheck($request){
        $serviceid = $licenseid = $service_name = $messages = $service_status = $date_end = "";
        $call_ajax = false;

        $mode = $button_action = "create";
       
        if(isset($request["service_name"])){ $service_name = sanitize_text_field($request["service_name"]); }
        if(isset($request["serviceid"])){ $serviceid = intval($request["serviceid"]); }
        if(isset($request["licenseid"])){ $licenseid = intval($request["licenseid"]); }
        if(isset($request["messages"])){ $messages = sanitize_text_field($request["messages"]); }
        if(isset($request["service_status"])){ $service_status = intval($request["service_status"]); }
        if(isset($request["date_end"]) && !empty($request["date_end"])){ $date_end = date('Y-m-d', strtotime($request["date_end"])); }

        if(isset($request["id"])){ $serviceid = intval($request["id"]); }

        if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "license_management_request"){
            $call_ajax = true;
        }

        if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "license_management_request" && 
            isset($_REQUEST["create"]) && $_REQUEST["create"] == "create_service" ){
            $mandatory_field = array("service_name");
        }else{
            $mandatory_field = array("serviceid","licenseid","service_status");
        }

        if(isset($request["id"]) ){
            if( isset($request['update']) || isset($request['delete']) ){
                $mode = $button_action = "update";
            }else{
                $mode = "editview";
                $button_action = "update";
    
                $retrive = license_management_retrive($serviceid,"service","*",$licenseid);
                if(!empty($retrive)){
                    $mode = "update";
    
                    $service_name = $retrive[0]->service_name;
                    $licenseid = $retrive[0]->licenseid;
                    $service_status = $retrive[0]->service_status;
                    $date_end = $retrive[0]->date_end;
                    $messages = $retrive[0]->messages;
                }
            }
        }

        $params = array("error"=>false,"mode"=>$mode,"button_action"=>$button_action,"call_ajax"=>$call_ajax,"serviceid"=>$serviceid,"licenseid"=>$licenseid,"service_name"=>$service_name,"messages"=>$messages,"service_status"=>$service_status,"date_end"=>$date_end);

        foreach($params as $param_name => $param_val){
            if(in_array($param_name,$mandatory_field) && empty($param_val)){
                $params["error"] = true;
            }
        }
        return $params;
    }

    public function create_service($request){
        global $wpdb;
        $now = date("Y-m-d H:i:s");

        $result = 0;
        $entityid = $result_message = "";
        $check = $this->requestCheck($request);
        $service_name = $check["service_name"];

        if($check["error"]){
            $result_message = __('ALERT_MANDATORY_FIELD', 'license-management');
        } else{
            if(!license_management_check_duplicate("service_name",$service_name)){
                $ex_insert = $wpdb->insert(
                    $wpdb->prefix . "license_management_service", //table
                    array('service_name' => $service_name, 'createdtime' => $now, 'deleted' => 0), //data
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
                $result_message = __('ALERT_SERVICE_EXIST', 'license-management');
            }
        }

        $table_html = license_management_get_row_table_list("service",$entityid,$service_name);
        return json_encode(array("entityid"=>$entityid,"html"=>$table_html,"result_message"=>$result_message,"result"=>$result));
    }

    public function quick_service_html($request,$mode){
        $check = $this->requestCheck($request);

        if($mode == "create"){
            $title = __('LBL_NEW_SERVICE','license-management');

            $html = '<div class="row"><div class="col-xs-12"><label>'.__('Service Name','license-management').'</label><input type="text" name="service_name" id="new_service_name" class="ss-field-width form-control">
            <input type="button" value="Save" class="btn btn-dft-license-save" onclick="license_management_save_ajax(\'create_service\');"></div></div>';

        }elseif($mode == "edit" && isset($check["serviceid"])){
            $serviceid = $check["serviceid"];
            $title = __('LBL_EDIT_SERVICE','license-management');

            $html = '<div class="row"><div class="col-xs-12"><label>'.__('Service Name','license-management').'</label><input type="text" name="service_name" class="ss-field-width form-control">
            <input type="button" value="Save" class="btn btn-dft-license-save" onclick="license_management_easy_save(\'update_service\','.$serviceid.');"></div></div>';    
        }

        return json_encode(array("title"=>$title,"html"=>$html));
    }

    public function update_service($request){
        global $wpdb;
        $now = date("Y-m-d H:i:s");
        $table_name = $wpdb->prefix . "license_management_service";
        $result_message = "";
        $result = 2;

        $check = $this->requestCheck($request);
        $serviceid = $check["serviceid"];
        $service_name = $check["service_name"];

        if(!empty($service_name)){
            if(!license_management_check_duplicate("service_name",$service_name)){
                $ex_update= $wpdb->update(
                    $table_name, //table
                    array('service_name' => $service_name, 'modifiedtime' => $now), //data
                    array('id' => $serviceid, "deleted" => 0 ) //where
                );
                if($ex_update){
                    $result = 1;
                    $result_message = __('Updated', 'license-management');
                }else{
                    $result_message = __('Error Updated', 'license-management');
                }
            }else{
                $result_message = __('ALERT_SERVICE_EXIST', 'license-management');
            }
        }else{
            $result_message = __('ALERT_SERVICE_EMPTY', 'license-management');
        }

        return json_encode(array("message"=>$result_message,"result"=>$result));
    }

    public function add_messages($request){
        global $wpdb;
        $html = $service_name = "";
        
        $check = $this->requestCheck($request);
        $serviceid = $check["serviceid"];
        $licenseid = $check["licenseid"];

        $retrive = license_management_retrive($serviceid,"service","messages, service_name",$licenseid);
        if(!empty($retrive)){
            $service_name = $retrive[0]->service_name;
            $messages = $retrive[0]->messages;
            if(!empty($messages)){
                $html .= "<div class='row'><div class='col-xs-12'><div class='box1 sb7'>$messages</div></div></div>";
            }
        }
        
        $html .= '<div class="row"><div class="col-xs-12"><label>'.__('LBL_ADD_MESSAGES','license-management').'</label><textarea type="text" name="messages" class="ss-field-width form-control"></textarea>
        <input type="button" value="Save" class="btn btn-dft-license-save" onclick="license_management_easy_save(\'service_messages\','.$serviceid.','.$licenseid.');"></div></div>';

        $title = $service_name;
        return json_encode(array("title"=>$title,"html"=>$html));
    }

    public function create_messages($request){
        global $wpdb;
        $now = date("Y-m-d H:i:s");
        $html = "";
        $result = 2;

        $table_name = $this->get_table_name();
        
        $check = $this->requestCheck($request);
        $messages = $check["messages"];
        $serviceid = $check["serviceid"];
        $licenseid = $check["licenseid"];
        
        $ex_update = $wpdb->update(
            $table_name, //table
            array('messages' => $messages, 'modifiedtime' => $now), //data
            array('serviceid' => $serviceid, "licenseid" => $licenseid, "deleted" => 0 ) //where
        );
        if($ex_update){
            $result = 1;
            $html .= "<div class='row'><div class='col-xs-12'><div class='box1 sb7'>$messages</div></div></div>";
            $html .= "<div class='row'><div class='col-xs-12'><label>".__('LBL_ADD_MESSAGES','license-management')."</label><textarea type='text' name='messages' class='ss-field-width form-control'></textarea>
            <input type='button' value='Save' class='btn btn-dft-license-save' onclick=\"license_management_easy_save('service_messages',$serviceid,$licenseid);\"></div></div>";
        }
        return json_encode(array("html"=>$html,"result"=>$result));
    }

    public function before_save($params = "",$request = ""){

        if(empty($params) && !empty($request)){
            $check = $this->requestCheck($request);
            $serviceid = $check["serviceid"];
            // $service_name = $check["service_name"];
            $licenseid = $check["licenseid"];
            $service_status = $check["service_status"];
            $date_end = $check["date_end"];
            $messages = $check["messages"];
            $call_ajax = $check["call_ajax"];
            $error = $check["error"];

            $params = array('error'=>$error, 'call_ajax'=>$call_ajax, 'serviceid' => $serviceid, 'licenseid' => $licenseid, 'service_status' => $service_status, 'date_end' => $date_end, 'messages' => $messages);
        }

        return $params;
    }

    public function get_current_user_id(){
        $userid = "";
        $user = wp_get_current_user();

        if(!empty($user)){
            $userid = $user->ID;
        }

        return $userid;
    }

    public function change_status_rel($params,$status_after){

        if( !empty($params) && isset($params["service_status"]) ){
            if($params["service_status"] != $status_after){

                $params["service_status"] = $status_after;

                return $this->update($params);
            }
        }

    }
}