(function($) {
    $.fn.doPost = function(options) {
        $.fn.doPost.defaults = {
            url: null,
            data: null,
            callback: function(res) {
                if (res.success) {
                } else {
                    bootbox.alert(res.message);
                }
            },
            timeOut: 3000
        };
        var opts = $.extend({}, $.fn.doPost.defaults, options);
        var t = window.setTimeout(
                function() {
                    $.post(opts.url, opts.data, opts.callback);
                },
                opts.timeOut
                );

        return t;

    };

    $.fn.cancelPost = function(t) {
        window.clearTimeout(t);
    };



}(jQuery));


/***************************************************************************************************************/
function try_delete(id, adminPath) {
    try_delete.html = $('#row-' + id).html();
    var url = adminPath + '/draft/delete/' + id;

    var cb = function(res) {
        if (res.success) {
            $('#break-' + id).remove();
        } else {
            bootbox.alert(res.message);
        }
    };

    timeOut = 4200;
    var f = window.setTimeout(
            function() {
                $('#row-' + id).fadeOut();
            },
            timeOut - 300
            );
    var t = $.fn.doPost({url: url, data: {}, callback: cb, timeout: timeOut});

    try_delete.cancel = function() {

        window.clearTimeout(f);
        $.fn.cancelPost(t);
        $('#row-' + id).html(try_delete.html);
        return false;

    };
    var back = $('#undoTemplate').html();
    back = back.replace('{id}', id);
    $('#row-' + id).html(back);
    return false;
}
/***********************************************************************/
function try_unpublish(id, adminPath) {
    var url = adminPath + '/draft/unpublish/' + id;
    var cb = function(res) {
        if (res.success) {
            $('#post-' + id).fadeOut().remove();
            $('.blog-status', '#row-' + id).html('draft');
            $('.blog-action', '#row-' + id).html('<a href="#"  onclick=" return try_delete(' + id + ',\'' + adminPath + '\');"><i class="fa fa-trash-o"></i></a>');
            bootbox.alert('<div class="alert alert-success" >Post is now unpublished!</div>');
        } else {
            bootbox.alert(res.message);
        }
    };
    $('.fa-eye-slash', '#row-' + id).removeClass('fa-eye-slash').addClass('fa-spinner fa-spin');
    $.fn.doPost({url: url, data: {}, timeout: 0, callback: cb});
}
/*************************************************************************************************************/
function loadDrafts(url) {
    $('#draft-items-wrapper').addClass('text-center').html('<i class="fa fa-5x fa-spinner fa-spin"></i><br/><h3>loading ...</h3>');
    var query = $('input[name="query_string"]').val();
    if (!query) {
        query = '';
    }
    $.get(url || '/admin/drafts', {query: query}, function(res) {
        if (res.success) {
            $('#draft-items-wrapper').removeClass('text-center').html(res.html);
        }
        else {
            $('#draft-items-wrapper').removeClass('text-center').html('<p class="alert alert-info">No draft found!</p>');
        }
    });

    return false;
}
/**************************************************************   
 
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
 
 };*/

/********************************************************************************************
 * Generic Editor
 */

