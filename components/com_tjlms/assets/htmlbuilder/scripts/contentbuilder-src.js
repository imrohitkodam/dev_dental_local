(function ($) {

    var $activeRow;

    $.contentbuilder = function (element, options) {

        var defaults = {
            zoom: '1',
            selectable: "h1,h2,h3,h4,h5,h6,p,ul,ol,small,.edit",
            onRender: function () { },
            outline: false,
            snippetFile: 'assets/default/snippets.html',
            hiquality: false,
            snippetTool: 'right',
            imageselect: '',
            fileselect: '',
            enableZoom: true
        };

        this.settings = {};

        var $element = $(element),
                    element = element;

        this.init = function () {

            this.settings = $.extend({}, defaults, options);

            //$element.css({ 'margin-top': '80px', 'margin-bottom': '80px' });

            /**** Zoom ****/
            if (!this.settings.enableZoom) {
                localStorage.removeItem("zoom");
            }

            if (localStorage.getItem("zoom") != null) {
                this.settings.zoom = localStorage.zoom;
            } else {
                localStorage.zoom = this.settings.zoom;
            }
            $element.css('zoom', this.settings.zoom);
            $element.css('-moz-transform', 'scale(' + this.settings.zoom + ')');

            //IE fix
            this.settings.zoom = this.settings.zoom + ''; //Fix undefined
            if (this.settings.zoom.indexOf('%') != -1) {
                this.settings.zoom = this.settings.zoom.replace('%', '') / 100;
                localStorage.zoom = this.settings.zoom;
            }
            if (this.settings.zoom == 'NaN') {
                this.settings.zoom = 1;
                localStorage.zoom = 1;
            }
            /**** Zoom ****/

            /**** Enlarge droppable area ****/
            $element.css({ 'min-height': '500px' });

            /**** Localize All ****/
            $('body').append('<div id="divCb"></div>');

            /**** Load snippets library ****/
            if ($('#divSnippets').length == 0) {

                $('#divCb').append('<div id="divSnippets" style="display:none"></div>');

                var s = '<div id="divTool"><div id="divSnippetList"></div>';
                s += '';
                s += '<br><div id="divRange"><input type="range" id="inpZoom" min="80" max="100" value="100"></div>';
                s += '';
                s += '<a id="lnkToolClose" href="#">×</a></div>';
                $('#divCb').append(s);

                $('#inpZoom').val(this.settings.zoom * 100);

                $('#divCb input[type="range"]').rangeslider({
                    onSlide: function (position, value) { },
                    polyfill: false
                });

                var val = $('#inpZoom').val() / 100;
                this.zoom(val);

                $('#inpZoom').on('change', function () {
                    var val = $('#inpZoom').val() / 100;
                    $element.data('contentbuilder').zoom(val);
                });

                //Enable/disable Zoom
                if (!this.settings.enableZoom) {
                    $('#divRange').css('display', 'none');
                    $('#divSnippetList').css('height', '100%');
                }

                $('#divSnippets').load(this.settings.snippetFile, function () {
                    var html = '';
                    var i = 1;
                    $('#divSnippets').children('div').each(function () {
                        $(this).attr('id', 'snip' + i);
                        html += '<div data-snip="' + i + '"><img src="' + $(this).data("thumb") + '" /></div>';
                        i++;
                    });

                    $('#divSnippetList').html(html);

                    $('#divSnippetList > div').draggable({
                        cursor: 'move',
                        helper: function () {
                            return $("<div class='dynamic'></div>")[0];
                        },
                        connectToSortable: "#" + $element.attr('id'),
                        stop: function (event, ui) {

                            /* fix bug */
                            $element.children("div").each(function () {
                                if ($(this).children("img").length == 1) {
                                    $(this).remove();
                                }
                            });

                        }
                    });

                });
            }

            /**** Apply builder elements ****/
            $element.children("*").wrap("<div class='ui-draggable'></div>");
            $element.children("*").append('<div class="row-tool">' +
                '<div class="row-handle"><i class="cb-icon-move"></i></div>' +
                '<div class="row-html"><i class="cb-icon-code"></i></div>' +
                '<div class="row-remove"><i class="cb-icon-trash"></i></div>' +
                '</div>');

            $('#divCb').append('<div id="temp-contentbuilder" style="display: none"></div>');

            $('#divCb').append('<a id="lnkToolOpen" href="#divTool"><i class="cb-icon-pencil"></i></a>');

            /* Snippet Tool */
            var $window = $(window);
            var windowsize = $window.width();
            var toolwidth = 260;
            if (windowsize < 600) {
                toolwidth = 150;
            }

            if (this.settings.snippetTool == 'right') {
                // Sliding from Right
                $('#divTool').css('width', toolwidth + 'px');
                $('#divTool').css('right', '-' + toolwidth + 'px');

                $("#lnkToolOpen").click(function (e) {
                    $element.data('contentbuilder').clearControls();

                    $('#divTool').animate({
                        right: '+=' + toolwidth + 'px'
                    }, 250, function () {
                        $('#lnkToolClose').css('display', 'block');
                    });
                    /*$('body').animate({
                    marginRight: '+=' + toolwidth + 'px'
                    }, 250);
                    $('#rte-toolbar').animate({ // Slide the editor toolbar
                    paddingRight: '+=' + toolwidth + 'px'
                    }, 250);*/
                    e.preventDefault();
                });

                $("#lnkToolClose").click(function (e) {
                    $element.data('contentbuilder').clearControls();

                    $('#divTool').animate({
                        right: '-=' + toolwidth + 'px'
                    }, 250, function () {
                        $('#lnkToolClose').css('display', 'none');
                    });
                    /*$('body').animate({
                    marginRight: '-=' + toolwidth + 'px'
                    }, 250);
                    $('#rte-toolbar').animate({ // Slide the editor toolbar
                    paddingRight: '-=' + toolwidth + 'px'
                    }, 250);*/
                    e.preventDefault();
                });

                //Adjust the row tool
                $('.row-tool').css('right', 'auto');
                if (windowsize < 600) {
                    $('.row-tool').css('left', '-30px'); //for small screen
                } else {
                    $('.row-tool').css('left', '-55px');
                }

            } else {

                // Sliding from Left
                $('#divTool').css('width', toolwidth + 'px');
                $('#divTool').css('left', '-' + toolwidth + 'px');

                $('#lnkToolOpen').addClass('leftside');
                $('#lnkToolClose').addClass('leftside');

                $("#lnkToolOpen").click(function (e) {
                    $element.data('contentbuilder').clearControls();

                    $('#divTool').animate({
                        left: '+=' + (toolwidth + 0) + 'px'
                    }, 250, function () {
                        $('#lnkToolClose').css('display', 'block');
                    });
                    /*$('body').animate({
                    marginLeft: '+=' + toolwidth + 'px'
                    }, 250);
                    $('#rte-toolbar').animate({ // CUSTOM
                    paddingLeft: '+=' + toolwidth + 'px'
                    }, 250);*/
                    e.preventDefault();
                });

                $("#lnkToolClose").click(function (e) {
                    $element.data('contentbuilder').clearControls();

                    $('#divTool').animate({
                        left: '-=' + (toolwidth + 0) + 'px'
                    }, 250, function () {
                        $('#lnkToolClose').css('display', 'none');
                    });
                    /*$('body').animate({
                    marginLeft: '-=' + toolwidth + 'px'
                    }, 250);
                    $('#rte-toolbar').animate({
                    paddingLeft: '-=' + toolwidth + 'px'
                    }, 250);*/
                    e.preventDefault();
                });

                //Adjust the row tool
                $('.row-tool').css('left', 'auto');
                if (windowsize < 600) {
                    $('.row-tool').css('right', '-30px'); //for small screen
                } else {
                    $('.row-tool').css('right', '-55px');
                }

            }


            /**** Apply builder behaviors ****/
            this.applyBehavior();

            /**** Trigger Render event ****/
            this.settings.onRender();

            /**** DRAG & DROP behavior ****/
            $element.sortable({
                items: '.ui-draggable', axis: 'y',
                handle: '.row-handle',
                delay: 200,
                tolerance: "pointer",
                cursor: 'move',
                placeholder: 'block-placeholder',
                deactivate: function (event, ui) {

                    if (ui.item.parent().attr('id') == $element.attr('id')) {

                        ui.item.replaceWith(ui.item.html());

                        $element.children("*").each(function () {

                            if (!$(this).hasClass('ui-draggable')) {
                                $(this).wrap("<div class='ui-draggable'></div>");
                            }
                        });

                        $element.children('.ui-draggable').each(function () {
                            if ($(this).find('.row-tool').length == 0) {
                                $(this).append('<div class="row-tool">' +
                                '<div class="row-handle"><i class="cb-icon-move"></i></div>' +
                                '<div class="row-html"><i class="cb-icon-code"></i></div>' +
                                '<div class="row-remove"><i class="cb-icon-trash"></i></div>' +
                                '</div>');
                            }
                        });

                        $element.children('.ui-draggable').each(function () {
                            if ($(this).children('*').length == 1) {
                                $(this).remove();
                            }
                        });

                        /*
                        //dropped on root
                        if (ui.item.find('.row-tool').length == 0) {
                        ui.item.append('<div class="row-tool">' +
                        '<div class="row-handle"><i class="cb-icon-move"></i></div>' +
                        '<div class="row-html"><i class="cb-icon-code"></i></div>' +
                        '<div class="row-remove"><i class="cb-icon-trash"></i></div>' +
                        '</div>');
                        }*/

                    }

                    //Apply builder behaviors
                    $element.data('contentbuilder').applyBehavior();

                    //Trigger Render event
                    $element.data('contentbuilder').settings.onRender();

                }
            });

            /* http://stackoverflow.com/questions/6285758/cannot-drop-a-draggable-where-two-droppables-touch-each-other */
            $.ui.isOverAxis = function (x, reference, size) {
                return (x >= reference) && (x <= (reference + size));
            };

            $element.droppable({
                drop: function (event, ui) {
                    if ($(ui.draggable).data('snip')) {
                        var snip = $(ui.draggable).data('snip');
                        var snipHtml = $('#snip' + snip).html();
                        $(ui.draggable).data('snip', null); //clear
                        return ui.draggable.html(snipHtml);
                        event.preventDefault();
                    }
                },
                tolerance: 'pointer',
                greedy: true
            });


            $(document).bind('mousedown', function (event) {

                //console.log($(event.target).prop("tagName").toLowerCase())

                //Remove Overlay on embedded object to enable the object.
                if ($(event.target).attr("class") == 'ovl') {
                    $(event.target).css('z-index', '-1');
                }

                if ($(event.target).parents('.ui-draggable').length > 0 && $(event.target).parents('#' + $element.attr('id')).length > 0) {

                    /****** Row Controls ******/
                    if ($element.data('contenteditor').settings.outline) {
                        $(".ui-draggable").removeClass('ui-dragbox');
                        $(event.target).parents(".ui-draggable").addClass('ui-dragbox');
                    } else {
                        $(".ui-draggable").removeClass('ui-dragbox-outlined');
                        $(event.target).parents(".ui-draggable").addClass('ui-dragbox-outlined');
                    }

                    $element.find('.row-tool').stop(true, true).fadeOut(0);
                    $(event.target).parents(".ui-draggable").find('.row-tool').stop(true, true).css({ display: 'none' }).fadeIn(300);
                    /****************************/
                    return;
                }

                if ($(event.target).is('[contenteditable]') ||
                    $(event.target).css('position') == 'absolute' ||
                    $(event.target).css('position') == 'fixed'
                    ) {
                    return;
                }

                $(event.target).parents().each(function (e) {

                    if ($(this).is('[contenteditable]') ||
                        $(this).css('position') == 'absolute' ||
                        $(this).css('position') == 'fixed'
                        ) {
                        return;
                    }

                });

                $element.data('contentbuilder').clearControls();

            });

        };

        /**** Read HTML ****/
        this.html = function () {

            var selectable = this.settings.selectable;
            if (this.settings.outline) {
                $('#temp-contentbuilder').find(selectable).css('outline', '');
            }
            $('#temp-contentbuilder').html($element.html());
            $('#temp-contentbuilder').find('.row-tool').remove();
            $('#temp-contentbuilder').find('.ovl').remove();
            $('#temp-contentbuilder').find('[contenteditable]').removeAttr('contenteditable');
            $('*[class=""]').removeAttr('class');
            $('#temp-contentbuilder').find('.ui-draggable').replaceWith(function () { return $(this).html() });

            return $('#temp-contentbuilder').html().trim();

        };

        this.zoom = function (n) {
            this.settings.zoom = n;

            $element.css('zoom', n);
            $element.css('-moz-transform', 'scale(' + n + ')');

            localStorage.zoom = n;

            this.clearControls();
        };

        this.clearControls = function () {
            $element.find('.row-tool').stop(true, true).fadeOut(0);

            if (this.settings.outline) {
                $(".ui-draggable").removeClass('ui-dragbox');
            } else {
                $(".ui-draggable").removeClass('ui-dragbox-outlined');
            }

            var selectable = this.settings.selectable;
            if (this.settings.outline) {
                $element.find(selectable).css('outline', '');
            }
            $element.find(selectable).blur();
        };

        this.viewHtml = function () {
            /**** Custom Modal ****/
            $('#md-html').css('width', '45%');
            $('#md-html').simplemodal();
            $('#md-html').data('simplemodal').show();

            $('#txtHtml').val(this.html());

            $('#btnHtmlOk').unbind('click');
            $('#btnHtmlOk').bind('click', function (e) {

                $element.html($('#txtHtml').val());

                $('#md-html').data('simplemodal').hide();

                //Re-Init
                $element.children("*").wrap("<div class='ui-draggable'></div>");
                $element.children("*").append('<div class="row-tool">' +
                    '<div class="row-handle"><i class="cb-icon-move"></i></div>' +
                    '<div class="row-html"><i class="cb-icon-code"></i></div>' +
                    '<div class="row-remove"><i class="cb-icon-trash"></i></div>' +
                    '</div>');

                //Apply builder behaviors
                $element.data('contentbuilder').applyBehavior();

                //Trigger Render event
                $element.data('contentbuilder').settings.onRender();

            });
            /**** /Custom Modal ****/
        };

        this.loadHTML = function (html) {
            $element.html(html);

            //Re-Init
            $element.children("*").wrap("<div class='ui-draggable'></div>");
            $element.children("*").append('<div class="row-tool">' +
                '<div class="row-handle"><i class="cb-icon-move"></i></div>' +
                '<div class="row-html"><i class="cb-icon-code"></i></div>' +
                '<div class="row-remove"><i class="cb-icon-trash"></i></div>' +
                '</div>');

            //Apply builder behaviors
            $element.data('contentbuilder').applyBehavior();

            //Trigger Render event
            $element.data('contentbuilder').settings.onRender();
        };

        this.applyBehavior = function () {

            //Make hyperlinks not clickable
            $element.find('a').click(function () { return false });

            //Make Editable
            var selectable = this.settings.selectable;
            $element.find(selectable).attr("contentEditable", "true");

            //Custom Image Select
            var imageselect = this.settings.imageselect;
            var fileselect = this.settings.fileselect;

            //Enable Editor & Image Embed Plugin
            var hq = this.settings.hiquality;
            $element.contenteditor({ fileselect: fileselect });
            $element.data('contenteditor').render();
            $element.find('img').each(function () {

                $(this).imageembed({ hiquality: hq, imageselect: imageselect, fileselect: fileselect });
                //to prevent icon dissapear if hovered above absolute positioned image caption
                if ($(this).parents('figure').length != 0) {
                    if ($(this).parents('figure').find('figcaption').css('position') == 'absolute') {
                        $(this).parents('figure').imageembed({ hiquality: hq, imageselect: imageselect, fileselect: fileselect });
                    }
                }

            });

            //Add "Hover on Embed" event
            $element.find(".embed-responsive").each(function () {
                if ($(this).find('.ovl').length == 0) {
                    $(this).append('<div class="ovl" style="position:absolute;background:#fff;opacity:0.2;cursor:pointer;top:0;left:0px;width:100%;height:100%;z-index:-1"></div>');
                }
            });
            $element.find(".embed-responsive").hover(function () {
                if ($(this).parents(".ui-draggable").css('outline-style') == 'none') {
                    $(this).find('.ovl').css('z-index', '1');
                }
            }, function () {
                $(this).find('.ovl').css('z-index', '-1');
            });

            //Add "Focus" event
            $element.find(selectable).unbind('focus');
            $element.find(selectable).focus(function () {

                var zoom = $element.data('contentbuilder').settings.zoom;

                var selectable = $element.data('contentbuilder').settings.selectable;

                if ($element.data('contenteditor').settings.outline) {
                    $element.find(selectable).css('outline', '');
                    $(this).css('outline', 'rgba(0, 0, 0, 0.43) dashed 1px');
                }

                /****** Row Controls ******/
                if ($element.data('contenteditor').settings.outline) {
                    $(".ui-draggable").removeClass('ui-dragbox');
                    $(this).parents(".ui-draggable").addClass('ui-dragbox');
                } else {
                    $(".ui-draggable").removeClass('ui-dragbox-outlined');
                    $(this).parents(".ui-draggable").addClass('ui-dragbox-outlined');
                }

                $element.find('.row-tool').stop(true, true).fadeOut(0);
                $(this).parents(".ui-draggable").find('.row-tool').stop(true, true).css({ display: 'none' }).fadeIn(300);

            });

            //Add "Click to Remove" event (row)
            $element.children("div").find('.row-remove').unbind();
            $element.children("div").find('.row-remove').click(function () {
                $(this).parents('.ui-draggable').fadeOut(400, function () {

                    $("#divToolImg").stop(true, true).fadeOut(0); /* CUSTOM */

                    $(this).remove();

                    //Apply builder behaviors
                    //$element.data('contentbuilder').applyBehavior();

                    //Trigger Render event
                    $element.data('contentbuilder').settings.onRender();

                });
            });

            //Add "Click to View HTML" event (row)
            $element.children("div").find('.row-html').unbind();
            $element.children("div").find('.row-html').click(function () {

                /**** Custom Modal ****/
                $('#md-html').css('width', '45%');
                $('#md-html').simplemodal();
                $('#md-html').data('simplemodal').show();

                $activeRow = $(this).parents('.ui-draggable').children('*').not('.row-tool');
                $('#temp-contentbuilder').html($activeRow.html());

                $('#temp-contentbuilder').find('[contenteditable]').removeAttr('contenteditable');
                $('#temp-contentbuilder *[class=""]').removeAttr('class');
                $('#temp-contentbuilder *[style=""]').removeAttr('style');
                $('#temp-contentbuilder .ovl').remove();
                $('#txtHtml').val($('#temp-contentbuilder').html().trim());

                $('#btnHtmlOk').unbind('click');
                $('#btnHtmlOk').bind('click', function (e) {

                    $activeRow.html($('#txtHtml').val());

                    $('#md-html').data('simplemodal').hide();

                    //Apply builder behaviors
                    $element.data('contentbuilder').applyBehavior();

                    //Trigger Render event
                    $element.data('contentbuilder').settings.onRender();

                });
                /**** /Custom Modal ****/

            });

        };

        this.init();

    };

    $.fn.contentbuilder = function (options) {
        return this.each(function () {

            if (undefined == $(this).data('contentbuilder')) {
                var plugin = new $.contentbuilder(this, options);
                $(this).data('contentbuilder', plugin);

            }
        });
    };
})(jQuery);


