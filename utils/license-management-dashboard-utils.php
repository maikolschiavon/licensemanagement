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

class DashboardUtils{

    function get_license_plate_status($sql_rows){
        $service_status = array();
        if(!empty($sql_rows)){
            
            foreach($sql_rows as $obj){
                $status = $obj->service_status;
                $service_status[$status]["record"][] = $obj;
                $service_status[$status]["count"] = count($service_status[$status]["record"]);
            }
        }
        return $service_status;
    }

    function simple_table($sql_rows,$actions){

        // lm@1.2
        $all_actions = array("status"=>array("icon"=>"fas fa-pen","link"=>"license_management_service","eleID"=>"serviceid"),
                        "documents"=>array("icon"=>"far fa-file","click"=>"license_management_modal","eleID"=>"serviceid"),
                        "authorization"=>array("icon"=>"far fa-file-alt","click"=>"license_management_modal","eleID"=>"serviceid"));
        // lm@1.2e
        $status = array(1 =>array( "css"=>"status1", "lbl"=> __('status_1', 'license-management') ),
                        2 =>array( "css"=>"status2", "lbl"=> __('status_2', 'license-management') ),
                        3 =>array( "css"=>"status3", "lbl"=> __('status_3', 'license-management') ) );

        $html = "<table class='wp-list-table widefat fixed striped posts'>";
        
        $html .= "<tr>";
        $html .= "<th class='manage-column ss-list-width'>". __('Business Name', 'license-management') ."</th>";
        $html .= "<th class='manage-column ss-list-width'>". __('License Plate', 'license-management') ."</th>";
        $html .= "<th class='manage-column ss-list-width'>". __('Customer User', 'license-management') ."</th>";
        $html .= "<th class='manage-column ss-list-width'>". __('Service Name', 'license-management') ."</th>";
        $html .= "<th class='manage-column ss-list-width'>". __('Status', 'license-management') ."</th>";
        //$html .= "<th class='manage-column ss-list-width'>". __('Messages', 'license-management') ."</th>";
        $html .= "<th>&nbsp;</th>";
        $html .= "</tr>";

        if(!empty($sql_rows)){
            foreach ($sql_rows as $row) {
                $status_class_css = $status[$row->service_status]["css"];
                $status_lbl = $status[$row->service_status]["lbl"];
                $service_name = $row->service_name;

                $html .= "<tr>";
                $html .= "<td class='manage-column ss-list-width'>".$this->get_business_name($row->enterpriseid)."</td>";
                $html .= "<td class='manage-column ss-list-width'>{$row->license_plate}</td>";
                $html .= "<td class='manage-column ss-list-width'>".$this->get_user_name($row->userid)."</td>";
                $html .= "<td class='manage-column ss-list-width'>{$service_name}</td>";
                $html .= "<td class='manage-column ss-list-width'><span class='{$status_class_css}'>{$status_lbl}</span></td>";
                //$html .= "<td class='manage-column ss-list-width'>{$row->messages}</td>";

                // lm@1.2
                if(!empty($actions)){
                    $html .= "<td class='manage-column ss-list-width'>";
                    foreach($actions as $action_name){
                        if(isset($all_actions[$action_name])){
                            $eleID =  $all_actions[$action_name]["eleID"];
                            $icon = $all_actions[$action_name]["icon"];
    
                            if(isset($all_actions[$action_name]["link"])){
                                $lbl_tooltip = __('LBL_EDIT_SERVICE', 'license-management')." ".$service_name;
                                $url = admin_url('admin.php?page='.$all_actions[$action_name]["link"].'&id=' . $row->$eleID.'&licenseid='. $row->licenseid);
                                $html .= "&nbsp; <a href='".$url."' data-toggle='tooltip' data-placement='top' data-original-title='$lbl_tooltip'><i class='$icon'></i></a>";
                            }
                            elseif(isset($all_actions[$action_name]["click"])){
                                $fun = $all_actions[$action_name]["click"];
                                $id1 = $row->$eleID;
                                $id2 = $row->licenseid;
                                
                                if($action_name == "documents"){
                                    $moda_title = __('LBL_DOCUMENTS','license-management');
                                    $lbl_tooltip = __('LBL_UPLOAD_DOCUMENTS','license-management');
                                }
                                elseif($action_name == "authorization"){
                                    $moda_title = __('LBL_AUTHORIZATION','license-management');
                                    $lbl_tooltip = __('LBL_UPLOAD_AUTHORIZATION','license-management');
                                }

                                $html .= "&nbsp; <i class='$icon' data-toggle='tooltip' data-placement='top' data-original-title='$lbl_tooltip' onclick='$fun(\"$action_name\",$id1,$id2,\"\",\"$moda_title\")'></i>";
                            }
                                   
                        }
                    }
                    $html .= "</td>";
                }
                // lm@1.2e
            }
        }
        $html .= "</table>";
        
        return $html;
    }