genericEditor = {
    /* addURL: addURL,
     editURL: editURL,
     uploadURL: uploadURL,*/
    init: function() {
        $('#component-btns a').on('click', genericEditor.addComponent);
        //$('#component-btns a[data-component-type="text"]').trigger('click');
        //$('.draft-components-handle-edit').on('click', genericEditor.editClick);

    },
    submitComponentFrom: function(f) {
        var url = $(this).attr('action');
        var cb = function(res) {
            if (!res.success) {
                bootbox.alert(res.message);
                return;
            }
            var data = res.data;
            /*var h = $('#' + genericEditor.currentCmpSetting.res_tpl_name).html();
             h = h.replace('{{id}}', data.id);
             h = h.replace('{{comment}}', data.comment);
             h = h.replace('{{code}}', data.code);*/
            $('#components').append(res.html);
            $('#component-0').html('').removeClass('op-component-min-height');
        };

        $.post(url, $(this).serialize(), cb);

        return false;
    },
    submitChanges: function(f) {
        var url = $(f).attr('action');
        var cb = function(res) {
            if (!res.success) {
                bootbox.alert(res.message);
                return;
            }
            $('#component-' + res.data.id).html(res.html);
            genericEditor.reloadJS(res.data.id);
        };
        $.post(url, $(f).serialize(), cb);
        bootbox.hideAll();
        return false;

    },
    editClick: function(el) {
        var componentID = $(el).attr('data-component-id');
        $('#component-0').html('').removeClass('op-component-min-height');
        return genericEditor.editComponent(componentID);
    },
    editComponent: function(componentID) {

        var cmp = $('#component-' + componentID);
        var type = $(cmp).attr('data-component-type');
        var setting = genericEditor.getComponentSettings(type);
        if (!setting) {
            alert('Invalid Click');
            return;
        }

        cb = function(res) {
            if (component = res.component) {

                var tpl = genericEditor.prepareTPL(setting);
                tpl = tpl.replace('{{form-action}}', genericEditor.editURL);
                tpl = tpl.replace('{{component-id}}', componentID);
                tpl = tpl.replace('{{comment-val}}', component.comment);
              
                tpl = tpl.replace('{{code-val}}', component.code);
                tpl = tpl.replace('{{onsubmit}}', ' onsubmit =" return genericEditor.submitChanges(this);"');
                bootbox.dialog({message: tpl, title: 'Edit', buttons: {}});
                genericEditor.initWysihtml5();
            } else {
                alert(res.message);
            }
        }

        $.get(genericEditor.amdinPath + '/component-' + componentID + '.json', cb);

        return false;
    },
    addComponent: function() {

        var type = $(this).attr('data-component-type');

        var setting = genericEditor.getComponentSettings(type);
        if (!setting) {
            alert('Invalid Click');
            return;
        }

        var tpl = genericEditor.prepareTPL(setting);
        tpl = tpl.replace('{{code-val}}', '');
        tpl = tpl.replace('{{onsubmit}}', '');
        tpl = tpl.replace('{{comment-val}}', '');
        tpl = tpl.replace('{{form-action}}', genericEditor.addURL);
        tpl = '<div class="well well-small">' + tpl + '</div>'
        $('#component-0').html(tpl).fadeIn();
        if (!$('#component-0').hasClass('op-component-min-height')) {
            $('#component-0').addClass('op-component-min-height');
        }
        if (setting.name === 'image') {
            genericEditor.initDz();
        }
        genericEditor.initWysihtml5();
        $('#component-0 form').on('submit', genericEditor.submitComponentFrom);
        document.getElementById('component-btns').scrollIntoView();
        return false;
    },
    prepareTPL: function(setting) {
        var tpl = $('#' + setting.tpl_name).html();
        tpl = tpl.replace('{{type-id}}', setting.id);
        tpl = tpl.replace('{{label-text}}', setting.label);
        return tpl;
    },
    getComponentSettings: function(type) {
        var settings = genericEditor.cmpTypes;
        if (settings[type]) {
            genericEditor.currentCmpSetting = settings[type];
            return settings[type];
        }
        return null;


    },
    reloadJS: function(componentID) {

        scripts = document.getElementsByTagName('script');
        for (x in scripts) {
            script = scripts[x];
            

            if (script.constructor.name === 'HTMLScriptElement') {
                oldSrc = script.getAttribute('src');
                console.log(oldSrc);
                if (oldSrc) {
                    if (oldSrc.substring(0, 2) == '//') {

                        script.parentNode.removeChild(script);
                        newScript = document.createElement('script');

                        newScript.src = oldSrc;
                        document.body.appendChild(newScript);

                    }
                }
            }

        }

        return;
      


    },
    initWysihtml5: function() {
        $('textarea[name="comment"]').wysihtml5({
            'font-styles': false,
            "blockquote": false,
            "lists": false, //(Un)ordered lists, e.g. Bullets, Numbers. Default true
            "html": false, //Button which allows you to edit the generated HTML. Default false
            "link": true, //Button to insert a link. Default true
            "image": false, //Button to insert an image. Default true,
            "color": false, //Button to change color of font  

        });
        genericEditor.wysihtml5 = $('textarea[name="comment"]').data("wysihtml5").editor;
    },
    initDz: function() {
        $("div#dropzone").dropzone(
                {
                    url: genericEditor.uploadURL + '&type_id=' + genericEditor.currentCmpSetting.id,
                    parallelUploads: 1,
                    maxFilesize: 1,
                    paramName: 'image',
                    uploadMultiple: false,
                    thumbnailWidth: 400,
                    thumbnailHeight: 300,
                    maxFiles: 1,
                    addRemoveLinks: false,
                    init: function() {
                        this.on("error", function(file, message) {
                            this.removeFile(file);
                            bootbox.alert(message);
                        });

                        this.on("success", function(file, json, xmlHttp) {
                            if (json.success) {
                                var data = json.cmp,
                                        src = json.message;
                                var h = $('#' + genericEditor.currentCmpSetting.res_tpl_name).html();
                                h = h.replace('{{id}}', data.id);
                                h = h.replace('{{src}}', src);
                                h = h.replace('{{alt}}', 'Photo');
                                h = h.replace('{{comment}}', data.comment ? data.comment : '');
                                $('#component-' + data.id).remove();
                                $('#components').append(h);
                                $('#component-0').html('').removeClass('op-component-min-height');
                            } else {
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
$(function() {
    genericEditor.init();
});
