<?php
$this->setJS(OSPARI_URL . '/assets-admin/js/bootstrap3-typeahead.min.js');
$this->setJS(OSPARI_URL . '/assets-admin/js/dropzone.js');
$this->setJS(OSPARI_URL . '/assets-admin/js/bootstrap-tagsinput.min.js');
$this->setJS(OSPARI_URL . '/assets-admin/js/bootstrap3-wysihtml5.all.min.js');

$this->setCSS(OSPARI_URL . '/assets-admin/css/dropzone.css');
$this->setCSS(OSPARI_URL . '/assets-admin/css/bootstrap-tagsinput.css');
$this->setCSS(OSPARI_URL . '/assets-admin/css/bootstrap3-wysihtml5.min.css');

?>
<div class="col-lg-6 col-lg-offset-1" id="content-preview">
    
</div>
<div class="row">
    <div class="col-lg-6 col-lg-offset-1">
        <div class="tabbable tabs-below" id="myTab">
            <div class="tab-content">
                <div class="tab-pane active" id="home">
                    <textarea class="textarea" placeholder="Enter text ..." style="width: 100%; min-height: 320px; font-size: 14px; line-height: 18px;"></textarea>
                    <div class="text-right">
                        <div class="btn-group" style="margin-bottom: 5px; margin-top: 20px;">
                            <button type="button" class="btn btn-default clear-textarea">Clear</button>
                            <button type="button" class="btn btn-default save-text">Save As Draft</button>
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="profile">
                    <div style="clear:both;" id="clear-dropzone"></div>
                    <div class="dropzone"  id="dropzone"></div>
                    <div class="text-right">
                        <div class="btn-group" style="margin-bottom: 5px; margin-top: 20px;">
                            <button type="button" class="btn btn-default">Clear</button>
                            <button type="button" class="btn btn-default">Save As Draft</button>
                        </div>
                    </div>
                </div>
            </div>
            <ul class="nav nav-tabs">
                <li><a href="#home" data-toggle="tab">Text Editor</a></li>
                <li><a href="#profile" data-toggle="tab">Image Uploader</a></li>
            </ul>
        </div>
    </div>
    
</div>

<script>
    genericEditor = {
        init: function(){
             genericEditor.initWysihtml5();
             genericEditor.listeners(); 
             genericEditor.initDz();
             
        },
        initWysihtml5: function(){
             $('.textarea').wysihtml5({
                 image:false
             });
             genericEditor.wysihtml5 =$('.textarea').data("wysihtml5").editor;
        },
        initDz: function(){
            $("div#dropzone").dropzone(
                 { 
                     url:"/media/upload?draft_id="+$('#draft-id-input').val(),
                     parallelUploads:1,
                     maxFilesize:1,
                     paramName:'image',
                     uploadMultiple:false,
                     thumbnailWidth:400,
                     thumbnailHeight:300,
                     maxFiles:1,
                     addRemoveLinks:false,
                     init: function(){
                         this.on("error", function(file, message) {
                             this.removeFile(file);
                             bootbox.alert(message); 
                         });

                         this.on("success", function(file, json, xmlHttp) { 
                             if(json.success){
                             }else{
                                 this.removeFile(file);
                                 bootbox.alert(json.message);
                             }
                         });

                         this.on("maxfilesexceeded", function(file) {
                             this.removeFile(file);
                         });
                     }
                 }
              );
        },
       listeners:function(){
           $('.clear-textarea').bind('click',genericEditor.clearTextarea);
           $('.save-text').bind('click', genericEditor.saveText);
       },
      clearTextarea: function(){
          $('.textarea').val('');
          //genericEditor.wysihtml5.composer.commands.exec('cancel');
      },
      saveText: function(){
          var txt = $('.textarea').val();
          if(txt){
            $('#content-preview').append('<div class="text-component">'+txt+'</div>');
            genericEditor.hideAllTabs();
          }
      },
      hideAllTabs: function(){
          $('.tab-pane','#myTab').hide();
      }
    };
  $(function(){
     genericEditor.init();   
  });
</script>