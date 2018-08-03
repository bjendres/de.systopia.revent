/*-------------------------------------------------------+
| SYSTOPIA REMOTE EVENT REGISTRATION                     |
| Copyright (C) 2017 SYSTOPIA                            |
| Author: B. Endres (endres@systopia.de)                 |
| Author: P. Batroff (batroff@systopia.de)               |
| http://www.systopia.de/                                |
+--------------------------------------------------------+
| This program is released as free software under the    |
| Affero GPL license. You can redistribute it and/or     |
| modify it under the terms of this license which you    |
| can read by viewing the included agpl.txt or online    |
| at www.gnu.org/licenses/agpl.html. Removal of this     |
| copyright header is strictly prohibited without        |
| written permission from the original author(s).        |
+--------------------------------------------------------*/

// get event IDs
var idArray = [];
cj("tr[id^='event-']").each(function(){
    var pattern = /^event-([0-9].*)/;
    var tmp = pattern.exec(cj(this).attr("id"));
    idArray.push(tmp[1]);
});

var pattern_1 = /civicrm\/event\/manage\/settings/;
var pattern_2 = /civicrm\/event\/manage\/reminder/;

// iterate over Events and hide/do stuff
for (var i = 0; i < idArray.length; ++i) {
    cj("#panel_info_" + idArray[i] + " li a").each(function() {
        if (!pattern_1.test(cj(this).attr("href")) && !pattern_2.test(cj(this).attr("href"))) {
            cj(this).hide();
        }
    });
    var url = "__URL__?eid=" + idArray[i] + "&reset=1";
    var link_entry = '<li><a title="__Registration-Customisation__" class="action-item crm-hover-button enabled" href="' + url + '" >__Registration-Customisation__</a> </li>'

    var import_url = "__URL-import__?eid=" + idArray[i] + "&reset=1";
    var import_link_entry = '<li><a title="__Registration-Customisation-import__" class="action-item crm-hover-button crm-popup enabled" href="' + import_url + '" >__Registration-Customisation-import__</a> </li>'

    // #6330 Event Report Link
    var event_report_url = "__REPORT-URL__?reset=1&force=1&event_id_op=in&event_id_value=" + idArray[i];
    var event_report_link_entry = '<li><a title="__REPORT-URL-LABEL__" class="action-item crm-hover-button crm-popup enabled" href="' + event_report_url + '" >__REPORT-URL-LABEL__</a> </li>'


    cj("ul#panel_info_" + idArray[i]).append(link_entry);
    cj("ul#panel_info_" + idArray[i]).append(import_link_entry);
    cj("ul#panel_info_" + idArray[i]).append(event_report_link_entry);
}
