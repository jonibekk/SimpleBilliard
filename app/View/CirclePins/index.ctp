<?= $this->App->viewStartComment()?>
<style>
/*#pinnned { list-style-type: none; margin: 0; padding: 0; width: 100%; }
#pinnned li { margin: 0 3px 3px 3px; padding: 0.4em; padding-left: 1.5em; font-size: 1.4em; height: 18px; }
#pinnned li div { position: absolute; margin-left: -1.3em; }*/
</style>

<div class="panel panel-default col-sm-8 col-sm-offset-2 clearfix pin-circle-list">
    <div class="panel-heading">
        <?= __("PinCircle") ?>
    </div>
    <div class="panel-body pin-circle-view-panel-body">
        <div class="row">
            <div class="column">
                <div class="panel panel-primary">
                    <div class="panel-body">
                        <div class="list-group-item ignore-elements">
                                <?=
                                $this->Html->image('pre-load.svg',
                                    [
                                        'class'         => 'pin-circle-avatar lazy media-object',
                                        'data-original' => $defaultCircle[0]['Circle']['image'],
                                        'width'         => '32',
                                        'height'        => '32',
                                        'error-img'     => "/img/no-image-link.png",
                                    ]
                                )
                                ?>
                                <div class="pin-circle-text"><label><?php echo $defaultCircle[0]['Circle']['name'];?></label></div>
                                <?=
                                $this->Html->image('pre-load.svg',
                                    [
                                        'class'         => 'pull-right lazy media-object',
                                        'data-original' => "/img/no-image-link.png",
                                        'width'         => '32',
                                        'height'        => '32',
                                        'error-img'     => "/img/no-image-link.png",
                                    ]
                                )
                                ?>
                            </div>
                        <ul id="pinned" class="list-group">
                            <?php foreach ($pinnedCircles as $circle): ?>
                                <?php if (isset($circle['Circle']['id'])): ?>
                                    <li id="<?= $circle['Circle']['id']?>"" class="list-group-item">
                                        <?=
                                        $this->Html->image('pre-load.svg',
                                            [
                                                'class'         => 'pin-circle-avatar lazy media-object',
                                                'data-original' => $circle['Circle']['image'],
                                                'width'         => '32',
                                                'height'        => '32',
                                                'error-img'     => "/img/no-image-link.png",
                                            ]
                                        )
                                        ?>
                                        <div class="pin-circle-text"><label><?php echo $circle['Circle']['name'];?></label></div>
                                        <?=
                                        $this->Html->image('pre-load.svg',
                                            [
                                                'class'         => 'pull-right lazy media-object',
                                                'data-original' => "/img/no-image-link.png",
                                                'width'         => '32',
                                                'height'        => '32',
                                                'error-img'     => "/img/no-image-link.png",
                                            ]
                                        )
                                        ?>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                            <!-- <li class="list-group-item">Element 1</li>
                            <li class="list-group-item">Element 2</li> -->
                        </ul>
                    </div>
                </div>
                <div class="panel panel-primary">
                    <div class="panel-body">
                        <ul id="unpinned" class="list-group">
                            <?php foreach ($unpinnedCircles as $circle): ?>
                                <li id="<?= $circle['Circle']['id']?>"" class="list-group-item">
                                    <?=
                                    $this->Html->image('pre-load.svg',
                                        [
                                            'class'         => 'pin-circle-avatar lazy media-object',
                                            'data-original' => $circle['Circle']['image'],
                                            'width'         => '32',
                                            'height'        => '32',
                                            'error-img'     => "/img/no-image-link.png",
                                        ]
                                    )
                                    ?>
                                    <div class="pin-circle-text"><label><?php echo $circle['Circle']['name'];?></label></div>
                                    <?=
                                    $this->Html->image('pre-load.svg',
                                        [
                                            'class'         => 'pull-right lazy media-object',
                                            'data-original' => "/img/no-image-link.png",
                                            'width'         => '32',
                                            'height'        => '32',
                                            'error-img'     => "/img/no-image-link.png",
                                        ]
                                    )
                                    ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Sortable.js -->
