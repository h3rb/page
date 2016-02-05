<?php

/************************************************************** Author: H. Elwood Gilliland III
 *  _____  _                 _____       _                    * Maintainer: B. Mackenzie
 * |  _  ||_| ___  ___  ___ |     | ___ | |_  ___  ___        * (c) 2015 PieceMaker Technologies
 * |   __|| || -_||  _|| -_|| | | || .'|| '_|| -_||  _|       * ---------------------------------
 * |__|__ |_||___||___||___||_|_|_||__,||_,_||____|_|         * Enumeration mappings for
 * |_   _| ___  ___ | |_  ___  ___ | | ___  ___ |_| ___  ___  * Aggregation Service
 *   | |  | -_||  _||   ||   || . || || . || . || || -_||_ -| *
 *   |_|  |___||___||_|_||_|_||___||_||___||_  ||_||___||___| *
 *                                         |___|              *
 **************************************************************/

global $enum_Deployment_Equipment_Type;
$enum_Deployment_Equipment_Type
 = array( 1 => "Kiosk", 2 => "3D Printer" );

global $enum_Software_Version_Software_Type;
$enum_Software_Version_Software_Type
 = array(
  1 => "EPS Payload",
  2 => "EPS Merge-able Payload",
  3 => "Kiosk Payload",
  4 => "Kiosk Merge-able Payload",
  5 => "Catalog",
  6 => "Catalog Merge-able Payload",
  7 => "Printer Firmware Update",
  8 => "RPi Automation Image"
);

global $enum_Remote_Event_Type;
$enum_Remote_Event_Type
 = array(
  1 => "UI Event",
  2 => "Log Event",
  3 => "Aggregation Event"
);

global $enum_Event_UI;
$enum_Event_UI
 = array(
  1 => "Session Start",
  2 => "Session End",
  3 => "Switched to Category",
  4 => "Selected Item",
  5 => "Button Press",
  6 => "Keyed Input",
  7 => "User Interaction",
  8 => "Window Activated",
  9 => "Touch Down Recorded",
 10 => "Touch Up Recorded",
 11 => "Swipe Recorded",
 12 => "Session Marked As Test",
 13 => "Item Ordered",
 14 => "Receipt Printed"
);

global $enum_Event_Log;
$enum_Event_Log
 = array(
  1 => "Initialization Time",
  2 => "Ready for Service",
  3 => "Shutdown",
  4 => "Update Requested",
  5 => "Update Completed",
  6 => "Communication Failure",
  7 => "Remote Request"
);

global $enum_Log_Event_Shutdown;
$enum_Log_Event_Shutdown
 = array(
  1 => "Scheduled",
  2 => "Manual"
);

global $enum_Log_Update_Requested_Completed;
$enum_Log_Update_Requested_Completed
 = array(
  1 => "EPS",
  2 => "Kiosk",
  3 => "Catalog",
  4 => "Firmware",
  5 => "Automation"
);

global $enum_Log_Remote_Request;
$enum_Log_Remote_Request
 = array(
  1 => "Replacement Equipment",
  2 => "Bill of Sales ID Packet",
  3 => "Receipt Paper",
  4 => "Status Update",
);

global $enum_Transfer_Event;
$enum_Transfer_Event
 = array(
  1 => "Catalog Transmitted by Request",
  2 => "Remote payload received: loggable data",
  3 => "Server Automated Service Intervention",
  4 => "Service Operator Intervention",
  5 => "Remote Request",
  6 => "Bill of Sale IDs Transmitted",
  7 => "EPS Video Upload Processed",
  8 => "Installation Request",
  9 => "Software Update Request",
 10 => "Firmware Update Request"
);

global $enum_BOS_Payment_Type;
$enum_BOS_Payment_Type
 = array(
  1 => "Credit Card Swiped",
  2 => "Paid via Store Cashier"
);

global $enum_Fulfillment_Type;
$enum_Filfillment_Type
 = array(
  1 => "On-Demand Sale",
  2 => "Next Day Pickup",
  3 => "Offsite Shipment to Customer"
);

global $enum_Inventory_Type;
$enum_Inventory_Type
 = array(
  1 => "Receipt Paper",
  2 => "Filament Spool"
);

global $enum_Request_Status;
$enum_Request_Status
 = array(
  0 => "Failed",
  1 => "Succeeded",
  2 => "Awaiting",
  3 => "Attempting"
);

global $enum_EPS_Video_Upload_Type;
$enum_EPS_Video_Upload_Type
 = array(
  1 => "Service Log Video",
  2 => "Customer Printer Video"
);
