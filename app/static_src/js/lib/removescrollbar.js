$(function () {
	const psList = new PerfectScrollbar('#dashboard-circle-list-body-element', {
	  wheelSpeed: 1,
	  wheelPropagation: false,
	  suppressScrollY: false,
	  suppressScrollX: true,
	  maxScrollbarLength: 0,
	  minScrollbarLength: 0,
	  swipeEasing: true,
	});
	const psHamburger = new PerfectScrollbar('#circle-list-in-hamburger-element', {
	  wheelSpeed: 1,
	  wheelPropagation: false,
	  suppressScrollY: false,
	  suppressScrollX: true,
	  maxScrollbarLength: 0,
	  minScrollbarLength: 0,
	  swipeEasing: true,
	});
	const psNavSearchResults = new PerfectScrollbar('#NavSearchResults', {
	  wheelSpeed: 1,
	  wheelPropagation: false,
	  suppressScrollY: false,
	  suppressScrollX: true,
	  maxScrollbarLength: 0,
	  minScrollbarLength: 0,
	  swipeEasing: true,
	});
	const psNavSearchResultsToggle = new PerfectScrollbar('#NavSearchResultsToggle', {
	  wheelSpeed: 1,
	  wheelPropagation: false,
	  suppressScrollY: false,
	  suppressScrollX: true,
	  maxScrollbarLength: 0,
	  minScrollbarLength: 0,
	  swipeEasing: true,
	});
	$(window).on('resize', function() {
		psList.update();
		psHamburger.update();
		psNavSearchResults.update();
		psNavSearchResultsToggle.update();
	});
});