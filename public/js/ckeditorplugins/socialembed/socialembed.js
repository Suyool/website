CKEDITOR.plugins.add('socialembed',
    {
        init: function (editor) {
            var pluginName = 'socialembed';

            editor.addCommand('embedCommand', new CKEDITOR.dialogCommand('embedDialog'));

            editor.ui.addButton('facebook-embed',
                {
                    label: 'Embed facebook',
                    command: 'embedCommand',
                    icon: CKEDITOR.plugins.getPath('socialembed') + 'facebook.png'
                });
            editor.ui.addButton('instagram-embed',
                {
                    label: 'Embed instagram',
                    command: 'embedCommand',
                    icon: CKEDITOR.plugins.getPath('socialembed') + 'instagram.png'
                });
            editor.ui.addButton('twitter-embed',
                {
                    label: 'Embed twitter',
                    command: 'embedCommand',
                    icon: CKEDITOR.plugins.getPath('socialembed') + 'twitter.png'
                });

            CKEDITOR.dialog.add('embedDialog', this.path + 'dialogs/embedDialog.js');
        }
    });
