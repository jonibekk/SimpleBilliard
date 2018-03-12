(function(){
    'use strict';
    if(document.getElementById('pinned') != null){
        var pinnedSortable = new Sortable(document.getElementById('pinned'), {
            group: "circles-list",  // or { name: "...", pull: [true, false, clone], put: [true, false, array] }
            sort: true,  // sorting inside list
            delay: 0, // time in milliseconds to define when the sorting should start
            touchStartThreshold: 0, // px, how many pixels the point should move before cancelling a delayed drag event
            disabled: false, // Disables the sortable if set to true.
            store: null,  // @see Store
            animation: 0,  // ms, animation speed moving items when sorting, `0` — without animation
            handle: ".list-group-item",  // Drag handle selector within list items
            filter: ".ignore-elements",  // Selectors that do not lead to dragging (String or Function)
            preventOnFilter: true, // Call `event.preventDefault()` when triggered `filter`
            draggable: "li",  // Specifies which items inside the element should be draggable
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
                // dataTransfer.setData('Text', dragEl.textContent); // `dataTransfer` object of HTML5 DragEvent
            },

            // Element is chosen
            onChoose: function (/**Event*/evt) {
                // evt.oldIndex;  // element index within parent
            },

            // Element dragging started
            onStart: function (/**Event*/evt) {
                // evt.oldIndex;  // element index within parent
            },

            // Element dragging ended
            onEnd: function (/**Event*/evt) {
                // var itemEl = evt.item;  // dragged HTMLElement
                // evt.to;    // target list
                // evt.from;  // previous list
                // evt.oldIndex;  // element's old index within old parent
                // evt.newIndex;  // element's new index within new parent
                updateOrder();
            },

            // Element is dropped into the list from another list
            onAdd: function (/**Event*/evt) {
                // same properties as onEnd
                evt.item.querySelector('img').classList.add('pin');
                evt.item.querySelector('img').classList.remove('unpin');
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
            },

            // Attempt to drag a filtered element
            onFilter: function (/**Event*/evt) {
                // var itemEl = evt.item;  // HTMLElement receiving the `mousedown|tapstart` event.
            },

            // Event when you move an item in the list or between lists
            onMove: function (/**Event*/evt, /**Event*/originalEvent) {
                // Example: http://jsbin.com/tuyafe/1/edit?js,output
                // evt.dragged; // dragged HTMLElement
                // evt.draggedRect; // TextRectangle {left, top, right и bottom}
                // evt.related; // HTMLElement on which have guided
                // evt.relatedRect; // TextRectangle
                // originalEvent.clientY; // mouse position
                // return false; — for cancel
            },

            // Called when creating a clone of element
            onClone: function (/**Event*/evt) {
                // var origEl = evt.item;
                // var cloneEl = evt.clone;
            }
        });
    }
    if(document.getElementById('unpinned') != null){
        var unpinnedSortable = new Sortable(document.getElementById('unpinned'), {
            group: "circles-list",  // or { name: "...", pull: [true, false, clone], put: [true, false, array] }
            sort: false,  // sorting inside list
            delay: 0, // time in milliseconds to define when the sorting should start
            touchStartThreshold: 0, // px, how many pixels the point should move before cancelling a delayed drag event
            disabled: false, // Disables the sortable if set to true.
            store: null,  // @see Store
            animation: 0,  // ms, animation speed moving items when sorting, `0` — without animation
            handle: ".list-group-item",  // Drag handle selector within list items
            filter: ".ignore-elements",  // Selectors that do not lead to dragging (String or Function)
            preventOnFilter: true, // Call `event.preventDefault()` when triggered `filter`
            draggable: "li",  // Specifies which items inside the element should be draggable
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
                // dataTransfer.setData('Text', dragEl.textContent); // `dataTransfer` object of HTML5 DragEvent
            },

            // Element is chosen
            onChoose: function (/**Event*/evt) {
                // evt.oldIndex;  // element index within parent
            },

            // Element dragging started
            onStart: function (/**Event*/evt) {
                // evt.oldIndex;  // element index within parent
            },

            // Element dragging ended
            onEnd: function (/**Event*/evt) {
                // var itemEl = evt.item;  // dragged HTMLElement
                // evt.to;    // target list
                // evt.from;  // previous list
                // evt.oldIndex;  // element's old index within old parent
                // evt.newIndex;  // element's new index within new parent
            },

            // Element is dropped into the list from another list
            onAdd: function (/**Event*/evt) {
                // same properties as onEnd
                evt.item.querySelector('img').classList.add('unpin');
                evt.item.querySelector('img').classList.remove('pin');
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
            },

            // Attempt to drag a filtered element
            onFilter: function (/**Event*/evt) {
                // var itemEl = evt.item;  // HTMLElement receiving the `mousedown|tapstart` event.
            },

            // Event when you move an item in the list or between lists
            onMove: function (/**Event*/evt, /**Event*/originalEvent) {
                // Example: http://jsbin.com/tuyafe/1/edit?js,output
                // evt.dragged; // dragged HTMLElement
                // evt.draggedRect; // TextRectangle {left, top, right и bottom}
                // evt.related; // HTMLElement on which have guided
                // evt.relatedRect; // TextRectangle
                // originalEvent.clientY; // mouse position
                // return false; — for cancel
            },

            // Called when creating a clone of element
            onClone: function (/**Event*/evt) {
                // var origEl = evt.item;
                // var cloneEl = evt.clone;
            }
        });
    }
    var circles =document.querySelectorAll('.pin,.unpin');
    if(circles != null){
        var pinEvent = function(evt) {
            evt = evt || window.event;
          if(this.classList.contains('pin')) {
            this.classList.add('unpin');
            this.classList.remove('pin');
            var clone = this.parentElement.cloneNode(true);
            clone.querySelector('img').onclick = pinEvent;
            document.getElementById('unpinned').appendChild(clone);
            this.parentElement.remove();
          } else {
            this.classList.add('pin');
            this.classList.remove('unpin');
            var clone = this.parentElement.cloneNode(true);
            clone.querySelector('img').onclick = pinEvent;
            document.getElementById('pinned').appendChild(clone);
            this.parentElement.remove();
          }
        };
        for(var i=0; i < circles.length; i++){
            circles[i].onclick = pinEvent
        }
    }

    var forEach = function (array, callback, scope) {
      for (var i = 0; i < array.length; i++) {
        callback.call(scope, i, array[i]); // passes back stuff we need
      }
    };

    var makeParams = function() {
        var data = [];
        var index = 0;
        var pins = document.getElementById('pinned').getElementsByClassName('list-group-item');
        for (var pin of pins) {
            var id = pin.id;
            data.push({circle_id:id,pin_order:index});
            index++;
        }
        return data;
    }

    var updateOrder = function() {
        var data = {
            'data[_Token][key]': cake.data.csrf_token.key,
            'json' : JSON.stringify(makeParams()),
        };
        var formData = {};
        formData['data[_Token][key]'] = cake.data.csrf_token.key;
        formData['json'] = JSON.stringify(makeParams());
        console.log(formData);
        $.ajax({
          url: '/api/v1/circlepin/',
          type:"POST",
          data: formData,//JSON.stringify(data),
          contentType:"application/x-www-form-urlencoded; charset=utf-8",
          // dataType:"json",
          success: function(data){
            //console.log(data);
          },
          error: function(data){
            //console.log(data);
          }
        });
    }
})();