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

jQuery(document).ready(function () {
    jQuery('.nav-second a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        var tab = e.target; // newly activated tab
        var tabName = tab.attributes[1].value;
  
        if(tabName == 'calendar'){
            // page is now ready, initialize the calendar...

            var calendar_values = JSON.parse( jQuery("#calendar_values").val() );
            var calendar_value_len = Object.keys(calendar_values).length;
            
            var events = [];
            for(var i = 0; i < calendar_value_len; i++){

                var params = {};
                params['title'] = calendar_values[i].title;
                params['start'] = calendar_values[i].start;
                params['url'] = calendar_values[i].url;
                params['allDay'] = true;
                
                events.push(params);
            }

            if(jQuery('#calendar .fc-content').length == 0){
                jQuery('#calendar').fullCalendar({
                    // put your options and callbacks here
                    // defaultView: 'agendaDay',
                    eventBorderColor: "",

                    header: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'month,basicWeek,basicDay'
                    },

                    weekends:false,
                    weekNumbers:true,

                    editable: false,
                    selectable: true,
                    
                    //When u select some space in the calendar do the following:
                    select: function (start, end, allDay) {
                        //do something when space selected
                        //Show 'add event' modal
                        //jQuery('#createEventModal').modal('show');
                        license_managment_create_new_dateend(start);
                    },

                    //When u drop an event in the calendar do the following:
                    eventDrop: function (event, delta, revertFunc) {
                        //do something when event is dropped at a new location
                    },

                    //When u resize an event in the calendar do the following:
                    eventResize: function (event, delta, revertFunc) {
                        //do something when event is resized
                    },

                    eventRender: function(event, element) {
                        jQuery(element).tooltip({title: event.title});             
                    },

                    //Activating modal for 'when an event is clicked'
                    eventClick: function (event) {
                        //jQuery('#modalTitle').html(event.title);
                        //jQuery('#modalBody').html(event.description);
                        //jQuery('#fullCalModal').modal();
                    },
                    events: events,
                })
            }
        }
    });
});

function license_management_doSubmit(){
    jQuery("#license_managemente_edit").modal('hide');

    var licenseid = document.getElementById('licenseid');
    var license = licenseid.options[licenseid.selectedIndex].innerHTML;

    var serviceid = document.getElementById('service_name');
    var service_name = serviceid.options[serviceid.selectedIndex].innerHTML;

    var title = license +' ' +service_name;

    jQuery("#calendar").fullCalendar('renderEvent',
        {
            title: title,
            start: new Date(jQuery('[name=date_end]').val()),

        },
        true);
    }

function license_managment_create_new_dateend(date_end){
    if(date_end != ''){
        date_end = license_managment_convert(date_end);

        var type = 'collapse3';
        var params_aj = {};
        params_aj['action'] = 'license_management_request';
        params_aj['collapse'] = type;
        params_aj['date_end'] = date_end;
      
        license_management_modal(type, '', '', params_aj, langvars.LBL_CREATE_SERVICE);
    }
}

function license_managment_convert(str) {
    var date = new Date(str),
        mnth = ("0" + (date.getMonth()+1)).slice(-2),
        day  = ("0" + date.getDate()).slice(-2);
    return [ date.getFullYear(), mnth, day ].join("-");
}