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

/* CREATE BOLT */
jQuery( document ).ready(function($) {
    jQuery('#collapseBoltCreate2').on('shown.bs.collapse', function (id) {
        if(jQuery('#collapseBoltCreate2 .panel-group').find('.collapse.in').length > 0){

           var collapse_id = jQuery('#collapseBoltCreate2 .panel-group').find('.collapse.in')[0].id;
            // This does the ajax request
            $.ajax({
                url: ajaxurl, // or example_ajax_obj.ajaxurl if using on frontend
                data: {
                    'action': 'license_management_request',
                    'collapse': collapse_id
                },
                success:function(data) {
                    // This outputs the result of the ajax request
                    jQuery("#"+collapse_id+" .panel-body").html(data);
                },
                error: function(errorThrown){
                    console.log(errorThrown);
                }
            });  
        }

    });
    jQuery('#collapseLicenseCreate').on('shown.bs.collapse', function (id) {
        if(jQuery('#collapseLicenseCreate').hasClass('in')){
            $.ajax({
                url: ajaxurl, // or example_ajax_obj.ajaxurl if using on frontend
                data: {
                    'action': 'license_management_request',
                    'collapse': 'collapseLicenseCreate'
                },
                success:function(data) {
                    // This outputs the result of the ajax request
                    jQuery("#collapseLicenseCreate .row").html(data);
                },
                error: function(errorThrown){
                    console.log(errorThrown);
                }
            });
        }
    });


    $(document).scroll(function(e){
        var lastScrollTop = 0;
        $(window).scroll(function(event){
        var st = $(this).scrollTop();
        if(lastScrollTop > 0){
            if (st > lastScrollTop){
                // up
                jQuery("#btn-return-top").hide();
            }else {
                // down
                jQuery("#btn-return-top").show();
            }
        }
        lastScrollTop = st;
        });
    });

});

