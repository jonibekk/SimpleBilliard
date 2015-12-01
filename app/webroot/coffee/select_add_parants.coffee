selectAddParants = ->
  selectElm = document.querySelectorAll('select')
  selectParentElm = $(selectElm).parent()
  selectParentElm.addClass 'js-select-parants'
  console.log selectParentElm.attr 'class'
selectAddParants()
