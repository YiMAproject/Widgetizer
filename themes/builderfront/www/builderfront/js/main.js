(function($)
{
    /**
     * Areas Place Holder
     * @type {string}
     */
    var AREA_PLACEHOLDER  = '.builderfront_area_holder';

    var DRAGGABLE_ELEMENT = '.builderfront_element_dragable';

    $(window).load(function() {
        makeDropable();
        makeDraggable();
    });

    function makeDropable()
    {
        var sortables = $(AREA_PLACEHOLDER);

        sortables.droppable({
            accept: DRAGGABLE_ELEMENT,
            greedy: false, // any parent droppables will not receive the element. The drop event event.target can be checked to see which droppable received the draggable element.
            scope: "widgetizer",
            // Triggered when the droppable is created
            create: function(event, ui){
                var $placeHolder = event.target;
                $($placeHolder).find('.builderfront_start_empty_area').removeClass('ui-sortable-handle');
            },
            // Triggered when an accepted draggable starts dragging
            activate: function(event, ui){
                $(AREA_PLACEHOLDER).addClass("builderfront_area_holder_hover");
            },
            // Triggered when an accepted draggable stops dragging
            deactivate: function(event, ui) {
                $(AREA_PLACEHOLDER).removeClass("builderfront_area_holder_hover");
            },
            // Triggered when an accepted draggable is dropped on the droppable
            drop: function(event, ui){
                var $placeHolder = event.target;
                var $draggable   = ui.draggable;

                var $widget = $draggable.attr('id');
                $($placeHolder).widgetizerDrop({widget: $widget});
            },
            // Triggered when an accepted draggable is dragged out of the droppable
            out: function(event, ui){
                var $placeHolder = event.target;
                if( $($placeHolder).find('.ui-sortable-handle').size() == 0 ) {
                    // Bring Drop Message, We didn`t have any widget inserted
                    $($placeHolder).find('.builderfront_start_empty_area').fadeIn();
                }
            },
            // Triggered when an accepted draggable is dragged over the droppable
            over: function(event, ui){
                // change dragable
                var $draggable = ui.helper; // draggable helper
                $draggable.html('Drop Me Here');
                // clear empty drop message
                var $placeHolder = event.target;
                $($placeHolder).find('.builderfront_start_empty_area').fadeOut();
            }
        });
    }

    function makeDraggable()
    {
        $(DRAGGABLE_ELEMENT).each(function(){
            $(this).draggable({
                scope: "widgetizer", // A draggable with the same scope value as a droppable will be accepted by the droppable
                // addClasses: false,
                appendTo: 'body',
                // cancel: ".title", // Prevents from dragging
                // connectToSortable: AREA_PLACEHOLDER,
                // cursor: "crosshair",
                delay: 100,
                helper: function() {
                    return $('<div style="height: 80px; width: 100px; background: #F9FAFA; box-shadow: 5px 5px 1px rgba(0,0,0,0.1); text-align: center; line-height: 100px; font-size: 10px; color: #16A085">Drop Me To Area</div>');
                },
                iframeFix: true,
                opacity: 0.55,
                refreshPositions: true,
                revert: 'invalid',
                revertDuration: 300,
                snap: true,
                snapMode: "inner",
                snapTolerance: 30
            });
        });
    }


    $.fn.widgetizerDrop = function (options)
    {
        var defaults = {
            widget: '',            // prefixed widget name exp. widgetizer-[widgetNameHere]
            method: 'render',      // call method from widget object
            params: {}
        };

        options = $.extend(false, defaults, options);

        options.params.html_content = $('.jumbotron').html();

        // widgetizer-[widgetNameHere]
        $widgetArr = options.widget.split('-');
        options.widget = $widgetArr[1];

        $(this).widgetator(options);
    }

})(jQuery);