<?php

 global $_jq_comments; $_jq_comments=0;
 function table_commentary( &$p, $table, $id, $value ) {
  $json=json_decode(json_encode($value),true);
  if ( !is_array($json) || strlen(trim($value)) == 0 ) $json=json_decode(array());
  global $_jq_comments;
  $_jq_comments++;
  if ( $_jq_comments == 1 ) {
   $p->JQuery();
   $p->JQ('jquery-comments.min.js');
  }
  $cid='comments_'.$_jq_comments;
  $p->HTML('<div id="'.$cid.'"></div>');
  $p->JQ('
   $("#comments-container").comments({
    profilePictureURL: "i/user-icon.png",
    refresh: function() {
    },
    getComments: function( suc, err ) {
        $.ajax({
            type: "get",
            url: "ajax.json.comments.php",
            data: { T:"'.$table.'", I:"'.$id.'", A:"G" },
            success: function(data) { suc(data); },
            error: function() {}
        });
    },
    deleteComment: function(commentJSON, suc, err ) {
        $.ajax({
            type: "delete",
            url: "ajax.json.comments.php",
            data: { T:"'.$table.'", I:"'.$id.'", A:"D", Id:commentJSON.id },
            success: function(data){ suc(data); }
            error: function() {}
        });
    }
    putComment: function(commentJSON, success, error) {
        $.ajax({
            type: "put",
            url: "ajax.json.comments.php",
            data: { T:"'.$table.'", I:"'.$id.'", A:"P", Id:commentJSON.id },
            success: function(comment) { success(comment); },
            error: function() {}
        });
    }
    upvoteComment: function(commentJSON, suc, err) {
        var commentURL = "/api/comments/" + commentJSON.id;
        var upvotesURL = commentURL + '/upvotes/';

        if(commentJSON.userHasUpvoted) {
            $.ajax({
                type: "post",
                url: upvotesURL,
                data: {
                    comment: commentJSON.id
                },
                success: function() {
                    success(commentJSON)
                },
                error: error
            });
        } else {
            $.ajax({
                type: "delete",
                url: upvotesURL + upvoteId,
                success: function() {
                    success(commentJSON)
                },
                error: error
            });
        }
    }
   });
');
 }
