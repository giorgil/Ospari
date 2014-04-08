<?php
$this->setJS(OSPARI_URL . '/assets-admin/js/bootstrap3-typeahead.min.js');
$this->setJS(OSPARI_URL . '/assets-admin/js/dropzone.js');
$this->setJS(OSPARI_URL . '/assets-admin/js/bootstrap-tagsinput.min.js');
$this->setJS(OSPARI_URL . '/assets-admin/js/bootstrap3-wysihtml5.all.min.js');

$this->setCSS(OSPARI_URL . '/assets-admin/css/dropzone.css');
$this->setCSS(OSPARI_URL . '/assets-admin/css/bootstrap-tagsinput.css');
$this->setCSS(OSPARI_URL . '/assets-admin/css/bootstrap3-wysihtml5.min.css');

$title = 'Edit';
$this->title = $title;
$draft = $this->draft;

?>
<div class="col-lg-6 col-lg-offset-1" id="content-preview">
    
</div>
<div class="row">
    <div class="col-lg-6 col-lg-offset-1">
        <div class="tabbable tabs-below" id="myTab">
            <h1><?php echo $this->escape($draft->title); ?></h1>
            
            <div>
                <?php echo $this->escape($draft->content); ?>
            </div>
            <div id="draft-components">
                
            </div>
                
                <div id="draft-component-0">
                    
                </div>
                <hr>
                
                <div id="component-btns">
                    <a href="#" class="btn btn-default" data-component-type="text">Text</a>
                    <a href="#" class="btn btn-default" data-component-type="YotubeVideo">Youtube</a>
                </div>
                
                
               
        
    </div>
    
</div>


<script id="component-all-types-tpl-response" type="text/x-handlebars-template">
    <div id="draf-component-{{id}}">
    <p>{{comment}}</p>
    <div>{{code}}</div>
    </div>
</script>

<script id="component-text-tpl-response" type="text/x-handlebars-template">
    <div id="draf-component-{{id}}">
    <p>{{comment}}</p>
    <div>{{code}}</div>
    </div>
</script>
    
    
<script id="component-text-tpl" type="text/x-handlebars-template">
<form role="form" action="{{form-action}}" method="post">
  <div class="form-group">
    <label for="comment">Comment</label>
   <textarea name="comment" id="component-comment" class="form-control" rows="3"></textarea>
  </div>
  <input type="hidden" name="component-type" value="{{component-type}}">
  <button type="submit" class="btn btn-primary">Submit</button>
</form>
</script>



<script id="component-all-types-tpl" type="text/x-handlebars-template">
<form role="form" action="{{form-action}}" method="post">
  <div class="form-group">
    <label for="comment">Comment</label>
   <textarea name="comment" id="component-comment" class="form-control" rows="3"></textarea>
  </div>
  <div class="form-group">
    <label for="embed_code">Embed code</label>
     <textarea name="code" id="component-code" class="form-control" rows="3"></textarea>
  </div>
  
  <input type="hidden" name="component-type" value="{{component-type}}">
  <button type="submit" class="btn btn-primary">Submit</button>
</form>

</script>

<script>
    genericEditor = {
        addURL : '/<?php echo OSPARI_ADMIN_PATH.'/draft/'.$draft->id.'/add-component'; ?>',
         editURL : '/<?php echo OSPARI_ADMIN_PATH.'/draft/'.$draft->id.'/edit-component'; ?>',
        init: function(){
            /*
             genericEditor.initWysihtml5();
             genericEditor.listeners(); 
             genericEditor.initDz();
             */
            $('#component-btns a').on('click', genericEditor.addComponent);
             
        },
        
        submitComponentFrom: function(f){
            url = $(this).attr('action');
            
            cb = function( res ){
                
                h = $('#component-text-tpl-response').html();
                h = h.replace('{{id}}', res.id);
                h = h.replace('{{comment}}', res.comment);
                h = h.replace('{{code}}', res.code);
                $('#draft-components').append(h);
                $('#draft-component-0').html('');
            }
            
            $.post(url, $(this).serialize(), cb);
            
            return false;
        },
        
        addComponent: function(){
           
            var type = $(this).attr('data-component-type');
            
            setting = genericEditor.getComponentSettings(type);
            if( !setting ){
                alert('Invalid Click');
                return;
            }
            
            tpl = genericEditor.prepareTPL(setting);
            tpl = tpl.replace('{{form-action}}', genericEditor.addURL);
            $('#draft-component-0').html(tpl).fadeIn();
            $('#draft-component-0 form').on('submit', genericEditor.submitComponentFrom)
            //
            return false;
        },
        
        prepareTPL : function( setting ){
            tpl = $('#'+setting.tpl).html();
            tpl = tpl.replace('{{component-type}}', setting.type);
            
            return tpl;
        },
        getComponentSettings : function( type ){
            settings = {
                'text' : {
                    'tpl': 'component-text-tpl', 
                    'type' : 'text',
                },
                
                'YotubeVideo' : {
                    'tpl': 'component-all-types-tpl', 
                    'type' : 'YotubeVideo',
                }
            }
            
            if( settings[type] ){
                return settings[type];
            }
            return null;
            
            
        },
        
        initWysihtml5: function(){
            return;
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
      
     
      
    };
  $(function(){
     genericEditor.init();   
  });
</script>