/*
function save_new_license(){
    if(jQuery('#collapseBoltCreate2 .panel-group').find('.collapse.in').length > 0){
        var collapse_id = jQuery('#collapseBoltCreate2 .panel-group').find('.collapse.in')[0].id;

        var license_plate = jQuery("[name=license_plate]").val();
        var enterprise = jQuery("[name=enterprise]").val();
        var messages = jQuery("[name=messages]").val();
        
        jQuery.ajax({
            type: 'POST',
            url: ajaxurl, // or example_ajax_obj.ajaxurl if using on frontend
            data: {
                'action': 'example_ajax_request',
                'collapse': collapse_id,
                'insert': true,
                'license_plate': license_plate,
                'enterprise': enterprise,
                'messages': messages,
                'create': 'Save'
            },
            success:function(data) {
                // This outputs the result of the ajax request
                var div_background = "#F44336";

                if(data == "Inserted"){
                    div_background = "#4CAF50";
                    jQuery("#"+collapse_id+" .panel-body").hide();
                }
                
                jQuery("#"+collapse_id+" .response-message").css("background",div_background);
                jQuery("#"+collapse_id+" .response-message").html(data);
                jQuery("#"+collapse_id+" .response-message").show();
            },
            error: function(errorThrown){
                console.log(errorThrown);
            }
        });  
    
    }
}
*/
jQuery(function () {
    jQuery('[data-toggle="tooltip"]').tooltip()
})
function license_management_save_ajax(type,id=''){
    var collapse_id = '';
    var force_reload = false;

    if(jQuery('#collapseBoltCreate2 .panel-group').find('.collapse.in').length > 0 && !jQuery('#license_management_modal').hasClass('in')){
        collapse_id = jQuery('#collapseBoltCreate2 .panel-group').find('.collapse.in')[0].id;
    }
    /*if(jQuery('#collapseLicenseCreate').hasClass('in')){
        collapse_id = 'collapseLicenseCreate';
        force_reload = true;
    }*/
    /*if(jQuery('#collapseEnterpriseCreate').hasClass('in')){
        collapse_id = 'collapseEnterpriseCreate';
    }*/

    if(jQuery("#license_managemente_edit").is(':visible') || jQuery('#license_management_modal').hasClass('in') && jQuery('#license_management_modal #btn-service-save').length > 0){
        collapse_id = 'collapse3';
    }

    var params = license_management_get_params_save(collapse_id,type,id);

    if(params != ''){
        jQuery.ajax({
            type: 'POST',
            url: ajaxurl, // or example_ajax_obj.ajaxurl if using on frontend
            data: params,
            success:function(data) {
                // This outputs the result of the ajax request
                if(type == 'create_service' || type == 'enterprise' || type == 'license' || type == 'messages_tmp' || type == 'del_messages_tmp' || collapse_id != ''){
                    if(collapse_id != ''){
                        if(jQuery('#license_management_modal').hasClass('in')){
                            var div_response = '.modal-response-message';
                        }else{
                            var div_response = '#'+collapse_id+' .response-message';
                        }
                    }else if(type == 'create_service'){
                        var input_val = '#new_service_name';
                        var table_list = '.table-list-service';
                        if(jQuery('#license_management_modal').hasClass('in')){
                            var div_response = '.modal-response-message';
                        }else{
                            var div_response = '#collapseServiceCreate .collapse-response-message';
                        }
                    }else if(type == 'enterprise'){
                        var input_val = '[name=business_name]';
                        var table_list = '.table-list-enterprise';
                        var div_response = '#collapseEnterpriseCreate .collapse-response-message';
                    }else if(type == 'license'){
                        var input_val = '[name=license_plate]';
                        var table_list = '.table-list-license';
                        var div_response = '#collapseLicenseCreate .collapse-response-message';
                    }else if(type == 'messages_tmp' || type == 'del_messages_tmp'){
                        var div_response = '.tab-content .messages_tab.response-message';
                    }
                    
                    var obj = JSON.parse(data);

                    var select_key = 0;
                    if(typeof obj.entityid != 'undefined'){
                        select_key = parseInt(obj.entityid);
                    }
                    var response = obj.result_message;
                    var result = obj.result;

                    if(result == 1){
                        // jQuery("#collapseServiceCreate").collapse('hide');
                        
                        if(select_key > 0){
                            var myobject = {};
                            myobject[select_key] = jQuery(input_val).val();
                            
                            /*var select = document.getElementById("service_name");
                            if(select != null){
                                for(index in myobject) {
                                    select.options[select.options.length] = new Option(myobject[index], index);
                                }
                            }else{*/
                                jQuery(table_list).append(obj.html);                         
                            //}

                            if(type == 'create_service' && jQuery('#serviceid').length > 0){                                
                                var service_name = params.service_name;
                                var serviceid = obj.entityid;
                                var ele_service = document.getElementById('serviceid');
                                var opt = document.createElement('option');
                                opt.value = serviceid;
                                opt.innerHTML = service_name;
                                ele_service.appendChild(opt);
                            }
                        }

                        if(type == 'messages_tmp'){
                            if(params.tmp_active == 'true'){
                                var messages_tab_active = langvars.LBL_YES;
                            }else{
                                var messages_tab_active = langvars.LBL_NO;
                            }
                            jQuery('#messages_tab_template_name_'+params.entityid).html(params.template_name);
                            jQuery('#messages_tab_active_'+params.entityid).html(messages_tab_active);
                        }

                        license_management_messageColor(div_response,1,response);
                    }else{
                        license_management_messageColor(div_response,2,response);
                    }

                    var calendar_edit = jQuery("#license_managemente_edit").is(':visible');
                    if(calendar_edit){
                        license_management_doSubmit();
                    }

                    if(force_reload){
                        setTimeout(function() {
                            location.reload();
                        }, 100);
                    }
                }

            },
            error: function(errorThrown){
                console.log(errorThrown);
            }
        });
    } 
}

