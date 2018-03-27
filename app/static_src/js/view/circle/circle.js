window.onload = function(){
    'use strict';

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
                // var itemEl = evt.target;  // dragged HTMLElement
                // evt.to;    // target list
                // evt.from;  // previous list
                // evt.oldIndex;  // element's old index within old parent
                // evt.newIndex;  // element's new index within new parent
            },

            // Element is dropped into the list from another list
            onAdd: function (/**Event*/evt) {
                // same properties as onEnd
                // evt.target.querySelector('i').classList.remove('fa-disabled');
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
                // evt.target.dataset.beforeid = '';
                // setElementInformations(evt.oldIndex);
                // updateOrder();
            },

            // Attempt to drag a filtered element
            onFilter: function (/**Event*/evt) {
                // var itemEl = evt.target;  // HTMLElement receiving the `mousedown|tapstart` event.
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
                // var origEl = evt.target;
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
                // if(!evt.target.previousElement){
                //     previousElemntBeforeId = null;
                // }else {
                //     previousElemntBeforeId = evt.target.previousElement.id;
                //     console.log(previousElemntBeforeId);
                // }
            },

            // Element dragging ended
            onEnd: function (/**Event*/evt) {
                // var itemEl = evt.target;  // dragged HTMLElement
                // evt.to;    // target list
                // evt.from;  // previous list
                // evt.oldIndex;  // element's old index within old parent
                // evt.newIndex;  // element's new index within new parent
                //TODO:
                // if(!evt.target.previousElement){
                //     evt.target.setAttributeByName('data-id', 0);
                // }
            },

            // Element is dropped into the list from another list
            onAdd: function (/**Event*/evt) {
                // same properties as onEnd
                // evt.target.querySelector('i').classList.add('fa-disabled');
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
                // var itemEl = evt.target;  // HTMLElement receiving the `mousedown|tapstart` event.
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
                // var origEl = evt.target;
                // var cloneEl = evt.clone;
            }
        });
        var updateOrder = function(){
            var senddata = {
                'data[_Token][key]': cake.data.csrf_token.key,
                'pin_order': makeParams(),
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
    var circles = document.querySelectorAll('.pin-circle-list-item');
    if(circles){
        var makeParams = function() {
            var data = [];
            var pins = document.getElementById('pinned').getElementsByClassName('pin-circle-list-item');
            for (var pin of pins) {
                data.push(pin.id);
            }
            return data.join(',');
        }
        var editMenu = function(evt) {
            evt = evt || window.event;
            evt.preventDefault();
            var self = this.parentElement;
            if (self.classList.contains('double_click')) {
                return false;
            }
            self.classList.add('double_click');

            var modal_elm = $('<div class="modal on fade" tabindex="-1"></div>');
            modal_elm.on('hidden.bs.modal', function (e) {
                e.target.remove();
                document.getElementById('circles-edit-page').classList.remove('modal-open');
            });
            var url = self.getAttribute('data-url');
            console.log(url);
            if(!url){
                return false;
            }
            if (!url.indexOf('#')) {
                url.modal('open');
            } else {
                $.get(url, function (data) {
                    modal_elm.append(data);
                    //noinspection JSUnresolvedFunction
                    bindSelect2Members(modal_elm);
                    //アップロード画像選択時にトリムして表示
                    modal_elm.find('.fileinput_small').fileinput().on('change.bs.fileinput', function () {
                        self.children('.nailthumb-container').nailthumb({
                            width: 96,
                            height: 96,
                            fitDirection: 'center center'
                        });
                    });
                    modal_elm.modal();
                }).done(function (data) {
                    self.classList.remove('double_click');
                    document.getElementById('circles-edit-page').classList.add('modal-open');
                });
            }
        }
        // var toggleMenu = function(evt) {
        //     evt = evt || window.event;
        //     var target = this.parentElement.querySelector('.pin-circle-dropdown-content');
        //     target.style.display = target.style.display === "block" ? "none" : "block";
        // }
        // var hideMenuAll = function(evt) {
        //     evt = evt || window.event;
        //     var nodes = document.querySelectorAll('.pin-circle-dropdown-content');
        //     for(var i=0;i<nodes.length;i++){
        //         nodes[i].style.display = "none";
        //     }
        // }
        var pinEvent = function(evt) {
            evt = evt || window.event;
            this.parentElement.querySelector('.fa-align-justify').classList.toggle('style-hidden');
            this.classList.toggle('fa-disabled');

            if(this.classList.contains('fa-disabled')) {
                document.getElementById('unpinned').appendChild(this.parentElement);
            } else {
                document.getElementById('pinned').appendChild(this.parentElement);
            }
            // hideMenuAll();
            updateOrder();
            updateDisplayCount();
        };
        // var moveToBottom = function(evt) {
        //     evt = evt || window.event;
        //     this.parentElement.parentElement.parentElement.querySelector('.fa-thumbtack').classList.remove('fa-disabled');
        //     this.parentElement.parentElement.parentElement.querySelector('.fa-align-justify').classList.remove('style-hidden');
        //     document.getElementById('pinned').appendChild(this.parentElement.parentElement.parentElement);
        //     hideMenuAll();
        //     updateOrder();
        // }
        // var moveToTop = function(evt) {
        //     evt = evt || window.event;
        //     this.parentElement.parentElement.parentElement.querySelector('.fa-thumbtack').classList.remove('fa-disabled');
        //     this.parentElement.parentElement.parentElement.querySelector('.fa-align-justify').classList.remove('style-hidden');
        //     var list = document.getElementById('pinned');
        //     list.insertBefore(this.parentElement.parentElement.parentElement, list.querySelector('li'));
        //     hideMenuAll();
        //     updateOrder();
        // }
        // document.getElementById('pin-circle-panel').onclick = hideMenuAll;
        for(var i = 0; i < circles.length; i++){
            circles[i].querySelector('.fa-thumbtack').onclick = pinEvent;
            var cog = circles[i].querySelector('.fa-cog');
            if(cog){
                cog.onclick = editMenu;
            }
            
            // var nodes = circles[i].querySelector('.pin-circle-dropdown-content').querySelectorAll('.pin-circle-dropdown-element');
            // for(var j = 0; j < nodes.length; j++){
            //     nodes[j].onclick = hideMenuAll;
            // }
            // var editLink = circles[i].querySelector('.ajax-url');
            // if(editLink){
            //     editLink.onclick = editMenu;
            // }
            // var toTopLink = circles[i].querySelector('.move-top');
            // if(toTopLink){
            //     toTopLink.onclick = moveToTop;
            // } 
            // var toBottomLink = circles[i].querySelector('.move-bottom');
            // if(toBottomLink){
            //     toBottomLink.onclick = moveToBottom;
            // } 
        } 
    }

    function bindSelect2Members($this) {
        var $select2elem = $this.find(".ajax_add_select2_members");
        var url = $select2elem.attr('data-url');

        //noinspection JSUnusedLocalSymbols
        $select2elem.select2({
            'val': null,
            multiple: true,
            minimumInputLength: 1,
            placeholder: cake.message.notice.b,
            ajax: {
                url: url ? url : cake.url.a,
                dataType: 'json',
                quietMillis: 100,
                cache: true,
                data: function (term, page) {
                    return {
                        term: term, //search term
                        page_limit: 10 // page size
                    };
                },
                results: function (data, page) {
                    return {results: data.results};
                }
            },
            formatSelection: format,
            formatResult: format,
            escapeMarkup: function (m) {
                return m;
            }
        })
            .on('change', function () {
                var $this = $(this);
                // グループを選択した場合
                // グループに所属するユーザーを展開して入力済にする
                $this.select2('data', select2ExpandGroup($this.select2('data')));
            });
    }

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