    function get_registry_not_enterprice(){
        global $wpdb;
        $users = array();
        $table_name_enterprise = $wpdb->prefix . "license_management_enterprise";
        $table_name_users = $wpdb->prefix . "users";

        $sql = "SELECT id, user_login from $table_name_users WHERE id NOT IN (SELECT userid FROM $table_name_enterprise WHERE deleted = 0)";
        $rows = $wpdb->get_results($sql);
        if(!empty($rows)){
            $i = 0;
            foreach ($rows as $row) {
                $users[$i]["user_login"] = $row->user_login;
                $users[$i]["id"] = $row->id;
                $i++;
            }
        }
        return $users;
    }

    function get_registry_not_license(){
        global $wpdb;
        $enterprise = array();
        $table_name_license = $wpdb->prefix . "license_management_license";
        $table_name_enterprise = $wpdb->prefix . "license_management_enterprise";

        $sql = "SELECT id, business_name FROM $table_name_enterprise WHERE deleted = 0 AND id NOT IN (SELECT enterpriseid FROM $table_name_license WHERE deleted = 0)";
        $rows = $wpdb->get_results($sql);
        if(!empty($rows)){
            $i = 0;
            foreach ($rows as $row) {
                $enterprise[$i]["id"] = $row->id;
                $enterprise[$i]["business_name"] = $row->business_name;
                $i++;
            }
        }
        return $enterprise;
    }

    /*
    function license_management_get_registry_not_complete(){
        global $wpdb;
        $table_name_license = $wpdb->prefix . "license_management_license";
        $table_name_enterprise = $wpdb->prefix . "license_management_enterprise";
        $table_name_service = $wpdb->prefix . "license_management_service";

        $sql = "SELECT $table_name_license.id licenseid, $table_name_enterprise.id enterpriseid, license_plate, userid, service_status, messages, $table_name_service.id serviceid, $table_name_service.service_name
                FROM $table_name_license
                INNER JOIN $table_name_enterprise ON $table_name_enterprise.id = $table_name_license.enterpriseid
                INNER JOIN $table_name_service ON  $table_name_service.licenseid = $table_name_license.id
                WHERE $table_name_enterprise.deleted = 0 and $table_name_license.deleted = 0 AND $table_name_service.deleted = 0";

        $rows = $wpdb->get_results($sql);

    }*/

    function get_html_registry_not_complete(){
        $html = $html_icon = $link = "";
        $registry_not_complete = array();

        $not_enterprise = $this->get_registry_not_enterprice();
        $not_license = $this->get_registry_not_license();

        $registry_not_complete["enterprise"] = $not_enterprise;
        $registry_not_complete["license"] = $not_license;

        foreach($registry_not_complete as $type => $values){
            foreach($values as $value_name => $value){

                if($type == "enterprise"){
                    $link = admin_url('admin.php?page=license_management_enterprise&userid='.$value["id"]);
                    $value_view = "<span>". __('LBL_ADD_ENTERPRISE_USER', 'license-management')."</span> <br>".$value["user_login"]; // Aggiungi un'azienda all'utente:
                    $html_icon = "<i class='fas fa-user'></i>";
                }elseif($type == "license"){
                    $link = admin_url('admin.php?page=license_management_license&enterpriseid='.$value["id"]);
                    $value_view = "<span>". __('LBL_ADD_LICENSE_ENTERPRISE', 'license-management')."</span> <br>".$value["business_name"]; // Aggiungi una targa all'azienda:
                    $html_icon = "<i class='fas fa-truck-moving'></i>";
                }

                $html .= "<div class='col-lg-4 col-md-6 col-sm-6 col-xs-12'>";
                $html .= "<div class='container-not-complete'>";
                $html .= "<a href='$link'>";
                $html .= "<div class='col-xs-4'>";
                $html .= $html_icon;
                $html .= "</div>";
                $html .= "<div class='col-xs-8'>";
                $html .= $value_view;
                $html .= "</div>";
                $html .= "</a>";
                $html .= "</div>";
                $html .= "</div>";
            }
        }

        return $html;
    }

    function get_enteprise_user($sql_rows){
        $enteprise_user = array();
        if(!empty($sql_rows)){
            
            foreach($sql_rows as $obj){
                $enterpriseid = $obj->enterpriseid;
                $enteprise_user[$enterpriseid]["business_name"] = $obj->business_name;
                $enteprise_user[$enterpriseid]["userid"] = $obj->userid;
            }
        }
        return $enteprise_user;
    }

