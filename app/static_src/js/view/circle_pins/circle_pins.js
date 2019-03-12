"use strict"

function resizeLabels() {
    var target = $("#pinned-body");
    if(target.length) {
        var width = $(target).width();
        var labels = $(".circle-name-label");
        var newWidth = (width - 190) + "px";
        for (var i = 0; i < labels.length; i++) {
            $(labels[i]).css("width", newWidth);
        }
    }
}
function resizeBoundary() {
    var target = $("#container");
    if(target.length) {
        var deviceWidth = (window.innerWidth > 0) ? window.innerWidth : screen.width;
        if(deviceWidth <= 640) {
            target.css("margin-left", "-5px");
            var newWidth = (deviceWidth+ 5) + "px";
            target.css("width", newWidth);
            target.css("overflow-x", "hidden");
            $("#container > footer").css("margin-left", "25px");
            $("#circles-edit-page").css("margin-left", "auto");
        } else {
            target.css("margin-left", "auto");
            target.css("width", "auto");
            $("#container > footer").css("margin-left", "auto");
            var computedMargin = ((deviceWidth - 640) / 2 ) + "px";
            $("#circles-edit-page").css("margin-left", computedMargin);
        }
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
        // When group is selected enters the users belonging to the group and sets as entered
        $this.select2('data', select2ExpandGroup($this.select2('data')));
    });
}
function updateOrder() {
    var sendData = {
        'data[_Token][key]': cake.data.csrf_token.key,
        'pin_order': makeParams(),
    };
    $.ajax({
      url: '/api/v1/circle_pins/',
      type:"POST",
      data: sendData,
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
function updateDisplayCount() {
    var pincount = document.getElementById('pinned').querySelectorAll('li').length + 1;
    var unpincount = document.getElementById('unpinned').querySelectorAll('li').length;
    document.getElementById('pinnedCount').innerHTML = '(' + pincount + ')';
    document.getElementById('unpinnedCount').innerHTML = '(' + unpincount + ')';
}
function makeParams() {
    var data = [];
    var pins = document.getElementById('pinned').getElementsByClassName('pin-circle-list-item');
    for (var i = 0; i < pins.length; i++) {
        data.push(pins[i].id);
    }
    return data.join(',');
}
function editMenu(evt) {
    evt = evt || window.event;
    evt.preventDefault();
    var self = this.parentElement;

    var modal_elm = $('<div class="modal on fade" tabindex="-1"></div>');
    modal_elm.on('hidden.bs.modal', function (e) {
        e.target.remove();
        document.getElementById('circles-edit-page').classList.remove('modal-open');
    });
    var url = self.getAttribute('data-url');
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
            modal_elm.find('.fileinput_small').fileinput().on('change.bs.fileinput', function (e) {
                var me = $(e.currentTarget);
                me.children('.nailthumb-container').nailthumb({
                    width: 96,
                    height: 96,
                    fitDirection: 'center center'
                });
                //EXIF
                exifRotate(me);
            });
            modal_elm.modal();
        }).done(function (data) {
            document.getElementById('circles-edit-page').classList.add('modal-open');
        }).fail(function () {
            new Noty({
                type: 'error',
                text: cake.message.notice.d,
            }).show();
        });
    }
}
function pin(target) {
    target.parentElement.querySelector('.fa-align-justify').classList.toggle('style-hidden');
    $(target).one('click', pinEvent);
    if($('#dashboard-unpinned').length){
        var moveElement = $('#dashboard-unpinned').find('[circle_id='+target.parentElement.id+']').get(0);
        if(moveElement){
            document.getElementById('dashboard-pinned').appendChild(moveElement);
        }
    }
    updateOrder();
    updateDisplayCount();
}
function unpin(target) {
    target.parentElement.querySelector('.fa-align-justify').classList.toggle('style-hidden');
    $(target).one('click', pinEvent);
    if($('#dashboard-unpinned').length){
        var moveElement = $('#dashboard-pinned').find('[circle_id='+target.parentElement.id+']').get(0);
        if(moveElement){
            document.getElementById('dashboard-unpinned').appendChild(moveElement);
        }
    }
    updateOrder();
    updateDisplayCount();
}
function pinEvent() {
    var self = this;
    self.classList.toggle('fa-disabled');
    
    var parent = self.parentElement;
    if(self.classList.contains('fa-disabled')) {
        window.setTimeout(function() {
            $(parent).appendTo($("#unpinned"));
            unpin(self);
        }, 500);
    } else {
        window.setTimeout(function() {
            $(parent).appendTo($("#pinned"));
            pin(self);
        }, 500);
    }
};
function bindPinEvent(target) {
    $(target).one('click', pinEvent);
}
jQuery.fn.insertAt = function(index, element) {
  var lastIndex = this.children().length;
  if (index < 0) {
    index = Math.max(0, lastIndex + 1 + index);
  }
  this.append(element);
  if (index < lastIndex) {
    this.children().eq(index).before(this.children().last());
  }
  return this;
}
function setEvents() {
    var circles = document.querySelectorAll('.pin-circle-list-item');
    if(circles){   
        for(var i = 0; i < circles.length; i++){
            bindPinEvent($(circles[i]).find('.fa-thumb-tack'));
        }
    }
}
function initialize() {
    //Reorder
    if(document.getElementById('pinned') && document.getElementById('unpinned')) {
        var pinnedSortable = new Sortable(document.getElementById('pinned'), {
            // group: "circles-list",  // or { name: "...", pull: [true, false, clone], put: [true, false, array] }
            sort: true,  // sorting inside list
            delay: 0, // time in milliseconds to define when the sorting should start
            touchStartThreshold: 0, // px, how many pixels the point should move before cancelling a delayed drag event
            disabled: false, // Disables the sortable if set to true.
            store: null,  // @see Store
            animation: 150,  // ms, animation speed moving items when sorting, `0` — without animation
            handle: ".fa-align-justify",  // Drag handle selector within list items
            filter: ".ignore-elements",  // Selectors that do not lead to dragging (String or Function)
            preventOnFilter: true, // Call `event.preventDefault()` when triggered `filter`
            draggable: "li",  // Specifies which items inside the element should be draggable
            ghostClass: "sortable-ghost",  // Class name for the drop placeholder
            chosenClass: "circle-sortable-chosen",  // Class name for the chosen item
            dragClass: "sortable-drag",  // Class name for the dragging item
            dataIdAttr: 'data-id',

            forceFallback: false,  // ignore the HTML5 DnD behaviour and force the fallback to kick in

            fallbackClass: "sortable-fallback",  // Class name for the cloned DOM Element when using forceFallback
            fallbackOnBody: false,  // Appends the cloned DOM Element into the Document's Body
            fallbackTolerance: 0, // Specify in pixels how far the mouse should move before it's considered as a drag.

            scroll: true, // or HTMLElement
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
            },

            // Element is dropped into the list from another list
            onAdd: function (/**Event*/evt) {
            },

            // Changed sorting within list
            onUpdate: function (/**Event*/evt) {
            },

            // Called by any change to the list (add / update / remove)
            onSort: function (/**Event*/evt) {
                $('#dashboard-pinned').insertAt(evt.newIndex, $($('#dashboard-pinned').find('li').eq(evt.oldIndex)));
                updateOrder();
                updateDisplayCount();
            },

            // Element is removed from the list into another list
            onRemove: function (/**Event*/evt) {
            },

            // Attempt to drag a filtered element
            onFilter: function (/**Event*/evt) {
            },

            // Event when you move an item in the list or between lists
            onMove: function (/**Event*/evt, /**Event*/originalEvent) {
            },

            // Called when creating a clone of element
            onClone: function (/**Event*/evt) {
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
            },

            // Element is chosen
            onChoose: function (/**Event*/evt) {
            },

            // Element dragging started
            onStart: function (/**Event*/evt) {
            },

            // Element dragging ended
            onEnd: function (/**Event*/evt) {
            },

            // Element is dropped into the list from another list
            onAdd: function (/**Event*/evt) {
            },

            // Changed sorting within list
            onUpdate: function (/**Event*/evt) {
            },

            // Called by any change to the list (add / update / remove)
            onSort: function (/**Event*/evt) {
            },

            // Element is removed from the list into another list
            onRemove: function (/**Event*/evt) {

            },

            // Attempt to drag a filtered element
            onFilter: function (/**Event*/evt) {
            },

            // Event when you move an item in the list or between lists
            onMove: function (/**Event*/evt, /**Event*/originalEvent) {
            },

            // Called when creating a clone of element
            onClone: function (/**Event*/evt) {
            }
        });
        
        var dashboard = $(".js-dashboard-circle-list-body > ul").find('li');        
        updateDisplayCount();
    }
}

window.addEventListener('load', function() { 
    resizeLabels();
    // resizeBoundary();
    initialize();
    setEvents();
}, false);

window.addEventListener('resize', function() { 
    resizeLabels();
    // resizeBoundary();
}, false);