function license_management_get_params_save(collapse_id,type,id = ''){
    var params = {};

    if(collapse_id != ''){
        if(type == 'license'){
            var license_plate = jQuery("[name=license_plate]").val();
            var enterpriseid = jQuery("[name=enterpriseid]").val();
            var messages = jQuery("[name=messages]").val();

            params =  {
                'action': 'license_management_request',
                'collapse': collapse_id,
                'insert': true,
                'license_plate': license_plate,
                'enterpriseid': enterpriseid,
                'messages': messages,
                'create': 'Save'
            }

        }else if(type == 'enterprise'){
            var business_name = jQuery("[name=business_name]").val();
            var userid = jQuery("[name=userid]").val();
            params =  {
                'action': 'license_management_request',
                'collapse': collapse_id,
                'insert': true,
                'business_name': business_name,
                'userid': userid,
                'create': 'Save'
            }

        }else if(type == 'service'){
            var serviceid = jQuery("[name=serviceid]").val();
            var licenseid = jQuery("[name=licenseid]").val();
            var service_status = jQuery("[name=service_status]").val();
            var date_end = jQuery("[name=date_end]").val();
            params =  {
                'action': 'license_management_request',
                'collapse': collapse_id,
                'insert': true,
                'serviceid': serviceid,
                'licenseid': licenseid,
                'service_status': service_status,
                'date_end': date_end,
                'create': 'Save'
            }
            
        }
    }else if(type == 'create_service'){
        var service_name = jQuery("#new_service_name").val();
        params =  {
            'action': 'license_management_request',
            'insert': true,
            'service_name': service_name,
            'create': 'create_service'
        }
        jQuery("#new_service_name").val('');
    }else if(collapse_id == '' && (type == 'messages_tmp' || type == 'del_messages_tmp')){
        var template_name = jQuery("#template_name"+id).val();
        var tmp_active = jQuery("#tmp_active"+id).is(":checked");
        // var content_editor = tinyMCE.editors['content'+id].getContent();
        var inst = new Object();
        for (inst in tinyMCE.editors) {
            if (tinyMCE.editors[inst].id['id'] == 'content'+id){  content_editor = tinyMCE.editors[inst].getContent(); }
        }
        params =  {
            'action': 'license_management_request',
            'insert': true,
            'template_name': template_name,
            'tmp_active': tmp_active,
            'content': content_editor,
            'mode': type,
            'entityid': id
        }
    }else if(type == 'enterprise'){
        var business_name = jQuery("[name=business_name]").val();
        var userid = jQuery("[name=userid]").val();
        params =  {
            'action': 'license_management_request',
            'insert': true,
            'business_name': business_name,
            'userid': userid,
            'create': type
        }
    }else if(type == 'license'){
        var license_plate = jQuery("[name=license_plate]").val();
        var enterpriseid = jQuery("[name=enterpriseid]").val();
        var messages = jQuery("[name=messages]").val();

        params =  {
            'action': 'license_management_request',
            'insert': true,
            'license_plate': license_plate,
            'enterpriseid': enterpriseid,
            'messages': messages,
            'create': type
        }
    }

    return params;
}

/* CREATE BOLT END */