    function get_html_dropdown($params){
        /* 
            params : entity actions[ label, link ]
        */
        $html = "";
        if(!empty($params)){
            $dropdown_id = $params["entity"];
            $html .= "<div class='dropdown'>
                    <button id='d$dropdown_id' type='button' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
                        <span class='caret'></span>
                    </button>
                    <ul class='dropdown-menu' aria-labelledby='d$dropdown_id'>";
                    foreach($params["actions"] as $action_values){
                        $link_action = $action_values["link"];
                        $lable_action = $action_values["label"];
                        $html .= "<li><a href='$link_action'>$lable_action</a></li>";
                    }
                    
            $html .=" </ul></div>";
        }
        return $html;
    }

    function get_html_enteprise_user($record,$sql_rows = ""){
        $html = "";

        if(empty($record) && !empty($sql_rows)){
            $record = $this->get_enteprise_user($sql_rows);
        }

        if(!empty($record)){
            $link_add_enterprise = admin_url('admin.php?page=license_management_enterprise');

            $title_table = __('LBL_ADD_ENTERPRISE_ONE_LICENSE', 'license-management'); // "Aziende con almeno una targa";
            $params = array("entity"=>"enterprise","actions"=>array(array("label"=>__('LBL_CREATE_ENTERPRISE', 'license-management'),"link"=>$link_add_enterprise))); // Crea una Azienda
            $html .= $this->get_table_header($params,$title_table);

            $html .= "<table class='wp-list-table widefat fixed striped posts'>";
            $html .= "<tr>";
            $html .= " <th class='manage-column ss-list-width'>".__('Business Name', 'license-management')."</th>";
            $html .= "<th class='manage-column ss-list-width'>".__('User Name', 'license-management')."</th>";
            $html .= "<th class='manage-column ss-list-width'></th>";
            $html .= "</tr>";
            foreach($record as $enterpriseid => $value){
                $business_name = $value["business_name"];
                $userid = $this->get_user_name($value["userid"]);

                $params = array("id"=>$enterpriseid,"label"=>$business_name);
                $action_add_license = $this->get_actions_table("add_license_plate",$params);

                $link_edit = admin_url('admin.php?page=license_management_enterprise&id='.$enterpriseid);
                $action_edit = "<a href='$link_edit'><i class='fas fa-pen'></i></a>";

                $html .= "<tr>";
                $html .= "<td class='manage-column ss-list-width'>$business_name</td>";
                $html .= "<td class='manage-column ss-list-width'>$userid</td>";
                $html .= "<td class='manage-column ss-list-width'>$action_edit $action_add_license</td>";
                $html .= "</tr>";
            }
            $html .= "</table>";
        }

        return $html;
    }

    function get_table_header($params_dropdown,$title_table){
        $html = "<div class='row table-header'>";
        $html .= "<div class='col-xs-6'>";

        $html .= $this->get_html_dropdown($params_dropdown);
    
        $html .= "</div>";
        $html .= "<div class='col-xs-6 table-title'>$title_table</div>";
        $html .= "</div>";

        return $html;
    }

    function get_actions_table($type,$params,$only_url = false){
        /* 
            params : label, id
        */
        $html = "";

        if(!empty($type)){
            $id = $params["id"];
            $label = $params["label"];

            if($type == "add_license_plate"){
                $link_add_license = admin_url('admin.php?page=license_management_license&enterpriseid='.$id);
                $value_view = __('LBL_ADD_LICENSE', 'license-management')." ".$label; // Aggiungi una targa per:
                $html_icon = "<i class='fas fa-truck-moving'></i>";
                $html = "<a href='$link_add_license' data-toggle='tooltip' data-placement='top' title='$value_view'>$html_icon</a>";
            }
            elseif($type == "edit_license_plate"){
                $link_edit_license = admin_url('admin.php?page=license_management_license&id='.$id);
                $value_view = __('LBL_EDIT_LICENSE', 'license-management')." ".$label; // "Modifica la targa "
                $html_icon = "<i class='fas fa-pen'></i>";
                $html = "<a href='$link_edit_license' data-toggle='tooltip' data-placement='top' title='$value_view'>$html_icon</a>";
            }
            elseif($type == "edit_service_lbl"){
                $title = __('LBL_EDIT_SERVICE', 'license-management');
                $html = "<i onclick=\"license_management_modal('".$type."',$id,'')\" data-toggle='tooltip' data-placement='bottom' title='$title' class='fas fa-pen'></i>";
            }elseif($type == "edit_service"){
                $licenseid = $params["licenseid"];
                $link_edit_service = admin_url('admin.php?page=license_management_service&id='.$id.'&licenseid='.$licenseid);
                if(!$only_url){
                    $value_view = __('LBL_EDIT_SERVICE', 'license-management')." ".$label;  // "Modifica servizio "
                    $html_icon = "<i class='fas fa-pen'></i>";
                    $html = "<a href='$link_edit_service' data-toggle='tooltip' data-placement='top' title='$value_view'>$html_icon</a>";
                }else{
                    $html = $link_edit_service;
                }
            }elseif($type == "edit_enterprise"){
                $link = admin_url('admin.php?page=license_management_enterprise&id='.$id);
                $value_view = __('LBL_EDIT_ENTERPRISE', 'license-management')." ".$label;  // "Modifica servizio "
                $html_icon = "<i class='fas fa-pen'></i>";
                $html = "<a href='$link' data-toggle='tooltip' data-placement='top' title='$value_view'>$html_icon</a>";
            }elseif($type == "add_messages"){
                $licenseid = $params["licenseid"];
                $service_message = $params["service_message"];
                $value_view = __('LBL_NEW_COMMENT', 'license-management')." ".$label;
                $html_id = $type."_".$id."_".$licenseid;
                $html_badge = "";
                if(!empty($service_message)){ $html_badge = "<span class='badge'>1</span>"; }
                $html = "<i id='$html_id' onclick=\"license_management_modal('add_messages',$id,$licenseid)\" data-toggle='tooltip' data-placement='bottom' title='$service_message' class='fas fa-comment'>$html_badge</i>";
            }elseif($type == "edit_tmp_messages"){
                $value_view = $label;
                $tab_name = $params["tab_name"];
                $html = "<a href='#edit_tmp_messages$id' aria-controls='edit_tmp_messages$id' role='tab' data-toggle='tab'><i class='fas fa-pen'></i></a>";
            }
        }

        return $html;
    }

