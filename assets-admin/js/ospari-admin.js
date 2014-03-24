 
Ospari = {
    doAutoSave: 0,
    adminURL: '/admin',
    blogURL : '',
    initDraft: function() {
        Ospari.autoSaveURL = location.pathname.replace(/\/(create|edit\/(.*))/, '/auto-save');
        setInterval(Ospari.autoSave, 3000);
        Ospari.doAutoSave = 1;
        $('#btn-save-draft').click(Ospari.saveAsDraft);
        $('#btn-publish').click(Ospari.publishDraft);

       


    },
    updateSlug: function(){
        var me = this;
        var draft_id = $('#draft-id-input').val();
        if(!draft_id){
            bootbox.alert('Not auto saved yet');
        }
        
        var path = location.pathname.replace(/\/(create|edit\/(.*))/, '/edit-slug');
        var callback = function(res) {
            if (res.success) {
                //$('#draft-slug-bx').html(res.message+Ospari.getEditSlugBtnTpl());
            } else {
                bootbox.alert(res.message);
                return false;
            }
        };
        bootbox.dialog({
                        message: me.getSlugTpl(),
                        title: "Change URL",
                        buttons: {
                          
                          cancel: {
                            label: "<i class=\"fa fa-times\"></i> Cancel",
                            className: "btn",
                            callback: function() {
                              
                            }
                          },
                          save: {
                            label: "<i class=\"fa fa-check\"></i> Save",
                            className: "btn-primary",
                            callback: function() {
                                var slug = $('#slug').val();
                                if(!slug){
                                    return false;
                                }
                               $.post(path, {draft_id:draft_id,slug:slug}, callback);
                            }
                          }
                        }
                      });
        
        
    },
    autoSave: function() {
        if (Ospari.doAutoSave === 0) {
             console.log('auto save = 0');
            return;
        }

        Ospari.doAutoSave = 0;

        form = $('#draft-form');
        $('#draft-state-input').val(0);

        title = $('#draft-form').find("input[name=title]").val();
        content = $('#draft-content-textarea').val();

        if (!content && !title) {
            return;
        }


        callback = function(res) {
            if (res.success) {
                Ospari.doAutoSave = 0;
                //$('#auto-save-msg').html(res.message);
                //$('#draft-slug-bx').html(res.draft_slug+Ospari.getEditSlugBtnTpl());
                $('#preview-li').remove();
                $('#meta-li').remove();
                $('#draft-ul').append('<li id="meta-li"><a href="#" onclick=" return Ospari.addMeta();"><i class="fa fa-code"></i>Meta-Tags</a></li>');
                $('#draft-ul').append('<li id="preview-li"><a href="'+Ospari.blogURL+'/preview?draft_id='+res.draft_id+'" target="_preview"><i class="fa fa-external-link"></i> Preview</a></li>');
                $('#draft-id-input').val(res.draft_id);
                ///draft/edit/48
                if( history && history.pushState ){
                    if( location.pathname.match( /\/create(.*)/ ) )
                     history.pushState({}, "Edit "+res.draft_id, Ospari.adminURL+"/draft/edit/"+res.draft_id);
                }
               
                
            } else {
                bootbox.alert(res.message);
            }
        };

        $.post(Ospari.autoSaveURL, Ospari.preparePostData(form), callback);

    },
    saveAsDraft: function() {
        $('#btn-save-draft i:first').remove();
        $('#btn-save-draft').append(' <i class="fa fa-refresh fa-spin"></i>');
        
        form = $('#draft-form');
        $('#draft-state-input').val(0);
        callback = function(res) {
            bootbox.hideAll();
            if (res.success) {
                //bootbox.alert(res.message);
                $('#btn-save-draft i:first').remove();
                $('#btn-save-draft').append(' <i class="fa fa-check"></i>');
            } else {
                bootbox.alert(res.message);
            }

        };
        $.post($(form).attr('action'), Ospari.preparePostData(form), callback);
        return false;
    },
    publishDraft: function() {
        $('#btn-publish i:first').remove();
        $('#btn-publish').append(' <i class="fa fa-refresh fa-spin"></i>');
        
        var form = $('#draft-form');
        $('#draft-state-input').val(1);

        callback = function(res) {
            bootbox.hideAll();
            if (res.success) {
                $('#btn-publish i:first').remove();
                bootbox.dialog({
                    message: res.message,
                    //title: "Make Money Online with RankSider.com",
                    buttons: {
                        success: {
                            label: "View Post",
                            className: "btn btn-success",
                            callback: function() {
                                document.location = res.post_url;
                            }
                        },
                        main: {
                            label: "Close",
                            className: "btn bold",
                            callback: function() {}
                        }
                    }
                })


            } else {
                bootbox.alert(res.message);
            }
        };
        $.post($(form).attr('action'), Ospari.preparePostData(form) , callback);
        return false;
    },
    
    preparePostData: function( form ){
        arr = $(form).serializeArray();
        var content = $('#editor-preview').html();
        var tmpContent = content.replace('id="dropzone"', 'id="dropzone-tmp"');
        $('#draft-preview-content').html(tmpContent);
        $('#dropzone-tmp').remove();
        $('#clear-dropzone','#draft-preview-content').remove();
        arr.push( { 'name': 'content', 'value':   $('#draft-preview-content').html() } ); 
        return arr;
    },
    
    getSlugTpl: function(){
        var tpl='<div class="form-horizontal" role="form">'
              +'<div class="form-group">'
                +'<div class="col-sm-12">'
                  +'<input type="text" class="form-control" id="slug" placeholder="Type a new URL">'
                +'</div>'
              +'</div>'
            +'</div>';
    return tpl;
    },
    getEditSlugBtnTpl: function(){
        return '<span><a href="#" title="Edit" onclick=" return Ospari.updateSlug();" id="edit-slug"> <i class="fa fa-edit"></i></a></span>';
    },
    bindImgPositionEvent:function(){
        //$('.op-img').attr({"data-toggle":"tooltip", "data-placement":"auto", "data-title":"To reposition the image, click on it"}).tooltip();
        $('#editor-preview img').unbind('click');
        
        $('#editor-preview img').on('click', 
                function(){
                       var tmpThis = $(this);
                       var tpl ='<h4>Position Image</h4>'
                                 +'<div class="btn-group" data-toggle="buttons">'
                                 +'<label class="btn btn-default">'
                                  +'<input type="radio" name="img-position" id="option1" value="op-img-left"> Left'
                                +'</label>'
                                +'<label class="btn btn-default">'
                                  +'<input type="radio" name="img-position" id="option2" value="op-img-center"> Center'
                                +'</label>'
                                +'<label class="btn btn-default">'
                                  +'<input type="radio" name="img-position" id="option3" value="op-img-right"> Right'
                                +'</label>'
                              +'</div>';
                       bootbox.alert(tpl, function(){
                           var position = $("input:radio[name='img-position']:checked").val();
                           if(position !== undefined){
                               tmpThis.closest('div').removeClass('op-img-left op-img-center op-img-right').addClass(position);
                               Ospari.addClassToEltInEditor(tmpThis.closest('div').attr('id'), position);
                           }
                           
                       } );
                   })
                           .addClass('op-img-priview');
                   Ospari.bingImgHoverEvent();
    },
    bingImgHoverEvent: function(){
        var elt = $('.op-img');
        elt.unbind('hover');
        elt.hover(function(){
                   $(this).addClass('op-img-relative').append('<span class="op-img-text">Click on the image to reposition it</span>');
            },function(){
                 $('span.op-img-text').remove();
                 $(this).removeClass('op-img-relative');
            });  
    },
    addClassToEltInEditor: function(id, position){
        var content = $('#draft-content-textarea').val();
        var tmpContent = content.replace('id="'+id+'"', 'id="img-tmp-id"');
        $('#draft-preview-content').html(tmpContent);
        $('#img-tmp-id').removeClass('op-img-left op-img-center op-img-right').addClass(position);
        tmpContent = $('#draft-preview-content').html();
        tmpContent = tmpContent.replace('id="img-tmp-id"','id="'+id+'"');
        $('#draft-content-textarea').val( tmpContent);
        Ospari.doAutoSave = 1;
        Ospari.autoSave();
    },
    addMeta: function(){
        var draft_id = $('#draft-id-input').val();
        if(!draft_id){
            bootbox.alert('Not auto saved yet');
        }
        
        var path = location.pathname.replace(/\/(create|edit\/(.*))/, '/meta/'+draft_id);
        var callback = function(res) {
            if (res.success) {
                //$('#draft-slug-bx').html(res.message+Ospari.getEditSlugBtnTpl());
            } else {
                bootbox.alert(res.message);
                return false;
            }
        };
        bootbox.dialog({
                        message: $('#meta-form-script').html(),
                        title: "Enter Meta",
                        buttons: {
                          
                          cancel: {
                            label: "<i class=\"fa fa-times\"></i> Cancel",
                            className: "btn",
                            callback: function() {
                              
                            }
                          },
                          save: {
                            label: "<i class=\"fa fa-check\"></i> Save",
                            className: "btn-primary",
                            callback: function() {
                                
                                $.post(path, $('#meta-form').serialize(), callback);
                            }
                          }
                        }
                      });
        
    }

};


