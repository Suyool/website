export function initImagePicker() {
    //initialize image picker
    $('#spinner-loader').hide();
    $("#imagePicker").imagepicker({
        hide_select: true,
        show_label: false
    });
}

//if user want to choose image for news
export function chooseMainPicture(){
    //change picker button class to execute code specific for choosing news image
    $(".add-images").addClass("save-main-image").removeClass("add-to-body");
    //only 1 image is allowed to be picked
    $("#imagePicker").removeAttr("multiple");
    //show picker modal
    showPicturesModal();
}

//if user want to add images to the body
export function chooseEditorPicture(){
    //change picker button class to execute code specific for inserting body images
    $(".add-images").removeClass("save-main-image").addClass("add-to-body");
    //multiple images can be picked
    $("#imagePicker").attr("multiple","");
    //show picker modal
    showPicturesModal();
}

//show image picker modal
export function showPicturesModal() {
    //reset the image picker
    $("#imagePicker").html("");
    $(".image-search").val("");
    initImagePicker();
    $('#picturesModal').modal('show');
}

//hide image picker modal
export function hidePicturesModal() {
    $("#picturesModal").modal('hide');
}


//show new picture popup to upload new picture as main news picture
export function addNewMainPicture(){
    //set a flag to set new picture as news main picture
    $(".updatePictureForm").attr("data-type","mainPicture");
    $(".updatePictureForm").attr("action","/admin/picture/update");
    $(".updatePictureForm #picture_imageFile_file").attr("name","picture[imageFile][file]").removeAttr("multiple").prop("multiple",false);
    //show picker modal
    showNewPicturesModal();
}

//show new picture popup to upload new picture and add it to the body
export function addNewEditorPicture(){
    //set a flag to add new picture to the body
    $(".updatePictureForm").attr("data-type","bodyPicture");
    $(".updatePictureForm").attr("action","/admin/picture/multiupdate");
    $(".updatePictureForm #picture_imageFile_file").attr("name","picture[imageFile][file][]").attr("multiple","").prop("multiple",true);
    //show picker modal
    showNewPicturesModal();
}


//show new picture popup
export function showNewPicturesModal() {
    //clear new picture form
    $(".updatePictureForm").trigger('reset');
    //clear new picture tag
    $("#picture_tags").importTags('');
    //open new picture popup
    $('#pictureFormModal').modal('show');
}

//hide new picture popup
export function hideNewPicturesModal() {
    $('#pictureFormModal').modal('hide');
}