    function get_enteprise_license($sql_rows){
        $enteprise_license = array();
        if(!empty($sql_rows)){
            
            foreach($sql_rows as $obj){
                $enterpriseid = $obj->enterpriseid;
                $enteprise_license[$enterpriseid]["business_name"] = $obj->business_name;
                $enteprise_license[$enterpriseid]["license_plate"][$obj->licenseid]["license_plate"] = $obj->license_plate;
                $enteprise_license[$enterpriseid]["license_plate"][$obj->licenseid]["messages_license"] = $obj->messages_license;
                $enteprise_license[$enterpriseid]["userid"] = $obj->userid;
            }
        }
        return $enteprise_license;
    }

    function get_html_enteprise_license($record,$sql_rows = ""){
        $html = "";

        if(empty($record) && !empty($sql_rows)){
            $record = $this->get_enteprise_license($sql_rows);
        }
        if(!empty($record)){

            $title_table = __('LBL_LICENSE_OF_ENTERPRISE', 'license-management'); // "Le targhe delle aziende";
            $link_add_license = admin_url('admin.php?page=license_management_license');
            $params = array("entity"=>"enterprise","actions"=>array(array("label"=>__('LBL_CREATE_LICENSE', 'license-management'),"link"=>$link_add_license)));
            $html .= $this->get_table_header($params,$title_table);

            $html .= "<table class='wp-list-table widefat fixed striped posts'>";
            $html .= "<tr>";
            $html .= "<th class='manage-column ss-list-width'>".__('Business Name', 'license-management')."</th>";
            $html .= "<th class='manage-column ss-list-width'>".__('User Name', 'license-management')."</th>";
            $html .= "<th class='manage-column ss-list-width'>".__('License Plate', 'license-management')."</th>";
            $html .= "<th class='manage-column ss-list-width'>".__('Messages', 'license-management')."</th>";
            $html .= "<th class='manage-column ss-list-width'></th>";
            $html .= "</tr>";

            foreach($record as $enterpriseid => $value){
                $business_name = $value["business_name"];
                $userid = $this->get_user_name($value["userid"]);
                $license_plate_values = $value["license_plate"];

                $html .= "<tr>";
                $html .= "<td class='manage-column ss-list-width'>$business_name</td>";
                $html .= "<td class='manage-column ss-list-width'>$userid</td>";
                $html .= "<td class='manage-column ss-list-width' colspan='3'><table>";
                    foreach($license_plate_values as $licenseid=>$license_plate_val){
                        $license_plate = $license_plate_val["license_plate"];
                        $messages_license = $license_plate_val["messages_license"];

                        $params = array("id"=>$licenseid,"label"=>$license_plate);
                        $add_license_plate = $this->get_actions_table("edit_license_plate",$params);

                        $html .= "<tr>";
                        $html .= "<td>$license_plate</td>";
                        $html .= "<td>$messages_license</td>";
                        $html .= "<td>$add_license_plate</td>";
                        $html .= "</tr>";
                    }
                $html .= "</table></td>";
                $html .= "</tr>";

            }

            $html .= "</table>";
        }

        return $html;
    }

