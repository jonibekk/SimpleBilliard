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
	const psHamburger = new PerfectScrollbar('#NavbarOffcanvas', {
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
	});
});