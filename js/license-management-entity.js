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

function license_managment_export_csv(entity){

    params =  {
        'action': 'license_management_request',
        'entity': entity,
        'export_csv': true
    };

    jQuery.ajax({
        type: 'POST',
        url: ajaxurl, // or example_ajax_obj.ajaxurl if using on frontend
        data: params,
        success:function(data) {
            window.location.href = data;
        },
        error: function(errorThrown){
            console.log(errorThrown);
        }
    });
}
function license_managment_confirm_delete(lbl){

    var check = confirm(lbl);
    if(check == true){
        var lo_path = window.location.pathname;
        var lo_search = window.location.search;
        var str_pos = lo_search.search('&id=');
        if(str_pos > 0){
            var url = lo_search.slice(0,str_pos);
            jQuery("#form-entity")[0].action = lo_path + url;
        }
    }
}
jQuery( document ).ready(function($) {
    jQuery('#license-managment-module a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        var tab = e.target; // newly activated tab
        var tabName = tab.attributes[1].value;
        // console.log(e.relatedTarget); // previous active tab

        if( jQuery(".tab-content #"+ tabName +" .dashboard-header").length == 0 ){
            jQuery.ajax({
                url: ajaxurl, // or example_ajax_obj.ajaxurl if using on frontend
                data: {
                    'action': 'license_management_request',
                    'mode': 'tab',
                    'entity': tabName
                },
                success:function(data) {
                    // This outputs the result of the ajax request
                    //jQuery("#"+collapse_id+" .panel-body").html(data);
                    jQuery(".tab-content #" + tabName ).html(data);
                    // jQuery('#license_management_modal').modal();
                },
                error: function(errorThrown){
                    console.log(errorThrown);
                }
            });
        }
    });
});