<script type="text/javascript" src="/js/Sortable.min.js"></script>
<script type="text/javascript">
$(function () {
'use strict';
    $(document).ready(function () {
        // document.getElementById('unpinned').sort((a, b) => {
        //     if(a.find('label').innerHTML < b.find('label').innerHTML) return -1;
        //     if(a.find('label').innerHTML > b.find('label').innerHTML) return 1;
        //     return 0;
        // });
    var pinnedsortable = Sortable.create(document.getElementById("pinned"), {
        group: "circles",  // or { name: "...", pull: [true, false, clone], put: [true, false, array] }
        sort: true,  // sorting inside list
        delay: 0, // time in milliseconds to define when the sorting should start
        touchStartThreshold: 0, // px, how many pixels the point should move before cancelling a delayed drag event
        disabled: false, // Disables the sortable if set to true.
        store: null,  // @see Store
        animation: 100,  // ms, animation speed moving items when sorting, `0` — without animation
        handle: ".list-group-item",  // Drag handle selector within list items
        filter: ".ignore-elements",  // Selectors that do not lead to dragging (String or Function)
        preventOnFilter: true, // Call `event.preventDefault()` when triggered `filter`
        //draggable: ".list-group-item",  // Specifies which items inside the element should be draggable
        ghostClass: "sortable-ghost",  // Class name for the drop placeholder
        chosenClass: "sortable-chosen",  // Class name for the chosen item
        dragClass: "sortable-drag",  // Class name for the dragging item
        dataIdAttr: 'data-id',

        forceFallback: false,  // ignore the HTML5 DnD behaviour and force the fallback to kick in

        fallbackClass: "sortable-fallback",  // Class name for the cloned DOM Element when using forceFallback
        fallbackOnBody: false,  // Appends the cloned DOM Element into the Document's Body
        fallbackTolerance: 0, // Specify in pixels how far the mouse should move before it's considered as a drag.

        scroll: true, // or HTMLElement
        //scrollFn: function(offsetX, offsetY, originalEvent, touchEvt, hoverTargetEl) { ... }, // if you have custom scrollbar scrollFn may be used for autoscrolling
        scrollSensitivity: 30, // px, how near the mouse must be to an edge to start scrolling.
        scrollSpeed: 10, // px

        setData: function (/** DataTransfer */dataTransfer, /** HTMLElement*/dragEl) {
            dataTransfer.setData('Text', dragEl.textContent); // `dataTransfer` object of HTML5 DragEvent
        },

        // Element is chosen
        onChoose: function (/**Event*/evt) {
            evt.oldIndex;  // element index within parent
        },

        // Element dragging started
        onStart: function (/**Event*/evt) {
            evt.oldIndex;  // element index within parent
        },

        // Element dragging ended
        onEnd: function (/**Event*/evt) {
            var itemEl = evt.item;  // dragged HTMLElement
            evt.to;    // target list
            evt.from;  // previous list
            evt.oldIndex;  // element's old index within old parent
            evt.newIndex;  // element's new index within new parent
            //alert(evt.newIndex + itemEl.getAttribute("id"));
        },

        // Element is dropped into the list from another list
        onAdd: function (/**Event*/evt) {
            // same properties as onEnd
        },

        // Changed sorting within list
        onUpdate: function (/**Event*/evt) {
            // same properties as onEnd
            //alert(evt.item.getAttribute("id"));
        },

        // Called by any change to the list (add / update / remove)
        onSort: function (/**Event*/evt) {
            // same properties as onEnd
            //alert(evt.item.getAttribute("id"));
        },

        // Element is removed from the list into another list
        onRemove: function (/**Event*/evt) {
            // same properties as onEnd
            //alert("Removed" + evt.item.getAttribute("id"));
        },

        // Attempt to drag a filtered element
        onFilter: function (/**Event*/evt) {
            var itemEl = evt.item;  // HTMLElement receiving the `mousedown|tapstart` event.
        },

        // Event when you move an item in the list or between lists
        onMove: function (/**Event*/evt, /**Event*/originalEvent) {
            // Example: http://jsbin.com/tuyafe/1/edit?js,output
            evt.dragged; // dragged HTMLElement
            evt.draggedRect; // TextRectangle {left, top, right и bottom}
            evt.related; // HTMLElement on which have guided
            evt.relatedRect; // TextRectangle
            originalEvent.clientY; // mouse position
            // return false; — for cancel
        },

        // Called when creating a clone of element
        onClone: function (/**Event*/evt) {
            //var origEl = evt.item;
            //var cloneEl = evt.clone;
        }
    });
    var unpinnedsortable = Sortable.create(document.getElementById("unpinned"), {
        group: "circles",  // or { name: "...", pull: [true, false, clone], put: [true, false, array] }
        sort: false,  // sorting inside list
        delay: 0, // time in milliseconds to define when the sorting should start
        touchStartThreshold: 0, // px, how many pixels the point should move before cancelling a delayed drag event
        disabled: false, // Disables the sortable if set to true.
        store: null,  // @see Store
        animation: 100,  // ms, animation speed moving items when sorting, `0` — without animation
        handle: ".list-group-item",  // Drag handle selector within list items
        filter: ".ignore-elements",  // Selectors that do not lead to dragging (String or Function)
        preventOnFilter: true, // Call `event.preventDefault()` when triggered `filter`
        //draggable: ".list-group-item",  // Specifies which items inside the element should be draggable
        ghostClass: "sortable-ghost",  // Class name for the drop placeholder
        chosenClass: "sortable-chosen",  // Class name for the chosen item
        dragClass: "sortable-drag",  // Class name for the dragging item
        dataIdAttr: 'data-id',

        forceFallback: false,  // ignore the HTML5 DnD behaviour and force the fallback to kick in

        fallbackClass: "sortable-fallback",  // Class name for the cloned DOM Element when using forceFallback
        fallbackOnBody: false,  // Appends the cloned DOM Element into the Document's Body
        fallbackTolerance: 0, // Specify in pixels how far the mouse should move before it's considered as a drag.

        scroll: true, // or HTMLElement
        //scrollFn: function(offsetX, offsetY, originalEvent, touchEvt, hoverTargetEl) { ... }, // if you have custom scrollbar scrollFn may be used for autoscrolling
        scrollSensitivity: 30, // px, how near the mouse must be to an edge to start scrolling.
        scrollSpeed: 10, // px

        setData: function (/** DataTransfer */dataTransfer, /** HTMLElement*/dragEl) {
            dataTransfer.setData('Text', dragEl.textContent); // `dataTransfer` object of HTML5 DragEvent
        },

        // Element is chosen
        onChoose: function (/**Event*/evt) {
            evt.oldIndex;  // element index within parent
        },

        // Element dragging started
        onStart: function (/**Event*/evt) {
            evt.oldIndex;  // element index within parent
        },

        // Element dragging ended
        onEnd: function (/**Event*/evt) {
            var itemEl = evt.item;  // dragged HTMLElement
            evt.to;    // target list
            evt.from;  // previous list
            evt.oldIndex;  // element's old index within old parent
            evt.newIndex;  // element's new index within new parent
            //alert(evt.newIndex + itemEl.getAttribute("id"));
        },

        // Element is dropped into the list from another list
        onAdd: function (/**Event*/evt) {
            // same properties as onEnd
            //document.getElementById("unpinned").sort();
        },

        // Changed sorting within list
        onUpdate: function (/**Event*/evt) {
            // same properties as onEnd
        },

        // Called by any change to the list (add / update / remove)
        onSort: function (/**Event*/evt) {
            // same properties as onEnd
        },

        // Element is removed from the list into another list
        onRemove: function (/**Event*/evt) {
            // same properties as onEnd
            //alert("Removed" + evt.item.getAttribute("id"));
        },

        // Attempt to drag a filtered element
        onFilter: function (/**Event*/evt) {
            var itemEl = evt.item;  // HTMLElement receiving the `mousedown|tapstart` event.
        },

        // Event when you move an item in the list or between lists
        onMove: function (/**Event*/evt, /**Event*/originalEvent) {
            // Example: http://jsbin.com/tuyafe/1/edit?js,output
            evt.dragged; // dragged HTMLElement
            evt.draggedRect; // TextRectangle {left, top, right и bottom}
            evt.related; // HTMLElement on which have guided
            evt.relatedRect; // TextRectangle
            originalEvent.clientY; // mouse position
            // return false; — for cancel
        },

        // Called when creating a clone of element
        onClone: function (/**Event*/evt) {
            //var origEl = evt.item;
            //var cloneEl = evt.clone;
        }
    });
    });
});
</script>
<?= $this->App->viewEndComment()?>