    function get_service_license($sql_rows){
        $license_service = array();
        if(!empty($sql_rows)){
            
            foreach($sql_rows as $obj){
                $serviceid = $obj->serviceid;
                $license_service[$serviceid]["service_name"] = $obj->service_name;
                $license_service[$serviceid]["users"][$obj->userid][$obj->licenseid][] = $obj->service_status;
                $license_service[$serviceid]["users"][$obj->userid][$obj->licenseid][] = $obj->license_plate;
                $license_service[$serviceid]["users"][$obj->userid][$obj->licenseid][] = $obj->date_end;
            }
        }
        //sort($license_service);
        return $license_service;
    }

    function get_license_service($sql_rows){
        $license_service = array();
        if(!empty($sql_rows)){
            
            foreach($sql_rows as $obj){
                $licenseid = $obj->licenseid;
                $license_service[$licenseid]["license_plate"] = $obj->license_plate;
                $license_service[$licenseid]["services"][$obj->serviceid][] = $obj->service_status;
                $license_service[$licenseid]["services"][$obj->serviceid][] = $obj->service_name;
                $license_service[$licenseid]["services"][$obj->serviceid][] = $obj->date_end;
                $license_service[$licenseid]["services"][$obj->serviceid][] = $obj->messages_service;
                $license_service[$licenseid]["services"][$obj->serviceid][] = $obj->createduserid;
                $license_service[$licenseid]["userid"] = $obj->userid;
                $license_service[$licenseid]["licenseid"] = $obj->licenseid;
            }
        }
        return $license_service;
    }

    function get_html_license_service($record,$sql_rows = ""){
        $trans_status = array("1"=>__('status_1', 'license-management'),"2"=>__('status_2', 'license-management'),"3"=>__('status_3', 'license-management'));

        $html = "";

        if(empty($record) && !empty($sql_rows)){
            $record = $this->get_license_service($sql_rows);
        }

        if(!empty($record)){

            $title_table = __('LBL_SERVICE_OF_LICENSE', 'license-management'); // "I servizi delle targhe";
            $link_add_service = admin_url('admin.php?page=license_management_service');
            $params = array("entity"=>"service","actions"=>array(array("label"=>__('LBL_CREATE_SERVICE', 'license-management'),"link"=>$link_add_service)));
            $html .= $this->get_table_header($params,$title_table);

            $html .= "<table class='wp-list-table widefat fixed striped posts'>";
            $html .= "<tr>";
            $html .= "<th class='manage-column ss-list-width'>".__('License Plate', 'license-management')."</th>";
            $html .= "<th class='manage-column ss-list-width'>".__('User Name', 'license-management')."</th>";
            $html .= "<th class='manage-column ss-list-width'>".__('Services', 'license-management')."</th>";
            $html .= "<th class='manage-column ss-list-width'>".__('Date End', 'license-management')."</th>";
            $html .= "<th class='manage-column ss-list-width'>".__('Services Status', 'license-management')."</th>";
            $html .= "<th class='manage-column ss-list-width'></th>";
            $html .= "</tr>";
        
            foreach($record as $licenseid => $value){
                $license_plate = $value["license_plate"];
                $userid = $this->get_user_name($value["userid"]);
                $services = $value["services"];

                $html .= "<tr>";
                $html .= "<td class='manage-column ss-list-width'>$license_plate</td>";
                $html .= "<td class='manage-column ss-list-width'>$userid</td>";
                $html .= "<td class='manage-column ss-list-width' colspan='4'><table>";

                foreach($services as $serviceid=>$service_val){
                    $servce_class = array("1"=>"status1","2"=>"status2","3"=>"status3");
                    $service_name = $service_val[1];
                    $service_status = $service_val[0];
                    $date_end = $service_val[2];
                    $service_message = $service_val[3];
                    if(!empty($date_end)){
                        if($date_end == "0000-00-00"){ $date_end = ""; 
                        }else{ $date_end = date("d-m-Y", strtotime($date_end)); }
                    }
                    $params = array("id"=>$serviceid,"label"=>$service_name,"licenseid"=>$licenseid,"service_message"=>$service_message);
                    $edit_service = $this->get_actions_table("edit_service",$params);
                    $add_messages = $this->get_actions_table("add_messages",$params);

                    $html .= "<tr>";
                    $html .= "<td>$service_name</td>";
                    $html .= "<td>$date_end</td>";
                    $html .= "<td><span class='$servce_class[$service_status]'>$trans_status[$service_status]</span></td>";
                    $html .= "<td>$edit_service $add_messages</td>";
                    $html .= "</tr>";
                }

                $html .= "</table></td>";
                $html .= "</tr>";
            }

            $html .= "</table>";
        }

        return $html;
    }

