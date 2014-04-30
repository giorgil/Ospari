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
            $('.blog-tooltip').tooltip();
        }
        else {
            $('#draft-items-wrapper').removeClass('text-center').html('<p class="alert alert-info">No draft found!</p>');
        }
    });

    return false;
}
/***************************************************************************************************************************/
function addMeta(draft_id, adminPath) {
    if (!draft_id) {
        bootbox.alert('Not auto saved yet');
    }

    var path = '/' + adminPath + '/draft/meta/' + draft_id;
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
        title: "Enter Meta-Tags",
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
    return false;
}
/******************************************************************************************************************************************/
function updateSlug(draft_id, adminPath) {
    if (!draft_id) {
        bootbox.alert('Not auto saved yet');
    }

    var path = '/' + adminPath + '/draft/edit-slug';
    var callback = function(res) {
        if (res.success) {
            $('#draft-title').html();
        } else {
            bootbox.alert(res.message);
            return false;
        }
    };
    bootbox.dialog({
        message: $('#slug-form-script').html(),
        title: "Change Slug",
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
                    if (!slug) {
                        return false;
                    }
                    $.post(path, {draft_id: draft_id, slug: slug}, callback);
                }
            }
        }
    });
    return false;

}
/********************************************************************************************/
function try_publish(id, adminPath, el) {
    var url = '/' + adminPath + '/draft/publish';
    var h = $(el).html();
    var cb = function(res) {
        $(el).removeClass('disabled').html('Update');
        if (res.success) {
            //bootbox.alert('<div class="alert alert-success" >Post is now published!</div>');
            bootbox.dialog({
                message: "Post is now published!",
                title: res.message,
                buttons: {
                    success: {
                        label: "Close",
                        className: "btn-default",
                        
                    },
                   
                    main: {
                        label: "View Post",
                        className: "btn-primary",
                        callback: function() {
                           document.location = res.url;
                        }
                    }
                }
            });
        } else {
            bootbox.alert(res.message);
        }
    };
    $(el).addClass('disabled').html('<i class="fa fa-refresh fa-spin"></i>');
    $.post(url, {draft_id: id, state: 1}, cb);
    return false;
    //$.fn.doPost({url: url, data: {draft_id: id, state:1}, timeout: 0, callback: cb});
}

/********************************************************************************************
 * Generic Editor
 */

genericEditor = {
    init: function() {
        $('#component-btns a').on('click', genericEditor.addComponent);
    },
    updateImgText: function(el){
        var cmp_id =$(el).attr('data-component-id');
        var cb = function(json){
            if(json.success){
                $('#component-comment-' +cmp_id ).html(json.component.comment);
                bootbox.hideAll();
                return;
            }
            bootbox.alert(json.message);
        };
        $.post(genericEditor.imgTextUrl, {'component_id':cmp_id,comment:$('#component-comment').val()}, cb);
        return false;
    },
    submitComponentFrom: function(f) {
        var url = $(this).attr('action');
        var cb = function(res) {
            if (!res.success) {
                bootbox.alert(res.message);
                return;
            }
            $('#draft-components').append(res.html);
            $('#component-0').html('').removeClass('op-component-min-height');
            genericEditor.reloadJS();
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
                tpl = tpl.replace('{{save-btn}}','<a href="#" class="btn btn-primary pull-right" data-component-id="'+componentID+'" onclick=" return genericEditor.updateImgText(this);">Update Comment</a>');
                tpl = tpl.replace('{{code-val}}', component.code);
                tpl = tpl.replace('{{onsubmit}}', ' onsubmit =" return genericEditor.submitChanges(this);"');
                bootbox.dialog({message: tpl, title: 'Edit', buttons: {}});
                genericEditor.initWysihtml5();
                if (setting.name == 'image') {
                    genericEditor.initDz(componentID);
                }
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
        tpl = tpl.replace('{{component-id}}', '0');
        tpl = tpl.replace('{{save-btn}}','');
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
        //document.getElementById('component-btns').scrollIntoView();
        
        element = $('#component-0');
        $('html, body').animate({ scrollTop: ($(element).offset().top)}, 'slow');
        
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
    },
    initDz: function(componentID) {
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
                        this.on('sending', function(file, xhr, formData) {
                            formData.append('comment', $('#component-comment').val());
                            if (componentID !== undefined) {
                                formData.append('component_id', componentID);
                            }
                        });
                        this.on("success", function(file, json, xmlHttp) {
                            if (json.success) {
                                if (json.mode == 'add') {
                                    $('#component-' + json.data.id).remove();
                                    $('#draft-components').append(json.html);
                                    $('#component-0').html('').removeClass('op-component-min-height');
                                }
                                else {
                                    $('#component-' + json.data.id).html(json.html);
                                }
                                bootbox.hideAll();
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
