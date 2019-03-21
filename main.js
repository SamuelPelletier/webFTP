var droppedFiles = false;
var fileName = '';
var $dropzone = $('.dropzone');
var $button = $('.upload-btn');
var uploading = false;
var $syncing = $('.syncing');
var $done = $('.done');
var $bar = $('.bar');
var timeOut;

$dropzone.on('drag dragstart dragend dragover dragenter dragleave drop', function(e) {
    e.preventDefault();
    e.stopPropagation();
})
    .on('dragover dragenter', function() {
        $dropzone.addClass('is-dragover');
    })
    .on('dragleave dragend drop', function() {
        $dropzone.removeClass('is-dragover');
    })
    .on('drop', function(e) {
        droppedFiles = e.originalEvent.dataTransfer.files;
        fileName = droppedFiles[0]['name'];
        $('.filename').html(fileName);
        $('.dropzone .upload').hide();
    });

$button.bind('click', function() {
    startUpload();
});

$("input:file").change(function (){
    fileName = $(this)[0].files[0].name;
    $('.filename').html(fileName);
    $('.dropzone .upload').hide();
});

function startUpload() {
    if (!uploading && fileName != '' ) {
        uploading = true;
        $button.html('Uploading...');
        $dropzone.fadeOut();
        $syncing.addClass('active');
        $done.addClass('active');
        $bar.addClass('active');
        timeoutID = window.setTimeout(showDone, 3200);
    }
}

function showDone() {
    $button.html('Done');
}

// get all folders in our .directory-list
var allFolders = $(".directory-list li > ul");
allFolders.each(function() {

    // add the folder class to the parent <li>
    var folderAndName = $(this).parent();
    folderAndName.addClass("folder");

    // backup this inner <ul>
    var backupOfThisFolder = $(this);
    // then delete it
    $(this).remove();
    // add an <a> tag to whats left ie. the folder name
    folderAndName.wrapInner("<a href='#' />");
    // then put the inner <ul> back
    folderAndName.append(backupOfThisFolder);
    folderAndName.find("a").each(function(index,value){
        $(value).attr('link',$(value).text())
        $(value).text('/'+$(value).text().split("/").pop())
    })
    // now add a slideToggle to the <a> we just added
    folderAndName.find("a").click(function(e) {
        window.location.href = "/?path="+$(this).attr('link')
        e.preventDefault();
    });

});