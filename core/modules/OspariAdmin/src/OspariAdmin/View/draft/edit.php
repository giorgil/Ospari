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
$cmpTypes = $this->cmpTypes;

?>
<div class="col-lg-6 col-lg-offset-1" id="content-preview">
    
</div>
<div class="row">
    <div class="col-lg-6 col-lg-offset-1">
        <div class="tabbable tabs-below" id="myTab">
            <h1><?php echo $this->escape($draft->title); ?></h1>
            
            <div>
                <?php echo $draft->content; ?>
            </div>
            <div id="draft-components">
                <?php if($this->components->count()>0):?>
                        <?php  foreach($this->components as $cmp):?>
               
                            <?php if($cmp->name=='text' || $cmp->name=='html'):?>
                            <div id="draft-component-<?=$cmp->id?>">
                                <?=$cmp->comment?>
                            </div>
                            <?php elseif($cmp->name=='image'):?>
                                <div id="draft-component-<?=$cmp->id?>">
                                    
                                    <img src="<?php echo OSPARI_URL.'/content/upload/'.$cmp->code;?>" >
                                </div>
                             <?php else: ?>
                                <p><?=$cmp->comment?></p>
                                <div><?=$cmp->code?></div>
                            <?php endif;?>
                        <?php endforeach;?>
                    <?php endif;?>
            </div>
                
            <div id="draft-component-0" class="op-component-min-height">
                    
                </div>
                <hr>
                
                <div id="component-btns">
                    <?php foreach ($cmpTypes as $type): ?>
                        <a href="#" class="btn btn-default" data-component-type="<?=$type['name']?>"><?=$type['short_name']?></a>
                    <?php endforeach;?>
                </div>
                
                
               
        
    </div>
    
</div>


<script id="component-all-types-tpl-response" type="text/x-handlebars-template">
    <div id="draft-component-{{id}}">
    <p>{{comment}}</p>
    <div>{{code}}</div>
    </div>
</script>

<script id="component-text-tpl-response" type="text/x-handlebars-template">
    <div id="draft-component-{{id}}">
      {{comment}}
    </div>
</script>
<script id="component-img-tpl-response" type="text/x-handlebars-template">
    <div id="draft-component-{{id}}">
      <img src="{{src}}" alt="{{alt}}">
    </div>
</script>
    
    
<script id="component-text-tpl" type="text/x-handlebars-template">
<form role="form" action="{{form-action}}" method="post">
  <div class="form-group">
    <label for="comment">{{label-text}}</label>
   <textarea name="comment" id="component-comment" class="form-control op-component-text-box" rows="3"></textarea>
  </div>
  <input type="hidden" name="type_id" value="{{type-id}}">
  <button type="submit" class="btn btn-primary">Submit</button>
</form>
</script>

<script id="component-image-tpl" type="text/x-handlebars-template">
    <label for="embed_code">{{label-text}}</label>
    <div style="clear:both;" id="clear-dropzone"></div><div class="dropzone" id="dropzone"></div>
</script>



<script id="component-all-types-tpl" type="text/x-handlebars-template">
<form role="form" action="{{form-action}}" method="post">
  <div class="form-group">
    <label for="comment">Comment</label>
   <textarea name="comment" id="component-comment" class="form-control op-component-comment-box" rows="3"></textarea>
  </div>
  <div class="form-group">
    <label for="embed_code">{{label-text}}</label>
     <textarea name="code" id="component-code" class="form-control op-component-code-box" rows="3"></textarea>
  </div>
  
  <input type="hidden" name="type_id" value="{{type-id}}">
  <button type="submit" class="btn btn-primary">Submit</button>
</form>

</script>

<script>
    genericEditor = {
        addURL : '/<?php echo OSPARI_ADMIN_PATH.'/draft/'.$draft->id.'/add-component'; ?>',
        editURL : '/<?php echo OSPARI_ADMIN_PATH.'/draft/'.$draft->id.'/edit-component'; ?>',
        uploadURL:'/<?php echo OSPARI_ADMIN_PATH.'/media/upload?draft_id='.$draft->id; ?>',
        init: function(){
            $('#component-btns a').on('click', genericEditor.addComponent);
            $('#component-btns a[data-component-type="text"]').trigger('click');
             
        },
        
        submitComponentFrom: function(f){
            var url = $(this).attr('action');
            var cb = function( res ){
                if(!res.success){
                bootbox.alert(res.message);
                    return;
                }
                var data = res.data;    
                var h = $('#'+genericEditor.currentCmpSetting.res_tpl_name).html();
                h = h.replace('{{id}}', data.id);
                h = h.replace('{{comment}}', data.comment);
                h = h.replace('{{code}}', data.code);
                $('#draft-components').append(h);
                $('#draft-component-0').html('');
            };
            
            $.post(url, $(this).serialize(), cb);
            
            return false;
        },
        
        addComponent: function(){
           
            var type = $(this).attr('data-component-type');
            
            var setting = genericEditor.getComponentSettings(type);
            if( !setting ){
                alert('Invalid Click');
                return;
            }
            
            var tpl = genericEditor.prepareTPL(setting);
            tpl = tpl.replace('{{form-action}}', genericEditor.addURL);
            $('#draft-component-0').html(tpl).fadeIn();
            if(setting.name ==='image'){
                genericEditor.initDz();
            }else{
               if(setting.name!=='html'){
                genericEditor.initWysihtml5();
               }
            }
            $('#draft-component-0 form').on('submit', genericEditor.submitComponentFrom);
           
            return false;
        },
        
        prepareTPL : function( setting ){
            var tpl = $('#'+setting.tpl_name).html();
            tpl = tpl.replace('{{type-id}}', setting.id);
            tpl = tpl.replace('{{label-text}}', setting.label);
            return tpl;
        },
        getComponentSettings : function( type ){
        var settings = <?php echo json_encode($cmpTypes);?>;
            if( settings[type] ){
                genericEditor.currentCmpSetting = settings[type];
                return settings[type];
            }
            return null;
            
            
        },
        
        initWysihtml5: function(){
             $('textarea[name="comment"]').wysihtml5({
                 image:false
             });
             genericEditor.wysihtml5 =$('textarea[name="comment"]').data("wysihtml5").editor;
        },
        initDz: function(){
            $("div#dropzone").dropzone(
                 { 
                     url:genericEditor.uploadURL+'&type_id='+genericEditor.currentCmpSetting.id,
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
                                var data = json.cmp,
                                     src = json.message;    
                                var h = $('#'+genericEditor.currentCmpSetting.res_tpl_name).html();
                                h = h.replace('{{id}}', data.id);
                                h = h.replace('{{src}}', src);
                                h = h.replace('{{alt}}', 'Photo');
                                $('#draft-component-'+data.id).remove();
                                $('#draft-components').append(h);
                                $('#draft-component-0').html('');
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
        }
      
    };
  $(function(){
     genericEditor.init();   
  });
</script>