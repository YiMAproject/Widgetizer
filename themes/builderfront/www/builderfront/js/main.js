(function($)
{
    // also in style.css ---------------------------------------------------

    // Area Place Holders (widgets doped into)
    var AREA_PLACEHOLDER_SELECTOR  = '.builderfront_area_holder';
    // The Sortable Elements Inside Area (widgets)
    var AREA_SORTABLE_CLASS        = 'ui_sortable_element';
    // Sortable Handler
    var AREA_SORTABLE_HANDLER_CLAS = 'builderfront_sortable_handler';

    // Draggable Widgets
    var DRAGGABLE_SELECTOR         = '.builderfront_element_dragable';

    var DRAGGABLE_HELPER_ELEMENT   = $('<div style="height: 80px; width: 100px; background: #F9FAFA; box-shadow: 5px 5px 1px rgba(0,0,0,0.1); text-align: center; line-height: 100px; font-size: 10px; color: #16A085">Drop Me To Area</div>');

    // =====================================================================

    $(window).load(function() {
        makeDraggable();
        makeSortable();
    });

    function makeDraggable()
    {
        $(DRAGGABLE_SELECTOR).each(function(){
            $(this).draggable({
                scope: "widgetizer", // A draggable with the same scope value as a droppable will be accepted by the droppable
                // addClasses: false,
                appendTo: 'body',
                // cancel: ".title", // Prevents from dragging
                connectToSortable: AREA_PLACEHOLDER_SELECTOR,
                cursor: "move",
                delay: 100,
                helper: function() {
                    return DRAGGABLE_HELPER_ELEMENT;
                },
                iframeFix: true,
                opacity: 0.75,
                refreshPositions: true,
                revert: 'invalid',
                revertDuration: 300,
                snap: true,
                snapMode: "inner",
                snapTolerance: 30,

                // Triggered when an accepted draggable starts dragging
                start: function(event, ui){
                    _highlightAllAreas();
                },
                stop: function(event, ui){
                    _downlightAllAreas();
                }
            });
        });
    }

    function makeSortable()
    {
        var sortables = $(AREA_PLACEHOLDER_SELECTOR);

        sortables.sortable({
            appendTo: AREA_PLACEHOLDER_SELECTOR,
            axis: "y",
            //connectWith: AREA_PLACEHOLDER_SELECTOR, // move widgets to another places
            cursor: "move",
            delay: 150,
            // Restricts sort start click to the specified element
            handle: '.'+AREA_SORTABLE_HANDLER_CLAS,
            opacity: 0.5,
            // A class name that gets applied to the otherwise white space
            placeholder: "sortable-placeholder",
            // Specifies which items inside the element should be sortable
            items: '.' + AREA_SORTABLE_CLASS,
            create: function(event, ui){
                var $placeholder = $(event.target);
                // we don't want empty message as an item
                $placeholder.find('.builderfront_start_empty_area').removeClass(AREA_SORTABLE_CLASS);
            },
            receive: function(event, ui){
                var $placeHolder = event.target;
                var $draggable   = ui.item;
                var $widget      = $draggable.attr('id'); // used as param to load widget

                if (! ui.sender.hasClass('builderfront_element_dragable')){
                    // Received from draggable (not moved from another place)
                }

                // fade out empty message
                $($placeHolder).find('.builderfront_start_empty_area').fadeOut();

                // load widget content into received draggable
                var $rd = $($placeHolder).find('.builderfront_element_dragable');
                $($rd).widgetizerDrop({widget: $widget});
            },
            // This event is triggered when a sortable item is moved into a sortable list
            over: function(event, ui){
                _highlightPlaceholder($(event.target));
                _helperDropMe(ui.helper);
            },
            out: function(event, ui){
                _downlightPlaceholder($(event.target));
                _revertHelperDropMe(ui.helper);
            }
        });
    }

    /**
     * Highlight All Widget Places Areas
     * : show user places that can drop widgets
     *
     * @private
     */
    function _highlightAllAreas() {
        $(AREA_PLACEHOLDER_SELECTOR).addClass("builderfront_area_holder_hover");
    }

    /**
     * Remove Highlighted Effects on Areas
     * @see _highlightAllAreas()
     * @private
     */
    function _downlightAllAreas() {
        $(AREA_PLACEHOLDER_SELECTOR).removeClass("builderfront_area_holder_hover");
    }

    /**
     * Highlight a placeholder
     * : usually call when user drag element over placeholder
     * @param $placeholder
     * @private
     */
    function _highlightPlaceholder($placeholder) {
        $placeholder.addClass("builderfront_area_holder_hover");

        // fade out empty message
        $placeholder.find('.builderfront_start_empty_area').fadeOut();
    }

    /**
     * Remove Highlighted Effects on a placeholder
     * @see _highlightPlaceholder()
     * @private
     */
    function _downlightPlaceholder($placeholder) {
        $placeholder.removeClass("builderfront_area_holder_hover");

        // fade in empty message
        if($placeholder.find('.builderfront_widget_holder').size() == 0 ) {
            // Bring Drop Message, We didn`t have any widget inserted
            $placeholder.find('.builderfront_start_empty_area').fadeIn();
        }
    }

    /**
     * Represent Drop Me Message On Draggable Helper
     * : happen when user drag over placeholder
     * @param $helper
     * @private
     */
    function _helperDropMe($helper)
    {
        if (!$helper.hasClass('builderfront_widget_holder')) {
            // this is draggable helper
            $helper.html('Drop Me Here ...');
        }
    }

    /**
     * Revert Helper Drop Me Message
     *
     * @param $helper
     * @private
     */
    function _revertHelperDropMe($helper)
    {
        //$helper.html('Drag Me to an Area');
    }

    /**
     * Load Widget by making ajax call
     *
     * @param options
     */
    $.fn.widgetizerDrop = function (options)
    {
        var defaults = {
            widget: '',            // prefixed widget name exp. widgetizer-[widgetNameHere]
            method: 'render',      // call method from widget object
            params: {}
        };

        options = $.extend(false, defaults, options);

        options.params.html_content = $('.jumbotron').html();

        // Append Widget Holder >>>>>>>>
        // note: when draggable falling into place is a clone of element
        //       we have to reset and load widget content into it.
        var $widgetHolder = $(this);
        $widgetHolder.attr('class',
            'builderfront_widget_holder '      // this is widget
            +AREA_SORTABLE_CLASS               // also sortable
            +' builderfront_loading_content'); // and still loading
        $widgetHolder.attr('rel-data', options.widget);
        $widgetHolder.html('');
        // <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<


        // widgetizer-[widgetNameHere]
        var $widgetArr = options.widget.split('-');
        options.widget = $widgetArr[1];

        options.callback = function(element, response) {
            // loading was complete
            element.removeClass('builderfront_loading_content');
            // set widget content into element
            $.fn.widgetator.defaultCallback(element, response);

            // append widget extra elements
            appendSortableHandler(element);
            appendSettingButtons(element);
        };

        $($widgetHolder).widgetator(options); // <== LOAD WIDGET

        // __________________________________________________________________________________

        function appendSortableHandler(element) {
            var $handler = $('<div></div>').addClass(AREA_SORTABLE_HANDLER_CLAS);
                var $icon = $('<span class="fa fa-arrows  white"></span>');
            $handler.html($icon);

            element.append($handler);
        }

        // append widget setting buttons elements
        function appendSettingButtons(element) {
            var $settingContainer = $('<div></div>').addClass('builderfront_settings');
            var $delButton = $('<button type="button"><span class="fa fa-trash-o  white"></span></button>');
            $delButton.addClass('builderfront_settings_item builderfront_btn btn-danger');
            var $editButton = $('<button type="button"><span class="fa fa-pencil  white"></span></button>');
            $editButton.addClass('builderfront_settings_item builderfront_btn btn-success');

            $settingContainer
                .append($editButton)
                .append($delButton)
            ;
            element.append($settingContainer);
        }
    }

    // =======================================================================================================================================
    // Setting Buttons Action
    $(document).ready(function() {
        $('.builderfront_settings_item').bind( 'click', function() {
            console.log($(this));
        });
    });


})(jQuery);