/*******************************************************************************************/


(function ($) {

    var $activeLink;
    var $activeElement;
    var $activeFrame;
    var instances = [];

    function instances_count() {
        //alert(instances.length);
    };

    $.fn.count = function () {
        //alert(instances.length);
    };

    $.contenteditor = function (element, options) {

        var defaults = {
            editable: "h1,h2,h3,h4,h5,h6,p,ul,ol,small,.edit",
            hasChanged: false,
            onRender: function () { },
            outline: false,
            fileselect: ''
        };

        this.settings = {};

        var $element = $(element),
             element = element;

        this.init = function () {

            this.settings = $.extend({}, defaults, options);

            //Custom File Select
            var bUseCustomFileSelect = false;
            if(this.settings.fileselect!='') bUseCustomFileSelect=true;

            /**** Localize All ****/
            if ($('#divCb').length == 0) {
                $('body').append('<div id="divCb"></div>');
            }

            var html_rte = '<div id="rte-toolbar">' +
					    '<a href="#" data-rte-cmd="bold"> <i class="cb-icon-bold"></i> </a>' +
					    '<a href="#" data-rte-cmd="italic"> <i class="cb-icon-italic"></i> </a>' +
					    '<a href="#" data-rte-cmd="underline"> <i class="cb-icon-underline"></i> </a>' +
					    '<a href="#" data-rte-cmd="strikethrough"> <i class="cb-icon-strike"></i> </a>' +
                        '<a href="#" data-rte-cmd="removeFormat"> <i class="cb-icon-eraser"></i> </a>' +
                        '<a href="#" data-rte-cmd="left"> <i class="cb-icon-align-left"></i> </a>' +
                        '<a href="#" data-rte-cmd="center"> <i class="cb-icon-align-center"></i> </a>' +
                        '<a href="#" data-rte-cmd="right"> <i class="cb-icon-align-right"></i> </a>' +
                        '<a href="#" data-rte-cmd="insertUnorderedList"> <i class="cb-icon-list-bullet" style="font-size:14px;line-height:1.3"></i> </a>' +
                        '<a href="#" data-rte-cmd="insertOrderedList"> <i class="cb-icon-list-numbered" style="font-size:14px;line-height:1.3"></i> </a>' +
					    '<a href="#" data-rte-cmd="createLink"> <i class="cb-icon-link"></i> </a>' +
					    '<a href="#" data-rte-cmd="removeElement"> <i class="cb-icon-trash"></i> </a>' +
            /*'<a href="#" data-rte-cmd="html"> <i class="cb-icon-code"></i> </a>' + */
				'</div>' +
				'' +
				'<div id="divRteLink">' +
					'<i class="cb-icon-link"></i> Edit Link' +
				'</div>' +
				'' +
				'<div id="divFrameLink">' +
					'<i class="cb-icon-link"></i> Edit Link' +
				'</div>' +
                '' +
                '<div class="md-modal" id="md-createlink">' +
			        '<div class="md-content">' +
				        '<div class="md-body">' +
                            (bUseCustomFileSelect ? '<input type="text" id="txtLink" class="inptxt" style="float:left;width:90%;" value="http:/' + '/"></input><i class="cb-icon-link md-btnbrowse" id="btnLinkBrowse" style="width:10%;"></i>' : '<input type="text" id="txtLink" class="inptxt" value="http:/' + '/"></input>') +
				        '</div>' +
					    '<div class="md-footer">' +
                            '<button id="btnLinkOk"> Ok </button>' +
                        '</div>' +
			        '</div>' +
		        '</div>' +
                '' +
                '<div class="md-modal" id="md-html">' +
			        '<div class="md-content">' +
				        '<div class="md-body">' +
                            '<textarea id="txtHtml" class="inptxt" style="height:350px;"></textarea>' +
				        '</div>' +
					    '<div class="md-footer">' +
                            '<button id="btnHtmlOk"> Ok </button>' +
                        '</div>' +
			        '</div>' +
		        '</div>' +
                '<div id="temp-contenteditor"></div>' +
                '';


            if ($('#rte-toolbar').length == 0) {

                $('#divCb').append(html_rte);

                this.prepareRteCommand('bold');
                this.prepareRteCommand('italic');
                this.prepareRteCommand('underline');
                this.prepareRteCommand('strikethrough');
                this.prepareRteCommand('undo');
                this.prepareRteCommand('redo');
                this.prepareRteStyle('left');
                this.prepareRteStyle('center');
                this.prepareRteStyle('right');
                this.prepareRteCommand('insertOrderedList');
                this.prepareRteCommand('insertUnorderedList');
                this.prepareRteCommand('removeFormat');
                this.prepareRteFormat('h1');
                this.prepareRteFormat('h2');
                this.prepareRteFormat('h3');
                this.prepareRteFormat('p');
            }


            var isCtrl = false;

            $element.bind('keydown', function (e) { /* CTRL-V */
                if (e.which == 17) {
                    isCtrl = true;
                    return;
                }
                if ((e.which == 86 && isCtrl == true) || (e.which == 86 && e.metaKey)) {

                    var savedSel = saveSelection();

                    $('#idContentWord').remove();
                    $(this).append("<textarea style='position:absolute;z-index:-1000;width:1px;height:1px;overflow:auto;' name='idContentWord' id='idContentWord'></textarea>");

                    var pasteFrame = document.getElementById("idContentWord");
                    pasteFrame.focus();

                    setTimeout(function () {
                        try {
                            restoreSelection(savedSel);
                            var $node = $(getSelectionStartNode());

                            // Insert pasted text
                            if ($('#idContentWord').length == 0) return; //protection

                            var sPastedText = $('#idContentWord').val();
                            $('#idContentWord').remove();

                            var oSel = window.getSelection();
                            var range = oSel.getRangeAt(0);
                            range.extractContents();
                            range.collapse(true);
                            var docFrag = range.createContextualFragment(sPastedText);
                            var lastNode = docFrag.lastChild;

                            range.insertNode(docFrag);

                            range.setStartAfter(lastNode);
                            range.setEndAfter(lastNode);
                            range.collapse(false);
                            var comCon = range.commonAncestorContainer;
                            if (comCon && comCon.parentNode) {
                                try { comCon.parentNode.normalize(); } catch (e) { };
                            }
                            oSel.removeAllRanges();
                            oSel.addRange(range);

                        } catch (e) {

                            $('#idContentWord').remove();
                        };

                    }, 200);
                }
            }).keyup(function (e) {
                if (e.which == 17) {
                    isCtrl = false; // no Ctrl
                }
            });

            // finish editing on click outside
            $(document).on('mousedown', function (event) {

                var bEditable = false;

                if ($('#rte-toolbar').css('display') == 'none') return;

                var el = $(event.target).prop("tagName").toLowerCase();

                if (($(event.target).is('[contenteditable]') ||
                    $(event.target).css('position') == 'absolute' ||
                    $(event.target).css('position') == 'fixed' ||
                    $(event.target).attr('id') == 'rte-toolbar') &&
                    el != 'img' &&
                    el != 'hr'
                    ) {
                    bEditable = true;
                    return;
                }

                $(event.target).parents().each(function (e) {

                    if ($(this).is('[contenteditable]') ||
                        $(this).css('position') == 'absolute' ||
                        $(this).css('position') == 'fixed' ||
                        $(this).attr('id') == 'rte-toolbar'
                        ) {
                        bEditable = true;
                        return;
                    }

                });

                if (!bEditable) {
                    $activeElement = null;

                    $('#rte-toolbar').css('display', 'none');

                    if ($element.data('contenteditor').settings.outline) {
                        for (var i = 0; i < instances.length; i++) {
                            $(instances[i]).css('outline', '');
                            $(instances[i]).find('*').css('outline', '');
                        }
                    }
                }
            });

        };


        this.render = function () {
            
            //var zoom = $element.css('zoom');
            var zoom;
            if (localStorage.getItem("zoom") != null) {
                zoom = localStorage.zoom;
            } else {
                zoom = $element.css('zoom');
            }

            if (zoom == undefined) zoom = 1;
            localStorage.zoom = zoom;

            var editable = $element.data('contenteditor').settings.editable;
            if (editable == '') {

                $element.attr('contenteditable', 'true');

                $element.unbind('mousedown');
                $element.bind('mousedown', function (e) {

                    $activeElement = $(this);

                    $("#rte-toolbar").stop(true, true).fadeIn(200);

                    if ($element.data('contenteditor').settings.outline) {
                        for (var i = 0; i < instances.length; i++) {
                            $(instances[i]).css('outline', '');
                            $(instances[i]).find('*').css('outline', '');
                        }
                        $(this).css('outline', 'rgba(0, 0, 0, 0.43) dashed 1px');
                    }

                    /*if ($(this).prop("tagName").toLowerCase() == 'a') {
                    $('#rte-toolbar a[data-rte-cmd="createLink"]').css('display', 'none');
                    } else {
                    $('#rte-toolbar a[data-rte-cmd="createLink"]').css('display', 'inline-block');
                    }*/

                });

            } else {

                $element.find(editable).each(function () {
                    var attr = $(this).attr('contenteditable');

                    if (typeof attr !== typeof undefined && attr !== false) {

                    } else {
                        $(this).attr('contenteditable', 'true');
                    }
                });

                $element.find(editable).unbind('mousedown');
                $element.find(editable).bind('mousedown', function (e) {

                    $activeElement = $(this);

                    $("#rte-toolbar").stop(true, true).fadeIn(200);
                    if ($element.data('contenteditor').settings.outline) {
                        for (var i = 0; i < instances.length; i++) {
                            $(instances[i]).css('outline', '');
                            $(instances[i]).find('*').css('outline', '');
                        }
                        $(this).css('outline', 'rgba(0, 0, 0, 0.43) dashed 1px');
                    }

                    /*if ($(this).prop("tagName").toLowerCase() == 'a') {
                    $('#rte-toolbar a[data-rte-cmd="createLink"]').css('display', 'none');
                    } else {
                    $('#rte-toolbar a[data-rte-cmd="createLink"]').css('display', 'inline-block');
                    }*/

                });

                //Kalau di dalam .edit ada contenteditable, hapus, krn tdk perlu & di IE membuat keluar handler.
                $element.find('.edit').find(editable).removeAttr('contenteditable');

            }

            /*$element.find("p").parent().bind('keyup', function (e) {
            if (e.keyCode == 13) {
            $(this).removeAttr('contenteditable');
            }
            });*/

            //Apply BR on Paragraph Enter
            //p enter ganti div gak bisa di-edit, kalo pake p buggy di IE, jadi pake <br>
            $element.find("p").unbind('keydown'); //keypress
            $element.find("p").bind('keydown', function (e) {
                /*if (e.keyCode == 13) {
                $(this).parent().attr('contenteditable', 'true');
                }*/

                if (e.keyCode == 13 && $element.find("li").length == 0) {  // don't apply br on li 

                    var UA = navigator.userAgent.toLowerCase();
                    var LiveEditor_isIE = (UA.indexOf('msie') >= 0) ? true : false;
                    if (LiveEditor_isIE) {
                        var oSel = document.selection.createRange();
                        if (oSel.parentElement) {
                            oSel.pasteHTML('<br>');
                            e.cancelBubble = true;
                            e.returnValue = false;
                            oSel.select();
                            oSel.moveEnd("character", 1);
                            oSel.moveStart("character", 1);
                            oSel.collapse(false);
                            return false;
                        }
                    } else {
                        //document.execCommand('insertHTML', false, '<br><br>');
                        //return false;

                        var oSel = window.getSelection();
                        var range = oSel.getRangeAt(0);
                        range.extractContents();
                        range.collapse(true);
                        var docFrag = range.createContextualFragment('<br>');
                        //range.collapse(false);
                        var lastNode = docFrag.lastChild;
                        range.insertNode(docFrag);
                        //try { oEditor.document.designMode = "on"; } catch (e) { }
                        range.setStartAfter(lastNode);
                        range.setEndAfter(lastNode);

                        //workaround.for unknown reason, chrome need 2 br to make new line if cursor located at the end of document.
                        if (range.endContainer.nodeType == 1) {
                            // 
                            if (range.endOffset == range.endContainer.childNodes.length - 1) {
                                range.insertNode(range.createContextualFragment("<br />"));
                                range.setStartAfter(lastNode);
                                range.setEndAfter(lastNode);
                            }
                        }
                        //

                        var comCon = range.commonAncestorContainer;
                        if (comCon && comCon.parentNode) {
                            try { comCon.parentNode.normalize(); } catch (e) { }
                        }

                        oSel.removeAllRanges();
                        oSel.addRange(range);

                        return false;
                    }

                }
            });

            $('#rte-toolbar a[data-rte-cmd="html"]').unbind('click');
            $('#rte-toolbar a[data-rte-cmd="html"]').click(function (e) {
                /**** Custom Modal ****/
                $('#md-html').css('width', '45%');
                $('#md-html').simplemodal();
                $('#md-html').data('simplemodal').show();

                $('#temp-contenteditor').html($activeElement.html());

                if ($activeElement.attr('data-html')) {
                    $('#temp-contenteditor').html($activeElement.attr('data-html')); // Addition: for script
                }

                $('#temp-contenteditor').find('[contenteditable]').removeAttr('contenteditable');
                $('#temp-contenteditor *[class=""]').removeAttr('class');
                $('#temp-contenteditor *[style=""]').removeAttr('style');
                $('#txtHtml').val($('#temp-contenteditor').html().trim());

                $('#btnHtmlOk').unbind('click');
                $('#btnHtmlOk').bind('click', function (e) {

                    if ($activeElement.html().indexOf('<script') != -1) {
                        $activeElement.attr('data-html', $('#txtHtml').val()); // Addition: for script
                    }

                    $activeElement.html($('#txtHtml').val());

                    $('#md-html').data('simplemodal').hide();

                    $element.data('contenteditor').settings.hasChanged = true;
                    $element.data('contenteditor').render();
                });
                /**** /Custom Modal ****/

                e.preventDefault(); //spy wkt rte's link btn di-click, browser scroll tetap.
                e.stopImmediatePropagation(); //spy tdk click 2x
            });

            $('#rte-toolbar a[data-rte-cmd="removeElement"]').unbind('click');
            $('#rte-toolbar a[data-rte-cmd="removeElement"]').click(function (e) {

                $activeElement.remove();

                $element.data('contenteditor').settings.hasChanged = true;
                $element.data('contenteditor').render();

                e.preventDefault();
            });

            $('#rte-toolbar a[data-rte-cmd="createLink"]').unbind('click');
            $('#rte-toolbar a[data-rte-cmd="createLink"]').click(function (e) {

                // source: 	http://stackoverflow.com/questions/6251937/how-to-get-selecteduser-highlighted-text-in-contenteditable-element-and-replac
                //   		http://stackoverflow.com/questions/4652734/return-html-from-a-user-selection/4652824#4652824
                var html = "";
                if (typeof window.getSelection != "undefined") {
                    var sel = window.getSelection();
                    if (sel.rangeCount) {
                        var container = document.createElement("div");
                        for (var i = 0, len = sel.rangeCount; i < len; ++i) {
                            container.appendChild(sel.getRangeAt(i).cloneContents());
                        }
                        html = container.innerHTML;
                    }
                } else if (typeof document.selection != "undefined") {
                    if (document.selection.type == "Text") {
                        html = document.selection.createRange().htmlText;
                    }
                }

                if (html == '') {
                    alert('Please select a text.');
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    return;
                }

                document.execCommand('createLink', false, 'http://dummy');
                $activeLink = $("a[href='http://dummy']").first();
                $activeLink.attr('href', 'http://');


                /**** Custom Modal ****/
                $('#md-createlink').css('max-width', '450px');
                $('#md-createlink').simplemodal({
                    onCancel: function () {
                        if ($activeLink.attr('href') == 'http://')
                            $activeLink.replaceWith($activeLink.html());
                    }
                });
                $('#md-createlink').data('simplemodal').show();

                $('#txtLink').val($activeLink.attr('href'));

                $('#btnLinkOk').unbind('click');
                $('#btnLinkOk').bind('click', function (e) {
                    $activeLink.attr('href', $('#txtLink').val());

                    if ($('#txtLink').val() == 'http://' || $('#txtLink').val() == '') {
                        $activeLink.replaceWith($activeLink.html());
                    }

                    $('#md-createlink').data('simplemodal').hide();

                    //$element.data('contenteditor').settings.hasChanged = true;
                    //$element.data('contenteditor').render();
                    for (var i = 0; i < instances.length; i++) {
                        $(instances[i]).data('contenteditor').settings.hasChanged = true;
                        $(instances[i]).data('contenteditor').render();
                    }

                });
                /**** /Custom Modal ****/

                e.preventDefault(); //spy wkt rte's link btn di-click, browser scroll tetap.

            });

            $element.find(".embed-responsive").unbind('hover');
            $element.find(".embed-responsive").hover(function (e) {
            
                var zoom = localStorage.zoom;
                if (zoom == 'normal') zoom = 1;
                if (zoom == undefined) zoom = 1;

                //IE fix
                zoom = zoom + ''; //Fix undefined
                if (zoom.indexOf('%') != -1) {
                    zoom = zoom.replace('%', '') / 100;
                }
                if (zoom == 'NaN') {
                    zoom = 1;
                }

                zoom = zoom*1;

                var _top; var _left;
                var scrolltop = $(window).scrollTop();
                var offsettop = $(this).offset().top;
                var offsetleft = $(this).offset().left;
                var is_firefox = navigator.userAgent.toLowerCase().indexOf('firefox') > -1;
                var is_ie = detectIE();
                var browserok = true;
                if (is_firefox||is_ie) browserok = false;
                if(browserok){
                    //Chrome 37, Opera 24
                    _top = ((offsettop - 20) * zoom) + (scrolltop - scrolltop * zoom);
                    _left = offsetleft * zoom;
                } else {
                    if(is_ie){
                        //IE 11 (Adjustment required)

                        //Custom formula for adjustment in IE11
                        var space = $element.getPos().top;
                        var adjy_val = (-space/1.1)*zoom + space/1.1;
                        var space2 = $element.getPos().left;
                        var adjx_val = -space2*zoom + space2; 

                        var p = $(this).getPos();
                        _top = ((p.top - 20) * zoom) + adjy_val;
                        _left = (p.left * zoom) + adjx_val;
                    }  
                    if(is_firefox) {
                        //Firefox (No Adjustment required)
                        _top = offsettop - 20;
                        _left = offsetleft;
                    }
                }
                $("#divFrameLink").css("top", _top + "px");
                $("#divFrameLink").css("left", _left + "px");


                $("#divFrameLink").stop(true, true).css({ display: 'none' }).fadeIn(20);

                $activeFrame = $(this).find('iframe');

                $("#divFrameLink").unbind('click');
                $("#divFrameLink").bind('click', function (e) {

                    /**** Custom Modal ****/
                    $('#md-createlink').css('max-width', '450px');
                    $('#md-createlink').simplemodal();
                    $('#md-createlink').data('simplemodal').show();

                    $('#txtLink').val($activeFrame.attr('src'));

                    $('#btnLinkOk').unbind('click');
                    $('#btnLinkOk').bind('click', function (e) {
                        
                        var srcUrl = $('#txtLink').val();

                        var youRegex = /^http[s]?:\/\/(((www.youtube.com\/watch\?(feature=player_detailpage&)?)v=)|(youtu.be\/))([^#\&\?]*)/;
                        var vimeoRegex = /^.*(vimeo\.com\/)((channels\/[A-z]+\/)|(groups\/[A-z]+\/videos\/)|(video\/))?([0-9]+)\/?/;
                        var youRegexMatches = youRegex.exec(srcUrl);
                        var vimeoRegexMatches = vimeoRegex.exec(srcUrl); 
                        if (youRegexMatches != null || vimeoRegexMatches != null) {
                            if (youRegexMatches != null && youRegexMatches.length >= 7) {
                                var youMatch = youRegexMatches[6];
                                srcUrl = '//www.youtube.com/embed/' + youMatch + '?rel=0';
                            }
                            if (vimeoRegexMatches != null && vimeoRegexMatches.length >= 7) {
                                var vimeoMatch = vimeoRegexMatches[6];
                                srcUrl = '//player.vimeo.com/video/' + vimeoMatch;
                            }
                        }
                        $activeFrame.attr('src', srcUrl);

                        if ($('#txtLink').val() == '') {
                            $activeFrame.attr('src', '');
                        }

                        $('#md-createlink').data('simplemodal').hide();

                        //$element.data('contenteditor').settings.hasChanged = true;
                        //$element.data('contenteditor').render();
                        for (var i = 0; i < instances.length; i++) {
                            $(instances[i]).data('contenteditor').settings.hasChanged = true;
                            $(instances[i]).data('contenteditor').render();
                        }

                    });
                    /**** /Custom Modal ****/

                });

                $("#divFrameLink").hover(function (e) {
                    $(this).stop(true, true).css("display", "block"); // Spy tdk flickr
                }, function () {
                    $(this).stop(true, true).fadeOut(0);
                });

            }, function (e) {
                $("#divFrameLink").stop(true, true).fadeOut(0);
            });

            $element.find('a').not('.not-a').unbind('hover');
            $element.find('a').not('.not-a').hover(function (e) {

                if ($(this).children('img').length == 1 && $(this).children().length == 1) return;

                var zoom = localStorage.zoom;
                if (zoom == 'normal') zoom = 1;
                if (zoom == undefined) zoom = 1;

                //IE fix
                zoom = zoom + ''; //Fix undefined
                if (zoom.indexOf('%') != -1) {
                    zoom = zoom.replace('%', '') / 100;
                }
                if (zoom == 'NaN') {
                    zoom = 1;
                }
                
                zoom = zoom*1;

                var _top; var _left;
                var scrolltop = $(window).scrollTop();
                var offsettop = $(this).offset().top;
                var offsetleft = $(this).offset().left;
                var is_firefox = navigator.userAgent.toLowerCase().indexOf('firefox') > -1;
                var is_ie = detectIE();
                var browserok = true;
                if (is_firefox||is_ie) browserok = false;
                if(browserok){
                    //Chrome 37, Opera 24
                    _top = ((offsettop - 20) * zoom) + (scrolltop - scrolltop * zoom);
                    _left = offsetleft * zoom;
                } else {
                    if(is_ie){
                        //IE 11 (Adjustment required)

                        //Custom formula for adjustment in IE11
                        var space = $element.getPos().top;
                        var adjy_val = (-space/1.1)*zoom + space/1.1; 
                        var space2 = $element.getPos().left;
                        var adjx_val = -space2*zoom + space2; 
                        
                        var p = $(this).getPos();
                        _top = ((p.top - 20) * zoom) + adjy_val;
                        _left = (p.left * zoom) + adjx_val;
                    } 
                    if(is_firefox) {
                        //Firefox (No Adjustment required)
                        _top = offsettop - 20;
                        _left = offsetleft;
                    }
                }
                $("#divRteLink").css("top", _top + "px");
                $("#divRteLink").css("left", _left + "px");


                $("#divRteLink").stop(true, true).css({ display: 'none' }).fadeIn(20);

                $activeLink = $(this);

                $("#divRteLink").unbind('click');
                $("#divRteLink").bind('click', function (e) {

                    /**** Custom Modal ****/
                    $('#md-createlink').css('max-width', '450px');
                    $('#md-createlink').simplemodal();
                    $('#md-createlink').data('simplemodal').show();

                    $('#txtLink').val($activeLink.attr('href'));

                    $('#btnLinkOk').unbind('click');
                    $('#btnLinkOk').bind('click', function (e) {

                        $activeLink.attr('href', $('#txtLink').val());

                        if ($('#txtLink').val() == 'http://' || $('#txtLink').val() == '') {
                            $activeLink.replaceWith($activeLink.html());
                        }

                        $('#md-createlink').data('simplemodal').hide();

                        //$element.data('contenteditor').settings.hasChanged = true;
                        //$element.data('contenteditor').render();
                        for (var i = 0; i < instances.length; i++) {
                            $(instances[i]).data('contenteditor').settings.hasChanged = true;
                            $(instances[i]).data('contenteditor').render();
                        }

                    });
                    /**** /Custom Modal ****/

                });


                $("#divRteLink").hover(function (e) {
                    $(this).stop(true, true).css("display", "block"); // Spy tdk flickr
                }, function () {
                    $(this).stop(true, true).fadeOut(0);
                });

            }, function (e) {
                $("#divRteLink").stop(true, true).fadeOut(0);
            });

            //Custom File Select
            $("#btnLinkBrowse").unbind('click');
            $("#btnLinkBrowse").bind('click', function (e) {

                //Clear Controls
                $("#divToolImg").stop(true, true).fadeOut(0);
                $("#divToolImgSettings").stop(true, true).fadeOut(0);
                $("#divRteLink").stop(true, true).fadeOut(0);
                $("#divFrameLink").stop(true, true).fadeOut(0);

                $('#active-input').val('txtLink');

                /**** Custom Modal ****/
                $('#md-fileselect').css('width', '65%');
                $('#md-fileselect').simplemodal();
                $('#md-fileselect').data('simplemodal').show();
                /**** /Custom Modal ****/

            });

            $element.data('contenteditor').settings.onRender();

        };

        this.prepareRteCommand = function (s) {
            $('#rte-toolbar a[data-rte-cmd="' + s + '"]').unbind('click');
            $('#rte-toolbar a[data-rte-cmd="' + s + '"]').click(function (e) {
                try {
                    document.execCommand(s, false, null);
                } catch (e) {
                    //FF fix
                    $activeElement.parents('div').addClass('edit');
                    var el;
                    if (window.getSelection) {//https://www.jabcreations.com/blog/javascript-parentnode-of-selected-text
                        el = window.getSelection().getRangeAt(0).commonAncestorContainer.parentNode;
                        el = el.parentNode;
                    }
                    else if (document.selection) {
                        el = document.selection.createRange().parentElement();
                        el = el.parentElement();
                    }
                    //alert(el.nodeName)
                    el.setAttribute('contenteditable', true);
                    document.execCommand(s, false, null);
                    el.removeAttribute('contenteditable');
                    $element.data('contenteditor').render();
                }

                $(this).blur();

                $element.data('contenteditor').settings.hasChanged = true;

                e.preventDefault();
            });
        };

        this.prepareRteStyle = function (s) {
            $('#rte-toolbar a[data-rte-cmd="' + s + '"]').unbind('click');
            $('#rte-toolbar a[data-rte-cmd="' + s + '"]').click(function (e) {

                var el;
                if (window.getSelection) {//https://www.jabcreations.com/blog/javascript-parentnode-of-selected-text
                    el = window.getSelection().getRangeAt(0).commonAncestorContainer.parentNode;
                    //TODO
                    if (el.nodeName != 'H1' && el.nodeName != 'H2' && el.nodeName != 'H3' &&
                        el.nodeName != 'H4' && el.nodeName != 'H5' && el.nodeName != 'H6' &&
                        el.nodeName != 'P') {
                        el = el.parentNode;
                    }
                }
                else if (document.selection) {
                    el = document.selection.createRange().parentElement();
                    if (el.nodeName != 'H1' && el.nodeName != 'H2' && el.nodeName != 'H3' &&
                        el.nodeName != 'H4' && el.nodeName != 'H5' && el.nodeName != 'H6' &&
                        el.nodeName != 'P') {
                        el = el.parentElement();
                    }
                }
                if (s == 'center' || s == 'left' || s == 'right') {
                    el.style.textAlign = s;
                }

                $(this).blur();

                $element.data('contenteditor').settings.hasChanged = true;

                e.preventDefault();
            });
        };

        this.prepareRteFormat = function (s) {
            $('#rte-toolbar a[data-rte-cmd="' + s + '"]').unbind('click');
            $('#rte-toolbar a[data-rte-cmd="' + s + '"]').click(function (e) {
                document.execCommand('formatBlock', null, '<' + s + '>');
                $(this).blur();

                $element.data('contenteditor').settings.hasChanged = true;

                e.preventDefault();
            });
        };


        this.init();
    };

    $.fn.contenteditor = function (options) {

        return this.each(function () {

            instances.push(this);

            if (undefined == $(this).data('contenteditor')) {
                var plugin = new $.contenteditor(this, options);
                $(this).data('contenteditor', plugin);

            }

        });
    };
})(jQuery);



// source: http://stackoverflow.com/questions/5605401/insert-link-in-contenteditable-element 
var savedSel;
function saveSelection() {
    if (window.getSelection) {
        sel = window.getSelection();
        if (sel.getRangeAt && sel.rangeCount) {
            var ranges = [];
            for (var i = 0, len = sel.rangeCount; i < len; ++i) {
                ranges.push(sel.getRangeAt(i));
            }
            return ranges;
        }
    } else if (document.selection && document.selection.createRange) {
        return document.selection.createRange();
    }
    return null;
};
function restoreSelection(savedSel) {
    if (savedSel) {
        if (window.getSelection) {
            sel = window.getSelection();
            sel.removeAllRanges();
            for (var i = 0, len = savedSel.length; i < len; ++i) {
                sel.addRange(savedSel[i]);
            }
        } else if (document.selection && savedSel.select) {
            savedSel.select();
        }
    }
};
// source: http://stackoverflow.com/questions/2459180/how-to-edit-a-link-within-a-contenteditable-div 
function getSelectionStartNode() {
    var node, selection;
    if (window.getSelection) { // FF3.6, Safari4, Chrome5 (DOM Standards)
        selection = getSelection();
        node = selection.anchorNode;
    }
    if (!node && document.selection) { // IE
        selection = document.selection;
        var range = selection.getRangeAt ? selection.getRangeAt(0) : selection.createRange();
        node = range.commonAncestorContainer ? range.commonAncestorContainer :
			   range.parentElement ? range.parentElement() : range.item(0);
    }
    if (node) {
        return (node.nodeName == "#text" ? node.parentNode : node);
    }
};
//
var getSelectedNode = function () {
    var node, selection;
    if (window.getSelection) {
        selection = getSelection();
        node = selection.anchorNode;
    }
    if (!node && document.selection) {
        selection = document.selection;
        var range = selection.getRangeAt ? selection.getRangeAt(0) : selection.createRange();
        node = range.commonAncestorContainer ? range.commonAncestorContainer :
               range.parentElement ? range.parentElement() : range.item(0);
    }
    if (node) {
        return (node.nodeName == "#text" ? node.parentNode : node);
    }
};


/*******************************************************************************************/


(function ($) {

    var tmpCanvas;
    var nInitialWidth;
    var nInitialHeight;
    var $imgActive;

    $.imageembed = function (element, options) {

        var defaults = {
            hiquality: false,
            imageselect: '',
            fileselect: ''
        };

        this.settings = {};

        var $element = $(element),
                    element = element;

        this.init = function () {

            this.settings = $.extend({}, defaults, options);

            /**** Localize All ****/
            if ($('#divCb').length == 0) {
                $('body').append('<div id="divCb"></div>');
            }

            var html_photo_file = '';
            var html_photo_file2 = '';
            if (navigator.appName.indexOf('Microsoft') != -1) {
                html_photo_file = '<div id="divToolImg"><div class="fileinputs"><input type="file" name="file" class="my-file" /><div class="fakefile"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADwAAAA8CAYAAAA6/NlyAAAC+klEQVRoQ+2au24aQRSGz+ySkEvPA9AQubNEhXgCSogEShmZGkSQpTS8AjUNSAjXlCRNStpQ8QK8AI6UOLazM5lZvGRvswsz43hYz0iWZe3uzPnOf25rQOVymcAzWsgAZ1xto3DGBQajsFE4Yx4wIZ0xQSM4RmGjcMY8YEI6Y4LKFy0H/9TCJ7b1VsiOo0PaAAv5Wf4ho/CBPjQhneYokRyezWZQKpW4WzuOA71eD5bLZdrx++vahnSz2YRutwu5XC4RZrPZQL1eP33g4XAI1Wo1FeRYlbVQ+FA1U+kfblitVtBut2Nvf3LgQqEAk8kE2G9VC2MM4/EYRqNRZMsnBy4WizCdTiGfz6vidffhqaw98Ha7hU6nA+v1OuCQfr8PLBV46ySB/bAeoL8qJ0GfHLA/D8P9OOmap/jJAXvq1mq12NB1lW404LL/GVqtD5QTPfwwZEJz+DtcXHwEDPf0z3+f+2mbw17oxvZjhIBgGz71LqFSqcQ6xK8wgT+AyZ0L/t+AMflNz3MiNYZXpXkKI2SDhfKw3V67xYwXAdGQJhT6lj77SqgbHP3ywMLMITeB8GIn84C9PJ3P5/s+vYPdGbxYLGAwGABv3k4aPkSIBYAZMg0tfBs4L6kP+yvy7OoKzt6dg3+UTJrQtABmpOHQThs8PGjbeuMrSuDmbdLLhTbAYZXTgJmTEMrBj+sbbs6yPb1KzMIewOJOWiLh7Nog85UH/7vxobO0bb12QYJrV4jCxZA56OuXb26Oq1pSwOGwTgtPz2gLvaRqv9gzOORXpAiyiywN3jdagXtlwaWACbnf9UWBxdRjbWmnLA1l3qK92kYs79UsOeCYaq3GrOAuokNGnC1SwLRWg4NpT37kpREwHUIwzb9HXs8LWKccZsKK/Nv24IBwYdkIGm5jB+8QuVEyh+WA2XDBqjVygfyvheJAaU9KA6cdoNt1A6ybIqrtMQqr9qhu+xmFdVNEtT1GYdUe1W0/o7Buiqi2xyis2qO67WcU1k0R1fb8BZv85KDCNGIQAAAAAElFTkSuQmCC" /></div></div></div>';
                html_photo_file2 = '';
            } else {
                html_photo_file = '<div style="display:none"><input type="file" name="file" class="my-file"></div>';
                html_photo_file2 = '<div id="divToolImg">' +
                        '<i id="lnkEditImage" class="cb-icon-camera"></i>' +
                    '</div>';
            };

            var html_photo_tool = '<div id="divTempContent" style="display:none"></div>' +
                    '<div class="overlay-bg" style="position:fixed;top:0;left:0;width:1;height:1;z-index:10000;zoom 1;background:#fff;opacity:0.8"></div>' +
                    '<div id="divImageEdit" style="position:absolute;display:none;z-index:10000">' +
                        '<div id="my-mask" style="width:200px;height:200px;overflow:hidden;">' +
                            '<img id="my-image" src="" style="max-width:none" />' +
                        '</div>' +
                        '<div id="img-control" style="margin-top:1px;position:absolute;top:5px;left:7px;opacity:0.8">' +
					        '<input id="btnImageCancel" type="button" value="Cancel" /> ' +
                            '<input id="btnZoomOut" type="button" value="-" /> ' +
                            '<input id="btnZoomIn" type="button" value="+" /> ' +
                            '<input id="btnChangeImage" type="button" value="Ok" />' +
                        '</div>' +
                    '</div>' +
                    '<div style="display:none">' +
                        '<canvas id="myCanvas"></canvas>' +
				        '<canvas id="myTmpCanvas"></canvas>' +
                    '</div>' +
                    '<form id="canvasform" method="post" action="" target="canvasframe" enctype="multipart/form-data">' +
                        html_photo_file +
                        '<input id="hidImage" name="hidImage" type="hidden" />' +
                        '<input id="hidPath" name="hidPath" type="hidden" />' +
                        '<input id="hidFile" name="hidFile" type="hidden" />' +
				        '<input id="hidRefId" name="hidRefId" type="hidden" />' +
				        '<input id="hidImgType" name="hidImgType" type="hidden" />' +
                    '</form>' +
                    '<iframe id="canvasframe" name="canvasframe" style="width:1px;height:1px;border:none;visibility:hidden;position:absolute"></iframe>';

            //Custom Image Select
            var bUseCustomImageSelect = false;
            if(this.settings.imageselect!='') bUseCustomImageSelect=true;

            //Custom File Select
            var bUseCustomFileSelect = false;
            if(this.settings.fileselect!='') bUseCustomFileSelect=true;

            var html_hover_icons = html_photo_file2 +
                    '<div id="divToolImgSettings">' +
                        '<i id="lnkImageSettings" class="cb-icon-link"></i>' +
                    '</div>' +
                    '' +
                    '<div class="md-modal" id="md-img">' +
			            '<div class="md-content">' +
				            '<div class="md-body">' +
                                '<div class="md-label">Image URL:</div>' +
                                (bUseCustomImageSelect ? '<input type="text" id="txtImgUrl" class="inptxt" style="float:left;width:50%"></input><i class="cb-icon-link md-btnbrowse" id="btnImageBrowse" style="width:10%;"></i>' : '<input type="text" id="txtImgUrl" class="inptxt" style="float:left;width:60%"></input>') +
                                '<br style="clear:both">' +
                                '<div class="md-label">Alternate Text:</div>' +
                                '<input type="text" id="txtAltText" class="inptxt" style="float:right;width:60%"></input>' +
                                '<br style="clear:both">' +
                                '<div class="md-label">Navigate URL:</div>' +
                                (bUseCustomFileSelect ? '<input type="text" id="txtLinkUrl" class="inptxt" style="float:left;width:50%"></input><i class="cb-icon-link md-btnbrowse" id="btnFileBrowse" style="width:10%;"></i>' : '<input type="text" id="txtLinkUrl" class="inptxt" style="float:left;width:60%"></input>') +
				            '</div>' +
					        '<div class="md-footer">' +
                                '<button id="btnImgOk"> Ok </button>' +
                            '</div>' +
			            '</div>' +
		            '</div>' +
                    '' +
                    '<div class="md-modal" id="md-imageselect">' +
			            '<div class="md-content">' +
				            '<div class="md-body">' +
                                (bUseCustomImageSelect ? '<iframe id="ifrImageBrowse" style="width:100%;height:400px;border: none;display: block;" src="' + this.settings.imageselect + '"></iframe>' : '') +
				            '</div>' +
			            '</div>' +
		            '</div>' +
                    '' +
                    '<div class="md-modal" id="md-fileselect">' +
			            '<div class="md-content">' +
				            '<div class="md-body">' +
                                (bUseCustomFileSelect ? '<iframe id="ifrFileBrowse" style="width:100%;height:400px;border: none;display: block;" src="' + this.settings.fileselect + '"></iframe>' : '') +
				            '</div>' +
			            '</div>' +
		            '</div>' +
                    '' +
                    '<input type="hidden" id="active-input" />';

            if ($('#divToolImg').length == 0) {
                $('#divCb').append(html_photo_tool);
                $('#divCb').append(html_hover_icons);
            }


            tmpCanvas = document.getElementById('myTmpCanvas');

            $('.my-file[type=file]').change(function (e) {
                changeImage(e);

                $('#my-image').attr('src', ''); //reset

                if (!$imgActive.parent().attr('data-gal')) {
                    //alert('no lightbox');
                    $(this).clearInputs(); //=> won't upload the large file (by clearing file input.my-file)
                }

            });

            $element.hover(function (e) {

                var zoom;
                if (localStorage.getItem("zoom") != null) {
                    zoom = localStorage.zoom;
                } else {
                    zoom = $element.parents('[style*="zoom"]').css('zoom');
                    if (zoom == 'normal') zoom = 1;
                    if (zoom == undefined) zoom = 1;
                }

                //FF fix
                var is_firefox = navigator.userAgent.toLowerCase().indexOf('firefox') > -1;
                //if (is_firefox) zoom = '1';

                //IE fix
                zoom = zoom + ''; //Fix undefined
                if (zoom.indexOf('%') != -1) {
                    zoom = zoom.replace('%', '') / 100;
                }
                if (zoom == 'NaN') {
                    zoom = 1;
                }

                localStorage.zoom = zoom;

                zoom = zoom*1;

                /*var adjy = $element.data('imageembed').settings.adjy*1;
                var adjy_val = (-adjy/0.2)*zoom + (adjy/0.2);
                var adjH = -30;
                var adjW = -30;
                var p = $(this).getPos();

                $("#divToolImg").css("top", ((p.top + parseInt($(this).css('height')) / 2) + adjH) * zoom + adjy_val + "px");
                $("#divToolImg").css("left", ((p.left + parseInt($(this).css('width')) / 2) + adjW) * zoom + "px");
                $("#divToolImg").stop(true, true).css({ display: 'none' }).fadeIn(20);

                $("#divToolImgSettings").css("top", (((p.top + parseInt($(this).css('height')) / 2) + adjH) * zoom) - 40 + adjy_val + "px");
                $("#divToolImgSettings").css("left", (((p.left + parseInt($(this).css('width')) / 2) + adjW) * zoom) + "px");
                $("#divToolImgSettings").stop(true, true).css({ display: 'none' }).fadeIn(20);*/

                var _top; var _top2; var _left;
                var scrolltop = $(window).scrollTop();
                var offsettop = $(this).offset().top;
                var offsetleft = $(this).offset().left;
                var is_firefox = navigator.userAgent.toLowerCase().indexOf('firefox') > -1;
                var is_ie = detectIE();
                var browserok = true;
                if (is_firefox||is_ie) browserok = false;
                if(browserok){
                    //Chrome 37, Opera 24
                    _top = ((offsettop + parseInt($(this).css('height')) / 2) - 30) * zoom  + (scrolltop - scrolltop * zoom) ;
                    _left = ((offsetleft + parseInt($(this).css('width')) / 2) - 30) * zoom;
                    _top2 = _top - 40;
                } else {
                    if(is_ie){
                        //IE 11 (Adjustment required)

                        //Custom formula for adjustment in IE11
                        var space = 0; var space2 = 0;
                        $element.parents().each(function () {
                            if ($(this).data('contentbuilder')) {
                                space = $(this).getPos().top;
                                space2 = $(this).getPos().left;
                            }
                        });
                        var adjy_val = -space*zoom + space;
                        var adjx_val = -space2*zoom + space2; 

                        var p = $(this).getPos();
                        _top = ((p.top - 30 + parseInt($(this).css('height')) / 2)) * zoom + adjy_val;
                        _left = ((p.left - 30 + parseInt($(this).css('width')) / 2)) * zoom + adjx_val;
                        _top2 = _top - 40;

                    }
                    if(is_firefox) {
                        //Firefox (No Adjustment required)
                        var imgwidth = parseInt($(this).css('width'));
                        var imgheight = parseInt($(this).css('height'));
                        
                        _top = offsettop - 25 + imgheight*zoom/2;
                        _left = offsetleft - 25 + imgwidth*zoom/2;
                        _top2 = _top - 40;
                    }
                }
                $("#divToolImg").css("top", _top + "px");
                $("#divToolImg").css("left", _left + "px");
                $("#divToolImg").stop(true, true).css({ display: 'none' }).fadeIn(20);

                $("#divToolImgSettings").css("top", _top2 + "px");
                $("#divToolImgSettings").css("left", _left + "px");
                $("#divToolImgSettings").stop(true, true).css({ display: 'none' }).fadeIn(20);


                $imgActive = $(this);

                $("#divToolImg").unbind('click');
                $("#divToolImg").bind('click', function (e) {

                    $(this).data('image', $imgActive); //img1: Simpan wkt click browse, krn @imgActive berubah2 tergantung hover

                    $('input.my-file[type=file]').click();

                    e.preventDefault();
                    e.stopImmediatePropagation();
                });
                $("#divToolImg").unbind('hover');
                $("#divToolImg").hover(function (e) {
                    $("#divToolImg").stop(true, true).css("display", "block"); /* Spy tdk flickr */
                    $("#divToolImgSettings").stop(true, true).css("display", "block"); /* Spy tdk flickr */
                }, function () {
                    $("#divToolImg").stop(true, true).fadeOut(0);
                });
                $element.find('figcaption').unbind('hover');
                $element.find('figcaption').hover(function (e) {
                    $("#divToolImg").stop(true, true).css("display", "block"); /* Spy tdk flickr */
                    $("#divToolImgSettings").stop(true, true).css("display", "block"); /* Spy tdk flickr */
                }, function () {
                    $("#divToolImg").stop(true, true).fadeOut(0);
                });
                $("#divToolImgSettings").unbind('hover');
                $("#divToolImgSettings").hover(function (e) {
                    $("#divToolImg").stop(true, true).css("display", "block"); /* Spy tdk flickr */
                    $("#divToolImgSettings").stop(true, true).css("display", "block"); /* Spy tdk flickr */
                }, function () {
                    $("#divToolImgSettings").stop(true, true).fadeOut(0);
                });


                $("#lnkImageSettings").unbind('click');
                $("#lnkImageSettings").bind('click', function (e) {

                    $(this).data('image', $imgActive); //img1: Simpan wkt click browse, krn @imgActive berubah2 tergantung hover

                    //Clear Controls
                    $("#divToolImg").stop(true, true).fadeOut(0);
                    $("#divToolImgSettings").stop(true, true).fadeOut(0);

                    /**** Custom Modal ****/
                    $('#md-img').css('width', '45%');
                    $('#md-img').simplemodal();
                    $('#md-img').data('simplemodal').show();

                    //Check if hovered element is <img> or <figure>
                    var $img = $element;
                    if ($element.prop("tagName").toLowerCase() == 'figure') {
                        $img = $element.find('img:first');
                    }

                    //Get image properties (src, alt & link)
                    $('#txtImgUrl').val($img.attr('src'));
                    $('#txtAltText').val($img.attr('alt'));
                    $('#txtLinkUrl').val('');
                    if ($img.parents('a:first') != undefined) {
                        $('#txtLinkUrl').val($img.parents('a:first').attr('href'));
                    }

                    $('#btnImgOk').unbind('click');
                    $('#btnImgOk').bind('click', function (e) {

                        //Get Content Builder plugin
                        var builder;
                        $element.parents().each(function () {
                            if ($(this).data('contentbuilder')) {
                                builder = $(this).data('contentbuilder');
                            }
                        });

                        //Set image properties
                        $img.attr('src', $('#txtImgUrl').val());
                        $img.attr('alt', $('#txtAltText').val());
                        if ($('#txtLinkUrl').val() == 'http://' || $('#txtLinkUrl').val() == '') {
                            //remove link
                            $img.parents('a:first').replaceWith($img.parents('a:first').html());
                        } else {
                            if ($img.parents('a:first').length == 0) {
                                //create link
                                $img.wrap('<a href="' + $('#txtLinkUrl').val() + '"></a>');
                            } else {
                                //apply link
                                $img.parents('a:first').attr('href', $('#txtLinkUrl').val());
                            }
                        }

                        //Apply Content Builder Behavior
                        if (builder) builder.applyBehavior();

                        $('#md-img').data('simplemodal').hide();

                    });
                    /**** /Custom Modal ****/

                    e.preventDefault();
                    e.stopImmediatePropagation();
                });

                //Custom Image Select
                $("#btnImageBrowse").unbind('click');
                $("#btnImageBrowse").bind('click', function (e) {

                    //Clear Controls
                    $("#divToolImg").stop(true, true).fadeOut(0);
                    $("#divToolImgSettings").stop(true, true).fadeOut(0);
                    $("#divRteLink").stop(true, true).fadeOut(0);
                    $("#divFrameLink").stop(true, true).fadeOut(0);

                    $('#active-input').val('txtImgUrl');
       
                    /**** Custom Modal ****/
                    $('#md-imageselect').css('width', '65%');
                    $('#md-imageselect').simplemodal();
                    $('#md-imageselect').data('simplemodal').show();
                    /**** /Custom Modal ****/

                });

                //Custom File Select
                $("#btnFileBrowse").unbind('click');
                $("#btnFileBrowse").bind('click', function (e) {

                    //Clear Controls
                    $("#divToolImg").stop(true, true).fadeOut(0);
                    $("#divToolImgSettings").stop(true, true).fadeOut(0);
                    $("#divRteLink").stop(true, true).fadeOut(0);
                    $("#divFrameLink").stop(true, true).fadeOut(0);

                    $('#active-input').val('txtLinkUrl');

                    /**** Custom Modal ****/
                    $('#md-fileselect').css('width', '65%');
                    $('#md-fileselect').simplemodal();
                    $('#md-fileselect').data('simplemodal').show();
                    /**** /Custom Modal ****/

                });

            }, function (e) {
                $("#divToolImg").stop(true, true).fadeOut(0);
                $("#divToolImgSettings").stop(true, true).fadeOut(0);
            });

        };


        /* IMAGE OPERATION */
        var changeImage = function (e) {
            if (typeof FileReader == "undefined") return true;

            var elem = $(this);
            var files = e.target.files;

            var hiquality = false;
            try {
                hiquality = $element.data('imageembed').settings.hiquality;
            } catch (e) { };

            for (var i = 0, file; file = files[i]; i++) {

                var imgname = file.name;
                var extension = imgname.substr((imgname.lastIndexOf('.') + 1)).toLowerCase();
                if (extension == 'jpg' || extension == 'jpeg' || extension == 'png' || extension == 'gif' || extension == 'bmp') {

                } else {
                    alert('Please select an image');
                    return;
                }

                if (file.type.match('image.*')) {
                    var reader = new FileReader();
                    reader.onload = (function (theFile) {
                        return function (e) {
                            var image = e.target.result;

                            $imgActive = $("#divToolImg").data('image'); //img2: Selang antara klik browse & select image, hover diabaikan. $imgActive di-set dgn image yg active wkt klik browse.

                            var zoom = localStorage.zoom;
                            if ($imgActive.prop("tagName").toLowerCase() == 'img') {
                                $("#my-mask").css('width', $imgActive.width() + 'px');
                                $("#my-mask").css('height', $imgActive.height() + 'px');
                            } else {
                                $("#my-mask").css('width', $imgActive.innerWidth() + 'px');
                                $("#my-mask").css('height', $imgActive.innerHeight() + 'px');
                            }
                            $("#my-mask").css('zoom', zoom);
                            $("#my-mask").css('-moz-transform', 'scale(' + zoom + ')');

                            var oimg = new Image();
                            oimg.onload = function (evt) {

                                $imgActive = $("#divToolImg").data('image'); //img2: Selang antara klik browse & select image, hover diabaikan. $imgActive di-set dgn image yg active wkt klik browse.

                                nInitialWidth = this.width;
                                nInitialHeight = this.height;

                                var newW;
                                var newY;

                                /* source: http://stackoverflow.com/questions/3987644/resize-and-center-image-with-jquery */
                                var maskWidth = $imgActive.width();
                                var maskHeight = $imgActive.height();
                                var photoAspectRatio = nInitialWidth / nInitialHeight;
                                var canvasAspectRatio = maskWidth / maskHeight;
                                if (photoAspectRatio < canvasAspectRatio) {
                                    newW = maskWidth;
                                    newY = (nInitialHeight * maskWidth) / nInitialWidth;
                                }
                                else {
                                    newW = (nInitialWidth * maskHeight) / nInitialHeight;
                                    newY = maskHeight;
                                }
                                this.width = newW;
                                this.height = newY;
                                /* --------- */


                                $('#my-image').attr('src', image);
                                $('#my-image').on('load', function () {

                                    $('.overlay-bg').css('width', '100%');
                                    $('.overlay-bg').css('height', '100%');

                                    $imgActive = $("#divToolImg").data('image'); //img2: Selang antara klik browse & select image, hover diabaikan. $imgActive di-set dgn image yg active wkt klik browse.

                                    $("#my-image").css('top', '0px');
                                    $("#my-image").css('left', '0px');

                                    $("#my-image").css('width', newW + 'px');
                                    $("#my-image").css('height', newY + 'px');

                                    var zoom = localStorage.zoom;

                                    zoom = zoom*1;

                                    /*var adjy = $element.data('imageembed').settings.adjy*1;
                                    var adjy_val = (-adjy/0.183)*zoom + (adjy/0.183);

                                    var p = $imgActive.getPos();
                                    $('#divImageEdit').css('display', 'inline-block');
                                    if ($imgActive.attr('class') == 'img-polaroid') {
                                        $("#divImageEdit").css("top", (p.top + 5) * zoom + adjy_val + "px");
                                        $("#divImageEdit").css("left", (p.left + 5) * zoom + "px");
                                    } else {
                                        $("#divImageEdit").css("top", (p.top) * zoom + adjy_val + "px");
                                        $("#divImageEdit").css("left", (p.left) * zoom + "px");
                                    }*/
                                    var _top; var _left; var _top_polaroid; var _left_polaroid;
                                    var scrolltop = $(window).scrollTop();
                                    var offsettop = $imgActive.offset().top;
                                    var offsetleft = $imgActive.offset().left;
                                    var is_firefox = navigator.userAgent.toLowerCase().indexOf('firefox') > -1;
                                    var is_ie = detectIE();
                                    var browserok = true;
                                    if (is_firefox||is_ie) browserok = false;
                                    if(browserok){
                                        //Chrome 37, Opera 24
                                        _top = (offsettop * zoom) + (scrolltop - scrolltop * zoom);
                                        _left = offsetleft * zoom;
                                        _top_polaroid = ((offsettop + 5) * zoom) + (scrolltop - scrolltop * zoom);
                                        _left_polaroid = (offsetleft + 5) * zoom;
                                    } else {
                                        if(is_ie){
                                            //IE 11 (Adjustment required)

                                            //Custom formula for adjustment in IE11
                                            var space = 0;var space2 = 0;
                                            $element.parents().each(function () {
                                                if ($(this).data('contentbuilder')) {
                                                    space = $(this).getPos().top;
                                                    space2 = $(this).getPos().left;
                                                }
                                            });
                                            var adjy_val = -space*zoom + space; 
                                            var adjx_val = -space2*zoom + space2; 

                                            var p = $imgActive.getPos();
                                            _top = (p.top * zoom) + adjy_val;
                                            _left = (p.left * zoom) + adjx_val;
                                            _top_polaroid = ((p.top + 5) * zoom) + adjy_val;
                                            _left_polaroid = ((p.left + 5) * zoom) + adjx_val;
                                        } 
                                        if(is_firefox) {
                                            //Firefox (No Adjustment required)
                                            /*
                                            In Firefox, if my-mask is zoomed, it will be centered within it's container divImageEdit.
                                            Only because of this, an adjustment is needed for divImageEdit & img-control
                                            */
                                            var imgwidth = parseInt($imgActive.css('width'));
                                            var imgheight = parseInt($imgActive.css('height'));
                                            var adjx_val = imgwidth/2 - (imgwidth/2)*zoom;
                                            var adjy_val = imgheight/2 - (imgheight/2)*zoom;

                                            $('#img-control').css('top',5+adjy_val + 'px');
                                            $('#img-control').css('left',7+adjx_val + 'px');

                                            _top = offsettop-adjy_val;
                                            _left = offsetleft-adjx_val;
                                            _top_polaroid = offsettop-adjy_val + 5;
                                            _left_polaroid = offsetleft-adjx_val + 5;
                                        }
                                    }
                                    $('#divImageEdit').css('display', 'inline-block');
                                    if ($imgActive.attr('class') == 'img-polaroid') {
                                        $("#divImageEdit").css("top", _top_polaroid + "px");
                                        $("#divImageEdit").css("left", _left_polaroid + "px");
                                    } else {
                                        $("#divImageEdit").css("top", _top + "px");
                                        $("#divImageEdit").css("left", _left + "px");
                                    }

                                    panSetup();

                                    tmpCanvas.width = newW;
                                    tmpCanvas.height = newY;
                                    var imageObj = $("#my-image")[0];
                                    var context = tmpCanvas.getContext('2d');

                                    var is_firefox = navigator.userAgent.toLowerCase().indexOf('firefox') > -1;
                                    if (is_firefox) sleep(700);//fix bug on Firefox
                              
                                    //fix bug on iOs
                                    if((navigator.userAgent.match(/iPhone/i)) || (navigator.userAgent.match(/iPod/i))) {
                                        try {
                                            var mpImg = new MegaPixImage(imageObj);
                                            mpImg.render(tmpCanvas, { width: imageObj.width, height: imageObj.height });
                                        } catch(e) {
                                            context.drawImage(imageObj, 0, 0, newW, newY)
                                        }
                                    } else {
                                        context.drawImage(imageObj, 0, 0, newW, newY);
                                    }

                                    crop();

                                    if ($imgActive.attr('class') == 'img-circle') {
                                        $('#my-mask').css('-webkit-border-radius', '500px');
                                        $('#my-mask').css('-moz-border-radius', '500px');
                                        $('#my-mask').css('border-radius', '500px');
                                    } else {
                                        $('#my-mask').css('-webkit-border-radius', '0px');
                                        $('#my-mask').css('-moz-border-radius', '0px');
                                        $('#my-mask').css('border-radius', '0px');
                                    }

                                    $('#my-image').unbind('load'); //spy tdk load berulang2

                                    if ($imgActive.prop("tagName").toLowerCase() == 'img') {

                                    } else {
                                        $('#btnZoomIn').click(); $('#btnZoomIn').click(); //fix bug
                                    }

                                });

                                $('#btnChangeImage').unbind('click');
                                $('#btnChangeImage').bind('click', function () {
                                    var canvas = document.getElementById('myCanvas');

                                    $imgActive = $("#divToolImg").data('image'); //img2: Selang antara klik browse & select image, hover diabaikan. $imgActive di-set dgn image yg active wkt klik browse.

                                    //Embed Image
                                    var image;
                                    if (hiquality == false) {
                                        if (extension == 'jpg' || extension == 'jpeg') {
                                            image = canvas.toDataURL("image/jpeg", 0.9);
                                        } else {
                                            image = canvas.toDataURL("image/png", 1);
                                        }
                                    } else {
                                        image = canvas.toDataURL("image/png", 1);
                                    }

                                    if ($imgActive.prop("tagName").toLowerCase() == 'img') {
                                        $imgActive.attr('src', image);
                                        $imgActive.data('filename', imgname); //Set data attribute for filename
                                    } else if ($imgActive.prop("tagName").toLowerCase() == 'figure') {
                                        $imgActive.find('img').attr('src', image);
                                        $imgActive.find('img').data('filename', imgname); //Set data attribute for filename
                                    } else {
                                        $imgActive.css('background-image', 'url(data:' + image + ')');
                                        $imgActive.data('filename', imgname); //Set data attribute for filename
                                    }

                                    $('#divImageEdit').css('display', 'none');
                                    $('.overlay-bg').css('width', '1px');
                                    $('.overlay-bg').css('height', '1px');

                                });
                                $('#btnImageCancel').unbind('click');
                                $('#btnImageCancel').bind('click', function () {
                                    var canvas = document.getElementById('myCanvas');

                                    $imgActive = $("#divToolImg").data('image'); //img2: Selang antara klik browse & select image, hover diabaikan. $imgActive di-set dgn image yg active wkt klik browse.

                                    $('#divImageEdit').css('display', 'none');
                                    $('.overlay-bg').css('width', '1px');
                                    $('.overlay-bg').css('height', '1px');


                                });


                                $('#btnZoomIn').unbind('click');
                                $('#btnZoomIn').bind('click', function () {

                                    var nCurrentWidth = parseInt($("#my-image").css('width'));
                                    var nCurrentHeight = parseInt($("#my-image").css('height'));

                                    //if (nInitialWidth <= (nCurrentWidth / 0.9)) return;
                                    //if (nInitialHeight <= (nCurrentHeight / 0.9)) return;

                                    $("#my-image").css('width', (nCurrentWidth / 0.9) + 'px');
                                    $("#my-image").css('height', (nCurrentHeight / 0.9) + 'px');

                                    panSetup();

                                    tmpCanvas.width = (nCurrentWidth / 0.9);
                                    tmpCanvas.height = (nCurrentHeight / 0.9);

                                    var imageObj = $("#my-image")[0];
                                    var context = tmpCanvas.getContext('2d');
                                    context.drawImage(imageObj, 0, 0, (nCurrentWidth / 0.9), (nCurrentHeight / 0.9));

                                    crop();

                                });

                                $('#btnZoomOut').unbind('click');
                                $('#btnZoomOut').bind('click', function () {

                                    var nCurrentWidth = parseInt($("#my-image").css('width'));
                                    var nCurrentHeight = parseInt($("#my-image").css('height'));

                                    //if ((nCurrentWidth / 1.1) >= parseInt($("#my-mask").css('width')) && (nCurrentHeight / 1.1) >= parseInt($("#my-mask").css('height'))) {
                                    $("#my-image").css('width', (nCurrentWidth / 1.1) + 'px');
                                    $("#my-image").css('height', (nCurrentHeight / 1.1) + 'px');

                                    panSetup();

                                    tmpCanvas.width = (nCurrentWidth / 1.1);
                                    tmpCanvas.height = (nCurrentHeight / 1.1);

                                    var imageObj = $("#my-image")[0];
                                    var context = tmpCanvas.getContext('2d');

                                    context.drawImage(imageObj, 0, 0, (nCurrentWidth / 1.1), (nCurrentHeight / 1.1));

                                    crop();

                                    //}
                                });

                            };
                            oimg.src = image;

                        };
                    })(file);
                    reader.readAsDataURL(file);
                }
            }

        };

        var crop = function () {

            var x = parseInt($("#my-image").css('left'));
            var y = parseInt($("#my-image").css('top'));

            var dw = parseInt($("#my-mask").css('width'));
            var dh = parseInt($("#my-mask").css('height'));

            var canvas = document.getElementById('myCanvas');
            var context = canvas.getContext('2d');
            canvas.width = dw;
            canvas.height = dh;

            var imageObj = $("#my-image")[0];
            var sourceX = -1 * x;
            var sourceY = -1 * y;

            if (sourceY > (tmpCanvas.height - dh)) sourceY = tmpCanvas.height - dh;
            if (sourceX > (tmpCanvas.width - dw)) sourceX = tmpCanvas.width - dw;

            context.drawImage(tmpCanvas, sourceX, sourceY, dw, dh, 0, 0, dw, dh);
        };

        /* source: http://stackoverflow.com/questions/1590840/drag-a-zoomed-image-within-a-div-clipping-mask-using-jquery-draggable */
        var panSetup = function () {

            $("#my-image").css({ top: 0, left: 0 });

            var maskWidth = $("#my-mask").width();
            var maskHeight = $("#my-mask").height();
            var imgPos = $("#my-image").offset();
            var imgWidth = $("#my-image").width();
            var imgHeight = $("#my-image").height();

            var x1 = (imgPos.left + maskWidth) - imgWidth;
            var y1 = (imgPos.top + maskHeight) - imgHeight;
            var x2 = imgPos.left;
            var y2 = imgPos.top;

            $("#my-image").draggable({
                revert: false, containment: [x1, y1, x2, y2], drag: function () {

                    crop();
                }
            });
            $("#my-image").css({ cursor: 'move' });
        };

        this.init();

    };

    $.fn.imageembed = function (options) {
        return this.each(function () {

            if (undefined == $(this).data('imageembed')) {
                var plugin = new $.imageembed(this, options);
                $(this).data('imageembed', plugin);

            }
        });
    };
})(jQuery);


/* Utils */
function makeid() {//http://stackoverflow.com/questions/1349404/generate-a-string-of-5-random-characters-in-javascript
    var text = "";
    var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
    for (var i = 0; i < 5; i++)
        text += possible.charAt(Math.floor(Math.random() * possible.length));
    return text;
}
function sleep(milliseconds) {//http://www.phpied.com/sleep-in-javascript/
    var start = new Date().getTime();
    for (var i = 0; i < 1e7; i++) {
        if ((new Date().getTime() - start) > milliseconds) {
            break;
        }
    }
}


/*******************************************************************************************/


/* 
source:
http://stackoverflow.com/questions/1043957/clearing-input-type-file-using-jquery
https://github.com/malsup/form/blob/master/jquery.form.js
*/
jQuery.fn.clearFields = jQuery.fn.clearInputs = function (includeHidden) {
    var re = /^(?:color|date|datetime|email|month|number|password|range|search|tel|text|time|url|week)$/i; // 'hidden' is not in this list
    return this.each(function () {
        var t = this.type, tag = this.tagName.toLowerCase();
        if (re.test(t) || tag == 'textarea') {
            this.value = '';
        }
        else if (t == 'checkbox' || t == 'radio') {
            this.checked = false;
        }
        else if (tag == 'select') {
            this.selectedIndex = -1;
        }
        else if (t == "file") {
            if (/MSIE/.test(navigator.userAgent)) {
                $(this).replaceWith($(this).clone(true));
            } else {
                $(this).val('');
            }
        }
        else if (includeHidden) {
            // includeHidden can be the value true, or it can be a selector string
            // indicating a special test; for example:
            //  $('#myForm').clearForm('.special:hidden')
            // the above would clean hidden inputs that have the class of 'special'
            if ((includeHidden === true && /hidden/.test(t)) ||
                (typeof includeHidden == 'string' && $(this).is(includeHidden)))
                this.value = '';
        }
    });
};



/* Simple Modal - Inspired by modalEffects.js from http://www.codrops.com , http://tympanus.net/codrops/2013/06/25/nifty-modal-window-effects/ */
(function ($) {

    $.simplemodal = function (element, options) {

        var defaults = {
            onCancel: function () { }
        };

        this.settings = {};

        var $element = $(element),
             element = element;

        var $ovlid;

        this.init = function () {

            this.settings = $.extend({}, defaults, options);

            //var html_overlay = '<div class="md-overlay"></div>';
            //if ($('.md-overlay').length == 0) $('body').append(html_overlay);

            /**** Localize All ****/
            if ($('#divCb').length == 0) {
                $('body').append('<div id="divCb"></div>');
            }

        };

        this.hide = function () {
            $element.css('display', 'none');
            $element.removeClass('md-show');
            $ovlid.remove();//
        };

        this.show = function () {

            var rnd = makeid();
            var html_overlay = '<div id="md-overlay-' + rnd + '" class="md-overlay"></div>';
            $('#divCb').append(html_overlay);
            $ovlid = $('#md-overlay-' + rnd);

            /*setTimeout(function () {
                $element.addClass('md-show');
            }, 1);*/
            $element.addClass('md-show');
            $element.stop(true, true).css('display', 'none').fadeIn(300);

            $('#md-overlay-' + rnd).unbind();
            $('#md-overlay-' + rnd).click(function () {

                $element.stop(true, true).fadeOut(300, function(){
                    $element.removeClass('md-show');
                });
                $ovlid.remove();//

                $element.data('simplemodal').settings.onCancel();
            });

        };

        this.init();
    };

    $.fn.simplemodal = function (options) {

        return this.each(function () {

            if (undefined == $(this).data('simplemodal')) {
                var plugin = new $.simplemodal(this, options);
                $(this).data('simplemodal', plugin);

            }

        });
    };
})(jQuery);



/* source: http://stackoverflow.com/questions/1002934/jquery-x-y-document-coordinates-of-dom-object */
jQuery.fn.getPos = function () {
    var o = this[0];
    var left = 0, top = 0, parentNode = null, offsetParent = null;
    offsetParent = o.offsetParent;
    var original = o;
    var el = o;
    while (el.parentNode != null) {
        el = el.parentNode;
        if (el.offsetParent != null) {
            var considerScroll = true;
            if (window.opera) {
                if (el == original.parentNode || el.nodeName == "TR") {
                    considerScroll = false;
                }
            }
            if (considerScroll) {
                if (el.scrollTop && el.scrollTop > 0) {
                    top -= el.scrollTop;
                }
                if (el.scrollLeft && el.scrollLeft > 0) {
                    left -= el.scrollLeft;
                }
            }
        }
        if (el == offsetParent) {
            left += o.offsetLeft;
            if (el.clientLeft && el.nodeName != "TABLE") {
                left += el.clientLeft;
            }
            top += o.offsetTop;
            if (el.clientTop && el.nodeName != "TABLE") {
                top += el.clientTop;
            }
            o = el;
            if (o.offsetParent == null) {
                if (o.offsetLeft) {
                    left += o.offsetLeft;
                }
                if (o.offsetTop) {
                    top += o.offsetTop;
                }
            }
            offsetParent = o.offsetParent;
        }
    }
    return {
        left: left,
        top: top
    };
};

function detectIE() {
    var ua = window.navigator.userAgent;
    var msie = ua.indexOf('MSIE ');
    var trident = ua.indexOf('Trident/');

    if (msie > 0) {
        // IE 10 or older => return version number
        return parseInt(ua.substring(msie + 5, ua.indexOf('.', msie)), 10);
    }

    if (trident > 0) {
        // IE 11 (or newer) => return version number
        var rv = ua.indexOf('rv:');
        return parseInt(ua.substring(rv + 3, ua.indexOf('.', rv)), 10);
    }

    // other browser
    return false;
}

/*! rangeslider.js - v0.3.1 | (c) 2014 @andreruffert | MIT license | https://github.com/andreruffert/rangeslider.js */
eval(function(p,a,c,k,e,r){e=function(c){return(c<a?'':e(parseInt(c/a)))+((c=c%a)>35?String.fromCharCode(c+29):c.toString(36))};if(!''.replace(/^/,String)){while(c--)r[e(c)]=k[c]||e(c);k=[function(e){return r[e]}];e=function(){return'\\w+'};c=1};while(c--)if(k[c])p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c]);return p}('\'2v 2d\';(4(12){7(u 1i===\'4\'&&1i.2H){1i([\'1J\'],12)}1g 7(u 1W===\'1Z\'){12(2i(\'1J\'))}1g{12(2u)}}(4($){4 1D(){d Y=o.26(\'Y\');Y.2b(\'1v\',\'f\');9 Y.1v!==\'2E\'}d 8=\'17\',L=[],1q=1D(),16={N:1S,1P:\'17\',T:\'17--1E\',1u:\'1X\',14:\'24\',y:[\'28\',\'29\',\'2a\'],z:[\'2c\',\'2e\',\'2f\'],A:[\'2l\',\'2n\',\'2s\']};4 1n(j,1l){d P=1F.a.1B.23(1z,2);9 1t(4(){9 j.18(1L,P)},1l)}4 1w(j,U){U=U||1p;9 4(){7(!j.13){d P=1F.a.1B.18(1z);j.1A=j.18(M,P);j.13=1S}2m(j.1O);j.1O=1t(4(){j.13=1m},U);9 j.1A}}4 b(c,6){3.$M=$(M);3.$o=$(o);3.$c=$(c);3.6=$.2R({},16,6);3.2S=16;3.1V=8;3.y=3.6.y.1e(\'.\'+8+\' \')+\'.\'+8;3.z=3.6.z.1e(\'.\'+8+\' \')+\'.\'+8;3.A=3.6.A.1e(\'.\'+8+\' \')+\'.\'+8;3.N=3.6.N;3.H=3.6.H;3.F=3.6.F;3.K=3.6.K;7(3.N){7(1q){9 1m}}3.Q=\'25-\'+8+\'-\'+(+1T 27());3.k=R(3.$c[0].1j(\'k\')||0);3.p=R(3.$c[0].1j(\'p\')||1p);3.5=R(3.$c[0].5||3.k+(3.p-3.k)/2);3.s=R(3.$c[0].1j(\'s\')||1);3.$19=$(\'<1a 1b="\'+3.6.1u+\'" />\');3.$J=$(\'<1a 1b="\'+3.6.14+\'" />\');3.$f=$(\'<1a 1b="\'+3.6.1P+\'" 2o="\'+3.Q+\'" />\').2p(3.$c).2q(3.$19,3.$J);3.$c.2r({\'S\':\'2t\',\'1G\':\'1H\',\'2w\':\'1H\',\'2x\':\'2y\',\'2D\':\'0\'});3.I=$.1f(3.I,3);3.G=$.1f(3.G,3);3.E=$.1f(3.E,3);3.1k();d O=3;3.$M.D(\'1Y\'+\'.\'+8,1w(4(){1n(4(){O.15()},21)},20));3.$o.D(3.y,\'#\'+3.Q+\':22(.\'+3.6.T+\')\',3.I);3.$c.D(\'1o\'+\'.\'+8,4(e,m){7(m&&m.1r===8){9}d 5=e.1s.5,h=O.V(5);O.C(h)})}b.a.1k=4(){7(3.H&&u 3.H===\'4\'){3.H()}3.15()};b.a.15=4(){3.W=3.$J[0].1x;3.1y=3.$f[0].1x;3.X=3.1y-3.W;3.w=3.W/2;3.S=3.V(3.5);7(3.$c[0].1E){3.$f.2g(3.6.T)}1g{3.$f.2h(3.6.T)}3.C(3.S)};b.a.I=4(e){e.1c();3.$o.D(3.z,3.G);3.$o.D(3.A,3.E);7((\' \'+e.1s.2j+\' \').2k(/[\\n\\t]/g,\' \').1C(3.6.14)>-1){9}d l=3.Z(3.$f[0],e),10=3.11(3.$J[0])-3.11(3.$f[0]);3.C(l-3.w);7(l>=10&&l<10+3.W){3.w=l-10}};b.a.G=4(e){e.1c();d l=3.Z(3.$f[0],e);3.C(l-3.w)};b.a.E=4(e){e.1c();3.$o.B(3.z,3.G);3.$o.B(3.A,3.E);d l=3.Z(3.$f[0],e);7(3.K&&u 3.K===\'4\'){3.K(l-3.w,3.5)}};b.a.1I=4(h,k,p){7(h<k){9 k}7(h>p){9 p}9 h};b.a.C=4(h){d 5,q;5=(3.1K(3.1I(h,0,3.X))/3.s)*3.s;q=3.V(5);3.$19[0].1h.1G=(q+3.w)+\'1M\';3.$J[0].1h.q=q+\'1M\';3.1N(5);3.S=q;3.5=5;7(3.F&&u 3.F===\'4\'){3.F(q,5)}};b.a.11=4(r){d i=0;2z(r!==1L){i+=r.2A;r=r.2B}9 i};b.a.Z=4(r,e){9(e.2C||e.1Q.1R||e.1Q.2F[0].1R||e.2G.x)-3.11(r)};b.a.V=4(5){d v,h;v=(5-3.k)/(3.p-3.k);h=v*3.X;9 h};b.a.1K=4(h){d v,5;v=((h)/(3.X||1));5=3.s*2I.2J((((v)*(3.p-3.k))+3.k)/3.s);9 2K((5).2L(2))};b.a.1N=4(5){7(5!==3.5){3.$c.2M(5).2N(\'1o\',{1r:8})}};b.a.2O=4(){3.$o.B(3.y,\'#\'+3.Q,3.I);3.$c.B(\'.\'+8).2P(\'1h\').2Q(\'1d\'+8);7(3.$f&&3.$f.1U){3.$f[0].2T.2U(3.$f[0])}L.2V(L.1C(3.$c[0]),1);7(!L.1U){3.$M.B(\'.\'+8)}};$.j[8]=4(6){9 3.2W(4(){d $3=$(3),m=$3.m(\'1d\'+8);7(!m){$3.m(\'1d\'+8,(m=1T b(3,6)));L.2X(3)}7(u 6===\'2Y\'){m[6]()}})}}));',62,185,'|||this|function|value|options|if|pluginName|return|prototype|Plugin|element|var||range||pos||fn|min|posX|data||document|max|left|node|step||typeof|percentage|grabX||startEvent|moveEvent|endEvent|off|setPosition|on|handleEnd|onSlide|handleMove|onInit|handleDown|handle|onSlideEnd|pluginInstances|window|polyfill|_this|args|identifier|parseFloat|position|disabledClass|debounceDuration|getPositionFromValue|handleWidth|maxHandleX|input|getRelativePosition|handleX|getPositionFromNode|factory|debouncing|handleClass|update|defaults|rangeslider|apply|fill|div|class|preventDefault|plugin_|join|proxy|else|style|define|getAttribute|init|wait|false|delay|change|100|inputrange|origin|target|setTimeout|fillClass|type|debounce|offsetWidth|rangeWidth|arguments|lastReturnVal|slice|indexOf|supportsRange|disabled|Array|width|1px|cap|jquery|getValueFromPosition|null|px|setValue|debounceTimeout|rangeClass|originalEvent|clientX|true|new|length|_name|exports|rangeslider__fill|resize|object||300|not|call|rangeslider__handle|js|createElement|Date|mousedown|touchstart|pointerdown|setAttribute|mousemove|strict|touchmove|pointermove|addClass|removeClass|require|className|replace|mouseup|clearTimeout|touchend|id|insertAfter|prepend|css|pointerup|absolute|jQuery|use|height|overflow|hidden|while|offsetLeft|offsetParent|pageX|opacity|text|touches|currentPoint|amd|Math|ceil|Number|toFixed|val|trigger|destroy|removeAttr|removeData|extend|_defaults|parentNode|removeChild|splice|each|push|string'.split('|'),0,{}));