    function get_html_service_license($record,$sql_rows = ""){
        $trans_status = array("1"=>__('status_1', 'license-management'),"2"=>__('status_2', 'license-management'),"3"=>__('status_3', 'license-management'));

        $html = "";

        if(empty($record) && !empty($sql_rows)){
            $record = $this->get_service_license($sql_rows);
        }

        if(!empty($record)){

            $title_table = __('LBL_LICENSE_OF_SERVICE', 'license-management'); // "Quali sono le targhe del servizio";
            $link_add_service = admin_url('admin.php?page=license_management_service');
            $params = array("entity"=>"service","actions"=>array(array("label"=>__('LBL_CREATE_SERVICE', 'license-management'),"link"=>$link_add_service)));
            $html .= $this->get_table_header($params,$title_table);

            $html .= "<table class='wp-list-table widefat fixed striped posts'>";
            $html .= "<tr>";
            $html .= "<th class='manage-column ss-list-width'>".__('Services', 'license-management')."</th>";
            $html .= "<th class='manage-column ss-list-width'>".__('User Name', 'license-management')."</th>";
            $html .= "<th class='manage-column ss-list-width'>".__('License Plate', 'license-management')."</th>";
            $html .= "<th class='manage-column ss-list-width'>".__('Date End', 'license-management')."</th>";
            $html .= "<th class='manage-column ss-list-width'>".__('Services Status', 'license-management')."</th>";
            $html .= "<th class='manage-column ss-list-width'></th>";
            $html .= "</tr>";

            foreach($record as $serviceid => $value){
                $service_name = $value["service_name"];
                $users = $value["users"];

                $html .= "<tr>";
                $html .= "<td class='manage-column ss-list-width'>$service_name</td>";

                $i = 0;
                foreach($users as $userid=>$users_block){
                    if($i > 0){ $html .= "<td class='manage-column ss-list-width'></td>"; }
                    $user_name = $this->get_user_name($userid);
                    $html .= "<td class='manage-column ss-list-width'>$user_name</td>";
                    $html .= "<td class='manage-column ss-list-width' colspan='4'><table>";

                    foreach($users_block as $licenseid=>$license_val){
                        $servce_class = array("1"=>"status1","2"=>"status2","3"=>"status3");
                        $service_status = $license_val[0];
                        $license_plate = $license_val[1];
                        $date_end = $license_val[2];

                        if(!empty($date_end)){
                            if($date_end == "0000-00-00"){ $date_end = ""; 
                            }else{ $date_end = date("d-m-Y", strtotime($date_end)); }
                        }
                        $params = array("id"=>$serviceid,"label"=>$service_name,"licenseid"=>$licenseid);
                        $edit_service = $this->get_actions_table("edit_service",$params);
                        
                        $html .= "<tr>";
                        $html .= "<td>$license_plate</td>";
                        $html .= "<td>$date_end</td>";
                        $html .= "<td><span class='$servce_class[$service_status]'>$trans_status[$service_status]</span></td>";
                        $html .= "<td>$edit_service</td>";
                        $html .= "</tr>";
                    }
                    $html .= "</table></td>";
                    $html .= "</tr>";
                    $i++;
                }
            }

            $html .= "</table>";
        }

        return $html;
    }

    function get_num_for_service($sql_rows){
        $trans_status = array("1"=>__('status_1', 'license-management'),"2"=>__('status_2', 'license-management'),"3"=>__('status_3', 'license-management'));
        $result = array();

        if(!empty($sql_rows)){
            
            foreach($sql_rows as $obj){
                $service_name = $obj->service_name;
                //$service_name = str_replace("'","\'\'",$service_name);
                $service_status = $obj->service_status;
                if(!isset($result[$service_name])){
                    foreach($trans_status as $status=>$lbl_status){
                        $result[$service_name][$status] = 0;
                    }
                }
                $result[$service_name][$service_status] += 1; 
            }
        }
        return json_encode($result, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);     
    }
    function get_num_for_analytics(){
        $current_month = date("m");
        $result = array(1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0,8=>0,9=>0,10=>0,11=>0,12=>0);

        
        if(!empty($changelog)){
            $result = $this->get_data_chart_license_change_status($changelog);
            $result[$current_month] =  $this->get_data_chart_license_change_status($this->get_month_changelog($current_month));
        }
        return $result;
    }
    function get_data_chart_license_change_status($status){
        $current_month = date("m");
        $result = array(1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0,8=>0,9=>0,10=>0,11=>0,12=>0);
        $changelog = $this->get_month_changelog();

        if(!empty($changelog)){
            foreach($changelog as $changelog_values){
                $month = date("n",strtotime($changelog_values->createdtime));
                $changelog_decode = json_decode($changelog_values->changelog);
                if($changelog_decode->service_status == $status){
                    $result[$month] += 1;
                }
            }
        }

        $result[$month] += $this->get_num_service_status($status);
        
        return json_encode($result);
    }
    function get_month_changelog($month = ""){
        global $wpdb;
        $current_year = date("Y");
        $table_name = $wpdb->prefix . "license_management_changelog";

        $sql = "SELECT createdtime, changelog FROM $table_name WHERE YEAR(createdtime) = $current_year AND entity = 'service'";
        if(!empty($month)){
            $sql .= " AND MONTH($month) ";
        }
        $rows = $wpdb->get_results($sql);
        return $rows;
    }
    function get_service_group_status_for_month($status,$sql_rows){
        $current_year = date("Y");
        $result = array(1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0,8=>0,9=>0,10=>0,11=>0,12=>0);
        if(!empty($sql_rows)){
            foreach($sql_rows as $entity=>$entity_val){
                $service_status = $entity_val->service_status;
                $date_end = $entity_val->date_end;
                if($service_status == $status && !empty($date_end) && $date_end != '0000-00-00' && date("Y",strtotime($date_end)) == $current_year){
                    $month = date("n",strtotime($date_end));
                    $result[$month] += 1;
                }
            }
        }
        return json_encode($result);
    }
    function get_num_service_status($status){
        global $wpdb;
        $current_year = date("Y");
        $table_name = $wpdb->prefix . "license_management_service";

        if(!empty($status)){
            $sql = "SELECT count(id) as tot FROM $table_name WHERE YEAR(createdtime) = $current_year AND service_status = '{$status}' ";
            $rows = $wpdb->get_results($sql);
            if(!empty($rows) && isset($rows[0]->tot)){
                return $rows[0]->tot;
            }
        }
        return 0;
    }

