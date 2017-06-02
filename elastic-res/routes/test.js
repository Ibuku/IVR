
 var x = {
 	3: ["one", "two"], 
 	5: ["three"],
 	6: ["seven", "eight", "nine"]
 }

 var array = x.map(myObj, function(value, index) {
    return [value];
});

var arr = [];
for (var i = 0; i < x.length; i++){
    arr[i] = todayCDRGroupBy[i].length
}
console.log(arr);