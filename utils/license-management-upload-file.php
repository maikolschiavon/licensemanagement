<?php 
// lm@1.2

class license_management_upload_file{

    public function get_table_name(){
        global $wpdb;
        $table_name = $wpdb->prefix . "license_management_upload_file";
        return $table_name;
    }

    function get_upload_file($serviceid,$licenseid,$fileid = 0,$type = ""){
        global $wpdb;
        
        $file = array();

        $sql = "SELECT id, file, send_mail, createdtime, type FROM wp_license_management_upload_file WHERE deleted = 0";
        if(!empty($type)){
            $sql .= " AND type = '$type' ";
        }

        if($fileid > 0){
            $sql .= " AND id = $fileid ";
        }
        else{
            $sql .= " AND serviceid = $serviceid AND licenseid = $licenseid ORDER BY createdtime";
        }
        
        $rows = $wpdb->get_results($sql);
        if(!empty($rows)){
            foreach($rows as $file_values){
                $fileid = $file_values->id;
                $send_mail = $file_values->send_mail;
                $createdtime = $file_values->createdtime;
                $directory_file = $file_values->file;
                $type = $file_values->type;
                $filename = basename($directory_file);

                $file[$fileid]["directory_file"] = $directory_file;
                $file[$fileid]["filename"] = $filename;
                $file[$fileid]["send_mail"] = $send_mail; 
                $file[$fileid]["createdtime"] = $createdtime; 
                $file[$fileid]["serviceid"] = $serviceid;
                $file[$fileid]["licenseid"] = $licenseid;
                $file[$fileid]["type"] = $type;
            }
        }

        return $file;
    }

    function get_file_html($fileid,$params){

        $filename = $params["filename"];
        $directory_file = $params["directory_file"];
        $createdtime = $params["createdtime"];
        $serviceid = $params["serviceid"];
        $licenseid = $params["licenseid"];
        $send_mail = $params["send_mail"];
        $type = $params["type"];

        $createdtime = date('d-m-Y H:i:s', strtotime($createdtime));
        $lbl_send_mail =  __('LBL_EMAIL_WAS_NOT_SEND','license-management');
        if($send_mail){ $lbl_send_mail =  __('LBL_EMAIL_WAS_SEND','license-management');}
        if($type == "documents"){
            $lbl_confirm =  __('LBL_CONFIRM_DELETE_DOC','license-management');
        }
        elseif($type == "authorization"){
            $lbl_confirm =  __('LBL_CONFIRM_DELETE_AUTH','license-management');
        }
        $lbl_delete = __('LBL_DELETE', 'license-management');

        $html = '<div class="row" id="file-'.$fileid.'">
                <div class="col-xs-8"> 
                    <div class="col-xs-10"><a href="'.$directory_file.'" target="_blank"><i class="fas fa-file"></i> '.$filename.'</a></div>
                    <div class="col-xs-12"><i class="far fa-envelope"></i> <span>'.$lbl_send_mail.'</span></div>
                    <div class="col-xs-12"><span class="help-block">'.$createdtime.'</span></div>
                </div>
                <div class="col-xs-4">
                    <input type="button" name="delete" value="'.$lbl_delete.'" class="btn btn-dft-license-delete" onclick="confirm(\''.$lbl_confirm.'?\') && license_management_delete_upload_file('.$fileid.',\''.$type.'\');">
                </div></div>';

        return $html;
    }

    function get_form($serviceid,$licenseid,$type){
        $html = "";
        $css_upload_file = "";

        $files = $this->get_upload_file($serviceid,$licenseid,0,$type);

        if(!empty($files)){
            if($type == "authorization"){
                $css_upload_file = "hidden";
            }

            $html .= "<div class='container-fluid'>";

            foreach($files as $fileid=>$file_values){
                $html .= $this->get_file_html($fileid,$file_values);
            }
            
            $html .= "</div>";

            if($type != "authorization"){
                $html .= " <hr>";
            }
        }

        $lbl_upload_file = __('LBL_UPLOAD_FILE','license-management');
        $lbl_send_mail = __('LBL_EMAIL_SEND','license-management');
        $lbl_submit = __('LBL_SUMBIT','license-management');

        $html .= "<div id='container-upload-file' class='container-fluid $css_upload_file'>
                    <form method='post' action='' name='myform' enctype='multipart/form-data'>
                        <input type='hidden' name='type' value='$type'>
                        <input type='hidden' name='serviceid' value='$serviceid'>
                        <input type='hidden' name='licenseid' value='$licenseid'>
                        <div class='form-group'>
                            <label for='uploadFormControlFile1'>$lbl_upload_file</label>
                            <input type='file' name='file' class='form-control-file' id='uploadFormControlFile1'>
                        </div>
                        <div class='form-group'>
                            <label>$lbl_send_mail</label>
                            <label class='switch'>
                                <input type='checkbox' name='mail' checked=''>
                                <span class='slider round'></span>
                            </label>
                        </div>
                        <div class='form-group'>
                            <input type='submit' class='btn btn-dft-license-save' name='but_submit' value='$lbl_submit'>
                        </div>
                    </form>
                </div>";

        return $html;
    }

