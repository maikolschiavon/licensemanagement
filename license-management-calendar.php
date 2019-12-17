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

function license_management_calendar($params){
  $calendar = new license_management_calendar;

  return $calendar->get_html($params);
}

class license_management_calendar{

  public function get_html($params){
    
    $calendar_values = $this->create_json($params);

    $html = "<div id='fullCalendar'></div>";
    $html .= "<div class='row'><input id='calendar_values' type='hidden' value='$calendar_values'></div>";

    return $html;
  }

  public function create_json($params){
      $result = array();
      $i = 0;

      if(!empty($params)){
        foreach($params as $obj){
          $start = $obj->date_end;

          if(!empty($start) && $start != '0000-00-00'){

            $title = $obj->license_plate." ".$obj->service_name." ".$obj->business_name;
            $url = admin_url('admin.php?page=license_management_service&id='.$obj->serviceid.'&licenseid='.$obj->licenseid);

            $result[$i]["title"] = $title;
            $result[$i]["url"] =  $url;
            $result[$i]["start"] = $start;
            $i++;
          }
          
        }
      }
      
      return json_encode($result, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
  }

}



?>