    function get_user_name($userid){

        $user_value = get_user_meta($userid);
        $user_name = "";
                
        if(!empty($user_value["last_name"][0]) && !empty($user_value["last_name"][0])){
            $user_name = $user_value["last_name"][0]." ".$user_value["first_name"][0];
        }elseif(isset($user_value["nickname"][0])){
            $user_name = $user_value["nickname"][0];
        }

        return $user_name;
    }

    function get_business_name($enterpriseid){
        $business_name = "";

        if(!empty($enterpriseid) && $enterpriseid > 0){
            global $wpdb;
            $table_name_enterprise = $wpdb->prefix . "license_management_enterprise";

            $sql = "SELECT business_name FROM $table_name_enterprise WHERE deleted = 0 AND id = $enterpriseid";
            $rows = $wpdb->get_results($sql);
            if( isset($rows[0]->business_name) && !empty($rows[0]->business_name) ) {
            $business_name = $rows[0]->business_name;
            }
        }

        return $business_name;
    }
    function list_sql_search($params){
        global $wpdb;

        $sql = "";
        $table_name_service = $wpdb->prefix . "license_management_service";
        $table_name_enterprise = $wpdb->prefix . "license_management_enterprise";
        $table_name_license = $wpdb->prefix . "license_management_license";

        if(isset($_REQUEST["sc_service_name"]) && !empty($_REQUEST["sc_service_name"])){
            $service_name = $_REQUEST["sc_service_name"];
            $sql .= " AND $table_name_service.service_name LIKE '%".$service_name."%'";
        }
        if(isset($_REQUEST["sc_business_name"]) && !empty($_REQUEST["sc_business_name"])){
            $business_name = $_REQUEST["sc_business_name"];
            $sql .= " AND $table_name_enterprise.business_name LIKE '%".$business_name."%'";
        }
        if(isset($_REQUEST["sc_license_plate"]) && !empty($_REQUEST["sc_license_plate"])){
            $license_plate = $_REQUEST["sc_license_plate"];
            $sql .= " AND $table_name_license.license_plate LIKE '%".$license_plate."%'";
        }
        return $sql;
    }
    function get_token_search(){
        $token = array();
        $field_search = array("sc_service_name","sc_business_name","sc_license_plate");
        foreach($field_search as $key=>$fieldname){
            if(isset($_REQUEST[$fieldname]) && !empty($_REQUEST[$fieldname])){
                $token[$key]["name"] = $fieldname;
                $token[$key]["value"] = $_REQUEST[$fieldname];
            }
        }
        return $token;
    }
    function generate_search_token_html(){
        $html = "";
        $token = $this->get_token_search();
        if(!empty($token)){
            foreach($token as $key=>$token_val){
                $fieldname = $token_val["name"];
                $fieldvalue = $token_val["value"];
                $html .=  '<div class="token">
                            <span class="token-label"> '.$fieldvalue.'</span>
                            <span class="close" onclick="license_management_removeTagUrl(\''.$fieldname.'\')" data-toggle="tooltip" data-placement="top" data-original-title="">
                            <span class="fa fa-times"></span></span>
                        </div>';
            }
        }
        return $html;
    }
    function get_date_end_html($entity){
        $html = "";
        $html_icon = "<i class='far fa-clock'></i>";
        $html = "<div class='row'><div class='col-xs-12'>
                <h5 class='text-right title-block'>".__('lbl_datend_next_month', 'license-management')."
                <i class='fa fa-info-circle' title='' data-toggle='tooltip' data-placement='top' data-original-title='".__('lbl_formula_datend_next_month', 'license-management')."'
                ></i></h5></div></div>";
        if(!empty($entity)){
            $entity = license_management_get_date_end($entity);
            foreach($entity as $date_end => $entity_val){
                foreach($entity_val as $value){

                    $serviceid = $value["serviceid"];
                    $licenseid = $value["licenseid"];
                    $messages_service = $value["messages_service"];
                    $service_status = $value["service_status"];

                    if($service_status != 2){
                        $params_tb = array("id"=>$serviceid,"label"=>"","licenseid"=>$licenseid,"service_message"=>"");

                        $link = $this->get_actions_table("edit_service",$params_tb,true);
                        $service_name = $value["service_name"];
                        $license_plate = $value["license_plate"];
                        $business_name = $value["business_name"];
                        $html .= "<div class='col-xs-6'>";
                        $html .= "<div class='badge-alert'>";
                        $html .= "<a href='$link'>".$html_icon."</a>";
                        $html .= "</div>";
                        $html .= "<div class='container-dateend container-fluid'>";
                        //$html .= "<a href='$link'>";
                        $html .= "<div class='col-xs-12'>";
                        $html .= "<div class='col-xs-12'>";
                        $html .= "<h4>".date("d-m-Y",strtotime($date_end))."</h4>";
                        $html .= "</div>";
                        $html .= "<div class='col-xs-12'>";
                        $html .= "<h4>".$business_name."</h4>";
                        $html .= "</div>";
                        $html .= "</div>";
                        $html .= "<div class='col-xs-12'>";
                        $html .= "<div class='col-xs-12'>";
                        $html .= "<h4>".$license_plate."</h4>";
                        $html .= "</div>";
                        $html .= "<div class='col-xs-12'>";
                        $html .= "<h4>".$service_name."</h4>";
                        $html .= "</div>";
                        $html .= "</div>";
                        //$html .= "</a>";
                        $html .= "</div>";
                        $html .= "</div>";
                    }
                }
            }
        }
        return $html;
    }
    function get_user_html($userid = "",$mandatory = false){
        $html = $selected = "";
        $users = get_users( array( 'fields' => array( 'ID' ) ) );

        if(!empty($users)){
            if($mandatory){
                $html = "<select required name='userid' class='form-control'>";
            }else{
                $html = "<select name='userid' class='form-control'>";
            }
            foreach($users as $user_id){
                $selected = "";
                $user_value = get_user_meta ( $user_id->ID);
                
                if(!empty($user_value["last_name"][0]) && !empty($user_value["last_name"][0])){
                    $user_name = $user_value["last_name"][0]." ".$user_value["first_name"][0];
                }else{
                    $user_name = $user_value["nickname"][0];
                }

                if(!empty($userid) && $userid == $user_id->ID){
                    $selected = "selected";
                }
                $html .= "<option $selected value='$user_id->ID'> $user_name </option>";
            } 
            $html .= "</select>";
        }
        return $html;
    }
    /* TODO
    function send_mail($from,$to,$cc='',$subject,$body){    
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);        // Passing `true` enables exceptions
        
        try {
            //Server settings
            $mail->SMTPDebug = 0;                               // Enable verbose debug output
            $mail->isSMTP();                                    // Set mailer to use SMTP
            $mail->Host = 'smtp.gmail.com';  					// Specify main and backup SMTP servers
            $mail->SMTPAuth = true;                             // Enable SMTP authentication
            $mail->Username = '';                               // SMTP username
            $mail->Password = '';                               // SMTP password
        //	$mail->SMTPSecure = 'tls';                          // Enable TLS encryption, `ssl` also accepted
        //	$mail->Port = 587;                                  // TCP port to connect to
        
            $mail->SMTPSecure = 'ssl';                          // Enable TLS encryption, `ssl` also accepted
            $mail->Port = 465;                                  // TCP port to connect to
        
            //$mail->SMTPOptions = array(
            //	'ssl' => array(
            //		'verify_peer' => false,
            //		'verify_peer_name' => false,
            //		'allow_self_signed' => true
            //	)
            // );

            //Recipients
            $mail->setFrom($from, 'TEST');
            $mail->addAddress($to, '');     // Add a recipient
        //	$mail->addAddress('ellen@example.com');               // Name is optional
        //	$mail->addReplyTo('info@example.com', 'Information');
        //	$mail->addCC('cc@example.com');
        //	$mail->addBCC('bcc@example.com');

            //Attachments
        //	$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
        //	$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

            //Content
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject =  $subject;
            $mail->Body    = $body;
            //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

            $mail->send();
            //echo 'Message has been sent';
            return true;
        } catch (Exception $e) {
            //echo 'Message could not be sent.';
            //echo 'Mailer Error: ' . $mail->ErrorInfo;
            return false;
        }
    }
    */
}
?>