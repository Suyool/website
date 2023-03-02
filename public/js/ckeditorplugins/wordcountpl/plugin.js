CKEDITOR.plugins.add('wordcountpl',
    {
        onLoad: function() {
            CKEDITOR.document.appendStyleSheet(this.path + "wordcount.css");
        },
        init: function (editor) {
            var pluginName = 'wordcountpl';

            function updateProgressBar() {
                var editorContent = $(CKEDITOR.instances.news_group1_body.getData());
                var plainEditorContent = editorContent.text().trim();
                filteredBody = plainEditorContent.replace(/[.،,'"\/#!$?؟%\^&\*;:{}=\-_`~()]/g, "")

                var words = filteredBody.match(/\S+/g);

                var count = 0;

                if (typeof words !== "undefined" && words !== null) {
                    count = words.length;
                }

                $('.nbOfWords').text(count);
                $('#word-count').val(count);
                var percentage = Math.round((count / 80) * 100);

                if (percentage > 100)
                    percentage = 100;

                var elem = $('.progressBar');

                elem.css('width', percentage + '%');
                elem.attr('aria-valuenow', percentage);
                elem.removeClass('short-text medium-text long-text');

                $("#news_wordCount").val(count);

                if (count >= 60) {
                    if (count >= 80) {
                        elem.addClass('long-text');
                    } else {
                        elem.addClass('medium-text');
                    }
                } else {
                    elem.addClass('short-text');
                }
            }

            editor.on("key",
                function(event) {
                    setTimeout(function () {
                        updateProgressBar();
                    }, 400);
                },
                editor,
                null,
                100);
            editor.on("dataReady",
                function(event) {
                    updateProgressBar();
                },
                editor,
                null,
                100);

            editor.on("uiSpace",
                function(event) {
                    if (editor.elementMode !== CKEDITOR.ELEMENT_MODE_INLINE) {
                        if (event.data.space == "bottom") {
                           event.data.html += '<div class="bodyWordsCounter">' +
                               '<span class="nbOfWords">0</span> <div class="progressBarWrapper"><div class="progressBar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div></div>' +
                               '</div>';
                        }
                    }
                },
                editor,
                null,
                100);

        }

    });
