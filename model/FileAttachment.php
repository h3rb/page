global $_file_attachment_id; $_file_attachment_id=0;
class FileAttachment extends Model {

  static public function ShowAttachmentDialog( &$p, $table, $id ) {
   $p->HTML('<div id="attachments"></div>');
   $p->JQ(' update_attachments(); ');
$p->JS('
 function add_attachment(ftable,fid) {
  $.ajax({
   url:"file.attach?T='.$table.'&I='.$id.'&F="+ftable+"&FID="+fid,
   responseType:"json",
   success:function(e){ if ( e.result == "error" ) alert("error!"); update_attachments();},
  });
 }
 function remove_attachment(id) {
  $.ajax({
   url:"file.detach?I="+id,
   responseType:"html",
   success:function(e){update_attachments();},
  });
 }
 function update_attachments() {
  $("#attachments").html("<center><img src=\'i/LOAD.GIF\'></center>");
  $.ajax({
   url:"file.attachments?T='.$table.'&I='.$id.'",
   responseType:"html",
   success:function(e){$("#attachments").html("<h2>Attachments</h2>"+e+attachment_add_newhtml()); get_attachables();},
  });
 }
 function attachment_add_newhtml() {
  return "<div class=\'file_attacher\' id=\'attachment_add_new\'></div>";
 }
 function get_attachables() {
  $.ajax({
   url:"file.attachables",
   responseType:"html",
   success:function(e){$("#attachment_add_new").html("<h3>Attachable Files</h3>"+e);}
  });
 }
');
  }

 };