/*! jQuery UI Touch Punch 0.2.3 | Copyright 2011–2014, Dave Furfero | Dual licensed under the MIT or GPL Version 2 licenses. */
!function (a) { function f(a, b) { if (!(a.originalEvent.touches.length > 1)) { a.preventDefault(); var c = a.originalEvent.changedTouches[0], d = document.createEvent("MouseEvents"); d.initMouseEvent(b, !0, !0, window, 1, c.screenX, c.screenY, c.clientX, c.clientY, !1, !1, !1, !1, 0, null), a.target.dispatchEvent(d) } } if (a.support.touch = "ontouchend" in document, a.support.touch) { var e, b = a.ui.mouse.prototype, c = b._mouseInit, d = b._mouseDestroy; b._touchStart = function (a) { var b = this; !e && b._mouseCapture(a.originalEvent.changedTouches[0]) && (e = !0, b._touchMoved = !1, f(a, "mouseover"), f(a, "mousemove"), f(a, "mousedown")) }, b._touchMove = function (a) { e && (this._touchMoved = !0, f(a, "mousemove")) }, b._touchEnd = function (a) { e && (f(a, "mouseup"), f(a, "mouseout"), this._touchMoved || f(a, "click"), e = !1) }, b._mouseInit = function () { var b = this; b.element.bind({ touchstart: a.proxy(b, "_touchStart"), touchmove: a.proxy(b, "_touchMove"), touchend: a.proxy(b, "_touchEnd") }), c.call(b) }, b._mouseDestroy = function () { var b = this; b.element.unbind({ touchstart: a.proxy(b, "_touchStart"), touchmove: a.proxy(b, "_touchMove"), touchend: a.proxy(b, "_touchEnd") }), d.call(b) } } } (jQuery);
