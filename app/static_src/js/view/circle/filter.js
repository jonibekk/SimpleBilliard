(function() {
    'strict'
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
})();