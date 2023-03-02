CKEDITOR.plugins.add('image-picker',
    {
        init: function (editor) {
            var pluginName = 'image-picker';
            editor.ui.addButton('image-picker',
                {
                    label: 'إضافة صور من الارشيف',
                    command: 'OpenWindow',
                    icon: CKEDITOR.plugins.getPath('image-picker') + 'addpicture.png'
                });
            editor.ui.addButton('add-new-image',
                {
                    label: 'إضافة صور جديدة',
                    command: 'OpenNewWindow',
                    icon: CKEDITOR.plugins.getPath('image-picker') + 'newpicture.png'
                });
            var cmd = editor.addCommand('OpenWindow', { exec: ImagePickerHandler.chooseEditorPicture });
            var cmd = editor.addCommand('OpenNewWindow', { exec: ImagePickerHandler.addNewEditorPicture });
        }
    });