function license_management_search(){

    var url = window.location.href;
    var lo_search = window.location.search;
    var str_pos_1 = lo_search.search('&sc_business_name=');
    if(str_pos_1 > 0){
        url = lo_search.slice(0,str_pos_1);
    }
    var str_pos_2 = lo_search.search('&sc_service_name=');
    if(str_pos_2 > 0){
        url = lo_search.slice(0,str_pos_2);
    }
    var str_pos_3 = lo_search.search('&sc_license_plate=');
    if(str_pos_3 > 0){
        url = lo_search.slice(0,str_pos_3);
    }

    var in_business_name = jQuery("#in_business_name").val();
    var in_service_name = jQuery("#in_service_name").val();
    var in_license_plate = jQuery("#in_license_plate").val();
    if(in_business_name != ''){
        url += "&sc_business_name=" + in_business_name;
    }
    if(in_service_name != ''){
        url += "&sc_service_name=" + in_service_name;
    }
    if(in_license_plate != ''){
        url += "&sc_license_plate=" + in_license_plate;
    }
    window.location = url;
}
function license_management_removeTagUrl(key, sourceURL = '') {
    if(sourceURL == ''){
        sourceURL = window.location.href;
    }
    var rtn = sourceURL.split("?")[0],
        param,
        params_arr = [],
        queryString = (sourceURL.indexOf("?") !== -1) ? sourceURL.split("?")[1] : "";
    if (queryString !== "") {
        params_arr = queryString.split("&");
        for (var i = params_arr.length - 1; i >= 0; i -= 1) {
            param = params_arr[i].split("=")[0];
            if (param === key) {
                params_arr.splice(i, 1);
            }
        }
        rtn = rtn + "?" + params_arr.join("&");
    }
    window.location = rtn;
}
function license_management_get_data_modal(type, entityid, entityid2, params = ''){
    if(params == ''){
        if(type == 'add_messages'){
            var params = {};
            params['action'] = 'license_management_request';
            params['create'] = type;
            params['serviceid'] = entityid;
            params['licenseid'] = entityid2;
        }else if(type == 'edit_service_lbl' || type == 'create_service_lbl'){
            var params = {};
            params['action'] = 'license_management_request';
            params['create'] = type;
            params['serviceid'] = entityid;
        }
    }

    return params;
}
function license_management_modal(type, entityid = '', entityid2 = '', params = '', title = ''){
    if(type != ''){

        var modal_data = license_management_get_data_modal(type, entityid, entityid2, params);

        jQuery.ajax({
            url: ajaxurl, // or example_ajax_obj.ajaxurl if using on frontend
            data: modal_data,
            success:function(data) {
                // This outputs the result of the ajax request
                //jQuery("#"+collapse_id+" .panel-body").html(data);
                if(license_management_IsJsonString(data)){
                    var obj = JSON.parse(data);
                    var modal_body = obj.html;
                    var modal_title = obj.title;
                }else{
                    var modal_body = data;
                    var modal_title = title;
                }
                jQuery("#license_management_modal #license_management_label").html(modal_title);
                jQuery("#license_management_modal .modal-body").html(modal_body);
                jQuery("#license_management_modal .modal-response-message").hide();
                jQuery('#license_management_modal').modal();
            },
            error: function(errorThrown){
                console.log(errorThrown);
            }
        });  
    }
}
function license_management_easy_save(type, entityid, entityid2 = ''){
    if(type != ''){
        
        if(type == 'service_messages'){
            var messages = jQuery("#license_management_modal .modal-body [name=messages]").val();
            var params = {};
            params['action'] = 'license_management_request';
            params['create'] = type;
            params['serviceid'] = entityid;
            params['licenseid'] = entityid2;
            params['messages'] = messages;
        }else if(type == 'update_service'){
            var service_name = jQuery("#license_management_modal .modal-body [name=service_name]").val();
            var params = {};
            params['action'] = 'license_management_request';
            params['create'] = type;
            params['serviceid'] = entityid;
            params['service_name'] = service_name;
        }

        jQuery.ajax({
            url: ajaxurl, // or example_ajax_obj.ajaxurl if using on frontend
            data: params,
            success:function(data) {
                // This outputs the result of the ajax request
                //jQuery("#"+collapse_id+" .panel-body").html(data);
                var obj = JSON.parse(data);

                if(typeof obj.html != 'undefined'){
                    jQuery("#license_management_modal .modal-body").html(obj.html);
                }
                if(typeof obj.message != 'undefined'){
                    license_management_messageColor("#license_management_modal .modal-response-message",obj.result,obj.message);
                    //jQuery("#license_management_modal .modal-response-message").html(obj.message);
                    //jQuery("#license_management_modal .modal-response-message").show();
                }
                
                if(obj.result == 1){
                    if(type == 'service_messages'){
                        parent.jQuery("#add_messages_"+entityid+"_"+entityid2).attr('data-original-title', messages); 
                        parent.jQuery("#add_messages_"+entityid+"_"+entityid2).append('<span class="badge">1</span>');
                    }else if(type == 'update_service'){
                        if(jQuery("#license_management_service_tab_"+entityid).length > 0){
                            document.getElementById("license_management_service_tab_"+entityid).firstElementChild.innerHTML = service_name;
                        }
                    }
                }

                jQuery('#license_management_modal').modal();
            },
            error: function(errorThrown){
                console.log(errorThrown);
            }
        });
    }
}
function license_management_ScrollTo(type) {
    if(type == 1){
        offset = jQuery(document).height();
    }else{
        element = jQuery('body');
        ele_offset = element.offset();
	    offset = ele_offset.top;
    }
    jQuery('html, body').animate({scrollTop: offset}, 500, 'linear');
}
function license_management_IsJsonString(str) {
  try {
    var json = JSON.parse(str);
    return (typeof json === 'object');
  } catch (e) {
    return false;
  }
}
function license_management_messageColor(div_id,result,message){
    var color_1 = "#4CAF50";
    var color_2 = "#F44336";

    if(div_id != '' && result != ''){
        if(result == 1){
            jQuery(div_id).css('background-color',color_1);
        }else if(result == 2){
            jQuery(div_id).css('background-color',color_2);
        }
        jQuery(div_id).html(message);
        jQuery(div_id).show();

        setTimeout(function(){ jQuery(div_id).hide(); }, 3000);
    }
}
function license_management_collapse(collapseid){
    jQuery('#'+collapseid).on('shown.bs.collapse', function (id) {

        if(jQuery('#'+collapseid).hasClass('in')){
            jQuery.ajax({
                url: ajaxurl, // or example_ajax_obj.ajaxurl if using on frontend
                data: {
                    'action': 'license_management_request',
                    'collapse': collapseid
                },
                success:function(data) {
                    // This outputs the result of the ajax request
                    jQuery('#'+collapseid+' .row').html(data);
                },
                error: function(errorThrown){
                    console.log(errorThrown);
                }
            });
        }
    });
}