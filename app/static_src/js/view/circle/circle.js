window.onload = function(){
    'use strict';
    //Toggling
   //  if(document.getElementById('pinned-header-icon') && document.getElementById('unpinned-header-icon')) {
   //  	var toggleCaret = function () {
			// this.classList.toggle('fa-caret-down');
			// this.classList.toggle('fa-caret-up');
	  //   }
   //      document.getElementById('pinned-header-icon').onclick = toggleCaret;
   //      document.getElementById('unpinned-header-icon').onclick = toggleCaret;
   //  }
    //Reorder
    if(document.getElementById('pinned') && document.getElementById('unpinned')){
        var pinnedSortable = new Sortable(document.getElementById('pinned'), {
            // group: "circles-list",  // or { name: "...", pull: [true, false, clone], put: [true, false, array] }
            sort: true,  // sorting inside list
            delay: 0, // time in milliseconds to define when the sorting should start
            touchStartThreshold: 0, // px, how many pixels the point should move before cancelling a delayed drag event
            disabled: false, // Disables the sortable if set to true.
            store: null,  // @see Store
            animation: 0,  // ms, animation speed moving items when sorting, `0` — without animation
            handle: ".fa-align-justify",  // Drag handle selector within list items
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
                // evt.item.querySelector('i').classList.remove('fa-disabled');
                // // updateElements = [];
                // // setElementInformations(evt.newIndex);
                // updateOrder();
            },

            // Changed sorting within list
            onUpdate: function (/**Event*/evt) {
                // same properties as onEnd
                // updateElements = [];
                // if(evt.oldIndex === evt.newIndex){
                //     return false;
                // } else {
                //     setElementInformations(evt.newIndex);
                //     setElementInformations(evt.oldIndex);  
                //     console.log("old:"+evt.oldIndex+ " new:" + evt.newIndex);
                //     updateOrder();
                // } 
                // updateOrder();
            },

            // Called by any change to the list (add / update / remove)
            onSort: function (/**Event*/evt) {
                updateOrder();
                updateDisplayCount();
            },

            // Element is removed from the list into another list
            onRemove: function (/**Event*/evt) {
                // same properties as onEnd
                // updateElements = [];
                // evt.item.dataset.beforeid = '';
                // setElementInformations(evt.oldIndex);
                // updateOrder();
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
        var unpinnedSortable = new Sortable(document.getElementById('unpinned'), {
            group: "circles-list",  // or { name: "...", pull: [true, false, clone], put: [true, false, array] }
            sort: false,  // sorting inside list
            delay: 0, // time in milliseconds to define when the sorting should start
            touchStartThreshold: 0, // px, how many pixels the point should move before cancelling a delayed drag event
            disabled: true, // Disables the sortable if set to true.
            store: null,  // @see Store
            animation: 0,  // ms, animation speed moving items when sorting, `0` — without animation
            //handle: ".list-group-item",  // Drag handle selector within list items
            filter: ".ignore-elements",  // Selectors that do not lead to dragging (String or Function)
            preventOnFilter: true, // Call `event.preventDefault()` when triggered `filter`
            //draggable: "li",  // Specifies which items inside the element should be draggable
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
                // if(!evt.item.previousElement){
                //     previousElemntBeforeId = null;
                // }else {
                //     previousElemntBeforeId = evt.item.previousElement.id;
                //     console.log(previousElemntBeforeId);
                // }
            },

            // Element dragging ended
            onEnd: function (/**Event*/evt) {
                // var itemEl = evt.item;  // dragged HTMLElement
                // evt.to;    // target list
                // evt.from;  // previous list
                // evt.oldIndex;  // element's old index within old parent
                // evt.newIndex;  // element's new index within new parent
                //TODO:
                // if(!evt.item.previousElement){
                //     evt.item.setAttributeByName('data-id', 0);
                // }
            },

            // Element is dropped into the list from another list
            onAdd: function (/**Event*/evt) {
                // same properties as onEnd
                // evt.item.querySelector('i').classList.add('fa-disabled');
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
        var updateOrder = function(){
            var senddata = {
                'data[_Token][key]': cake.data.csrf_token.key,
                'csv': makeParams(),
            };

            // var xhr = new XMLHttpRequest();
            // xhr.open('POST', '/api/v1/circle_pins/');
            // xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
            // xhr.onload = function () {
            //     if (xhr.status !== 200) {
            //         var response = JSON.parse(xhr.response);
            //         console.log(xhr);
            //         // Display error message
            //         new Noty({
            //             type: 'error',
            //             text: '<h4>' + cake.word.error + '</h4>' + response.message,
            //         }).show();
            //     }
            // };
            // console.log(senddata);
            // xhr.send(senddata);
            // console.log(senddata);
            $.ajax({
              url: '/api/v1/circle_pins/',
              type:"POST",
              data: senddata,
              contentType:"application/x-www-form-urlencoded; charset=utf-8",
              // dataType:"json",
              success: function(data){
              },
              error: function(data){
                new Noty({
                    type: 'error',
                    text: '<h4>' + cake.word.error + '</h4>' + 'Network/Data error',
                }).show();
              }
            });
        }
        var updateDisplayCount = function() {
            var pincount = document.getElementById('pinned').querySelectorAll('li').length + 1;
            var unpincount = document.getElementById('unpinned').querySelectorAll('li').length;
            document.getElementById('pinnedCount').innerHTML = '(' + pincount + ')';
            document.getElementById('unpinnedCount').innerHTML = '(' + unpincount + ')';
        }
        updateDisplayCount();
    }
    var circles = document.querySelectorAll('.list-group-item');
    // console.log(circles);
    if(circles){
        var pinEvent = function(evt) {
            evt = evt || window.event;
            // var thumbElement = this.querySelector('.fa-thumbtack');
            this.previousElementSibling.previousElementSibling.previousElementSibling.previousElementSibling.classList.toggle('style-hidden');
            this.classList.toggle('fa-disabled');
          if(this.classList.contains('fa-disabled')) {
            document.getElementById('unpinned').appendChild(this.parentElement);
          } else {
            document.getElementById('pinned').appendChild(this.parentElement);
          }
          updateOrder();
          updateDisplayCount();
        };
        for(var i = 0; i < circles.length; i++){
        	// console.log(circles[i]);
            circles[i].querySelector('.fa-thumbtack').onclick = pinEvent;
            // console.log(circles[i].querySelector('.fa-thumbtack'));
        }
    }

    var forEach = function (array, callback, scope) {
      for (var i = 0; i < array.length; i++) {
        callback.call(scope, i, array[i]); // passes back stuff we need
      }
    };

    var makeParams = function() {
        var data = [];
        var pins = document.getElementById('pinned').getElementsByClassName('list-group-item');
        for (var pin of pins) {
            data.push(pin.id);
        }
        return data.join(',');
    }

    

    // var updateOrder = function(element) {
    //     var previousElement = element.previousSibling;
    //     var elementId = element.id;
    //     var beforeId = null;
    //     if(previousElement){
    //         var beforeId = previous.id;
    //     }
    //     // var data = {
    //     //     'data[_Token][key]': cake.data.csrf_token.key,
    //     //     'json' : JSON.stringify({circle_id:elementId,before_id:beforeId}),
    //     // };
    //     var formData = {};
    //     formData['data[_Token][key]'] = cake.data.csrf_token.key;
    //     formData['json'] = JSON.stringify({circle_id:elementId,before_id:beforeId});
    //     console.log(formData);
    //     $.ajax({
    //       url: '/api/v1/circlepin/',
    //       type:"POST",
    //       data: formData,
    //       contentType:"application/x-www-form-urlencoded; charset=utf-8",
    //       // dataType:"json",
    //       success: function(data){
    //         //console.log(data);
    //       },
    //       error: function(data){
    //         //console.log(data);
    //       }
    //     });
    // }

    //Filter
    var filterList = document.getElementById('filter-circles-list');
    if(filterList != null){
        var filterCirlcleList = function(filter) {
          var circleNames = document.getElementsByClassName('circle-name');
          for (i = 0; i < circleNames.length; i++) {
            if (circleNames[i].innerHTML.toLowerCase().indexOf(filter.toLowerCase()) > -1) {
                circleNames[i].parentElement.style.display = "block";
            } else {
                circleNames[i].parentElement.style.display = "none";
            }
          }
        }
        filterList.onkeyup = function(evt) {
          evt = evt || window.event;
          filterCirlcleList(this.value);
        };
    }

    var filterListSide = document.getElementById('filter-circles-list-side');
    if(filterListSide != null){
        var filterCirlcleListSide = function(filter) {
          var circleNames = document.getElementsByClassName('dashboard-circle-name-box');
          for (i = 0; i < circleNames.length; i++) {
            if (circleNames[i].innerHTML.toLowerCase().indexOf(filter.toLowerCase()) > -1) {
                circleNames[i].parentElement.parentElement.style.display = "block";
            } else {
                circleNames[i].parentElement.parentElement.style.display = "none";
            }
          }
        }
        filterListSide.onkeyup = function(evt) {
          evt = evt || window.event;
          filterCirlcleListSide(this.value);
        };
    }

    var filterListHamburger = document.getElementById('filter-circles-list-hamburger');
    if(filterListHamburger != null){
        var filterCirlcleListHamburger = function(filter) {
          var circleNames = document.getElementsByClassName('ashboard-circle-list-row-wrap');
          for (i = 0; i < circleNames.length; i++) {
            if (circleNames[i].innerHTML.toLowerCase().indexOf(filter.toLowerCase()) > -1) {
                circleNames[i].parentElement.parentElement.style.display = "block";
            } else {
                circleNames[i].parentElement.parentElement.style.display = "none";
            }
          }
        }
        filterListHamburger.onkeyup = function(evt) {
          evt = evt || window.event;
          filterCirlcleListHamburger(this.value);
        };
    }
};