sum = (array) ->
  result = 0
  for i in array
    result += i
  result
array = new Array(1,2,3,4,5)
console.log "result=", sum(array)