    function save($serviceid,$licenseid,$file_directory,$send_mail="",$type=""){
        global $wpdb;
        
        $send = 0;
        $now = date("Y-m-d H:i:s");

        $table_name = $this->get_table_name();

        $file_explode = explode($_SERVER['DOCUMENT_ROOT'],$file_directory);
        $file = $file_explode[1];

        if($send_mail == "on"){
            $params = array("licenseid"=>$licenseid,"serviceid"=>$serviceid,"type"=>$type);
            $send = $this->send_mail($params);
        }

        $wpdb->insert(
            $table_name, //table
            array('serviceid' => $serviceid, 'licenseid' => $licenseid, 'createdtime' => $now, 'deleted' => 0, 'file' => $file, 'type' => $type, 'send_mail' => $send), //data
            array('%s', '%s') //data format
        );

        $entityid = $wpdb->insert_id;

        return $entityid;
    }

    function delete($fileid){
        global $wpdb;

        $now = date("Y-m-d H:i:s");

        $table_name = $this->get_table_name();

        $file = $this->get_upload_file(0,0,$fileid);
        
        if(isset($file[$fileid])){
            $directory_file = $_SERVER['DOCUMENT_ROOT'].$file[$fileid]["directory_file"];

            wp_delete_file( $directory_file );

            $wpdb->query($wpdb->prepare("UPDATE $table_name SET deleted = 1, deletedtime = '$now' WHERE id = %s", $fileid));
        }
    }

    function send_mail($params){
        $licenseid = $params["licenseid"];
        $serviceid = $params["serviceid"];
        $type = $params["type"];
        
        $license = license_management_retrive($licenseid,"license");
        $service = license_management_retrive($serviceid,"service");

        if(count($license) == 1){
            $site_url = get_site_url();
            $license_plate = $license[0]->license_plate;
            $enterpriseid = $license[0]->enterpriseid;
            $service_name = $service[0]->service_name;

            if($enterpriseid > 0){
                $enterprise = license_management_retrive($enterpriseid,"enterprise");
                
                if(count($enterprise) == 1){
                    $userid = $enterprise[0]->userid;
                    $user_info = get_userdata($userid);
                    $to = $user_info->user_email;

                    if($type == "documents"){
                        $subject = __('SUBJECT_EMAIL_DOWNLOAD_FILE','license-management');
                        $body = __('BODY_EMAIL_DOWNLOAD_FILE','license-management');
                        $body = str_replace('LICENSE_PLATE',$license_plate,$body);
                        $body = str_replace('SERVICE_NAME',$service_name,$body);
                        $body = str_replace('LINK',"<a href='$site_url/i-tuoi-mezzi?action=detail'>".__('HERE','license-management')."</a>",$body);
                    }
                    elseif($type == "authorization"){
                        $subject = __('SUBJECT_EMAIL_DOWNLOAD_AUTHORIZATION','license-management');

                        $body = __('BODY_EMAIL_DOWNLOAD_AUTHORIZATION','license-management');
                        $body = str_replace('LICENSE_PLATE',$license_plate,$body);
                        $body = str_replace('SERVICE_NAME',$service_name,$body);
                        $body = str_replace('LINK',"<a href='$site_url/i-tuoi-mezzi?status=3&action=detail'>".__('HERE','license-management')."</a>",$body);
                    }

                    add_filter('wp_mail_content_type', function( $content_type ) {
                        return 'text/html';
                    });

                    $headers = $attachments = array();
                    return wp_mail($to, $subject, $body, $headers, $attachments );
                }
            }
        }
    }
}

?>