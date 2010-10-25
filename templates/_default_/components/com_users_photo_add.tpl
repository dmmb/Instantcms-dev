{* ================================================================================ *}
{* ========================= Загрузка фото, Шаг 1 ================================= *}
{* ================================================================================ *}

<h1 class="con_heading">{$LANG.ADD_PHOTOS}</h1>

{add_js file='includes/swfupload/swfupload.js'}
{add_js file='includes/swfupload/swfupload.queue.js'}
{add_js file='includes/swfupload/fileprogress.js'}
{add_js file='includes/swfupload/handlers.js'}
{add_css file='includes/swfupload/swfupload.css'}

<script type="text/javascript">
    {literal}
    var swfu;

    window.onload = function() {
        var settings = {
            flash_url : "/includes/swfupload/swfupload.swf",
            upload_url: "/users/photos/upload",
            post_params: {"PHPSESSID" :{/literal} "{$sess_id}"{literal}},
            file_size_limit : "20 MB",
            file_types : "*.jpg;*.png;*.gif;*.jpeg;*.JPG;*.PNG;*.GIF;*.JPEG",
            file_types_description : "Фотографии",
    {/literal}
            file_upload_limit : {if $max_limit}{$max_files}{else}100{/if},
    {literal}
            file_queue_limit : 0,
            custom_settings : {
                progressTarget : "fsUploadProgress",
                cancelButtonId : "btnCancel"
            },
            debug: false,

            // Button settings
            button_image_url: "/includes/swfupload/uploadbtn199x36.png",
            button_width: "199",
            button_height: "36",
            button_placeholder_id: "spanButtonPlaceHolder",

            // The event handler functions are defined in handlers.js
            file_queued_handler : fileQueued,
            file_queue_error_handler : fileQueueError,
            file_dialog_complete_handler : fileDialogComplete,
            upload_start_handler : uploadStart,
            upload_progress_handler : uploadProgress,
            upload_error_handler : uploadError,
            upload_success_handler : uploadSuccess,
            upload_complete_handler : uploadComplete,
            queue_complete_handler : queueComplete	// Queue plugin event
        };

        swfu = new SWFUpload(settings);
    };

    function queueComplete(numFilesUploaded) {
        if (numFilesUploaded>0){
            $('#divStatus').show();
            $('#continue').show();
            $("#files_count").html(numFilesUploaded);
        }
    }

    {/literal}
</script>

<form id="usr_photos_upload_form" action="" method="post" enctype="multipart/form-data">

    {if $max_limit}
    <p class="usr_photos_add_limit">{$LANG.YOU_CAN_UPLOAD} <strong>{$max_files}</strong> {$LANG.PHOTO_SHORT}</p>
    {/if}

        <div class="fieldset flash" id="fsUploadProgress" style="display:none">
            <span class="legend">{$LANG.UPLOAD_QUEUE}</span>
        </div>
    
        <div>
            <span id="spanButtonPlaceHolder"></span>
            <input id="btnCancel" type="button" value="Отменить все" onclick="swfu.cancelQueue();" disabled="disabled" style="margin-left: 2px; font-size: 8pt; height: 36px;" />
        </div>

        <div id="divStatus" style="display:none">
            {$LANG.UPLOADED} <span id="files_count"><strong>0</strong></span> {$LANG.PHOTO_SHORT}.
            <a href="/users/photos/submit" id="continue">{$LANG.CONTINUE}</a>
        </div>

</form>